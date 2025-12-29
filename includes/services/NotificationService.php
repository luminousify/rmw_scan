<?php

require_once __DIR__ . '/../DatabaseManager.php';

/**
 * NotificationService
 * Handles sending notifications via OM messenger system
 * Based on HRIS Output Messenger implementation
 */
class NotificationService
{
    private $dbManager;
    private $apiEndpoint;
    private $apiKey;
    private $isEnabled;
    
    public function __construct()
    {
        // Set timezone to match application configuration
        date_default_timezone_set('Asia/Bangkok');
        
        $this->dbManager = DatabaseManager::getInstance();
        
        // Configuration from environment
        $this->isEnabled = (defined('APP_ENV') && APP_ENV === 'production') && 
                          ($_ENV['NOTIFICATIONS_ENABLED'] ?? 'true') === 'true';
        
        $this->apiEndpoint = $_ENV['OM_MESSENGER_API_URL'] ?? null;
        $this->apiKey = $_ENV['OM_MESSENGER_API_KEY'] ?? null;
    }
    
    /**
     * Send notification via OM messenger
     * 
     * @param string $targetUsername Target OM username
     * @param string $message Message content (HTML supported)
     * @param array $options Additional options
     * @return array Response with success status and details
     */
    public function sendMessage($targetUsername, $message, $options = [])
    {
        // Skip if notifications are disabled or not in production
        if (!$this->isEnabled) {
            return [
                'success' => true,
                'message' => 'Notifications disabled - message would be sent',
                'logged' => false
            ];
        }
        
        // Default options
        $defaults = [
            'from' => 'admin',
            'color' => 'C7EDFC',
            'otr' => '0',
            'notify' => '1',
            'request_id' => null,
            'notification_type' => 'other'
        ];
        
        $options = array_merge($defaults, $options);
        
        // Log the notification attempt
        $logId = $this->logNotification($options['request_id'], $options['notification_type'], $targetUsername, $message);
        
        try {
            // Prepare cURL request
            $ch = curl_init();
            
            // URL encode parameters
            $messageEscaped = curl_escape($ch, $message);
            $targetEscaped = curl_escape($ch, $targetUsername);
            
            // Build API URL
            $url = $this->apiEndpoint . '?' . http_build_query([
                'from' => $options['from'],
                'to' => $targetUsername,
                'color' => $options['color'],
                'otr' => $options['otr'],
                'notify' => $options['notify'],
                'message' => $messageEscaped
            ]);
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            // Set headers
            $headers = [
                "API-KEY: " . $this->apiKey,
                "Content-Type: application/json",
                "Content-Length: 0"
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            // SSL configuration
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            // Handle response
            if ($response === false || !empty($error)) {
                $errorMessage = "cURL Error: " . $error;
                $this->updateNotificationLog($logId, 'failed', $errorMessage, null);
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'logged' => true,
                    'log_id' => $logId
                ];
            }
            
            // Update log with successful response
            $this->updateNotificationLog($logId, 'sent', null, $response);
            
            return [
                'success' => true,
                'message' => 'Notification sent successfully',
                'response' => $response,
                'http_code' => $httpCode,
                'logged' => true,
                'log_id' => $logId
            ];
            
        } catch (Exception $e) {
            $errorMessage = "Exception: " . $e->getMessage();
            $this->updateNotificationLog($logId, 'failed', $errorMessage, null);
            
            return [
                'success' => false,
                'message' => $errorMessage,
                'logged' => true,
                'log_id' => $logId
            ];
        }
    }
    
    /**
     * Send material request creation notification to ALL relevant RMW users
     * 
     * @param int $requestId Material request ID
     * @param array $requestDetails Request details
     * @param string|null $productionDivision Production user's division for targeted notifications
     * @return array Response with comprehensive success/failure status
     */
    public function sendMaterialRequestCreatedNotification($requestId, $requestDetails, $productionDivision = null)
    {
        // Get ALL target usernames based on production division
        $targetUsernames = $this->getTargetUsernamesForNotifications($productionDivision);
        
        if (empty($targetUsernames)) {
            return [
                'success' => false,
                'message' => 'No target usernames configured for notifications',
                'sent_count' => 0,
                'total_count' => 0
            ];
        }
        
        // Format message once
        $message = $this->formatMaterialRequestMessage($requestDetails);
        
        $results = [];
        $successCount = 0;
        $failureCount = 0;
        
        // Send to each OM username
        foreach ($targetUsernames as $targetUsername) {
            $options = [
                'request_id' => $requestId,
                'notification_type' => 'material_request_created',
                'color' => '90EE90' // Light green for new requests
            ];
            
            $result = $this->sendMessage($targetUsername, $message, $options);
            $results[] = [
                'username' => $targetUsername,
                'success' => $result['success'],
                'message' => $result['message']
            ];
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }
        
        return [
            'success' => $successCount > 0,
            'message' => "Notifications sent: {$successCount} successful, {$failureCount} failed",
            'sent_count' => $successCount,
            'failed_count' => $failureCount,
            'total_count' => count($targetUsernames),
            'details' => $results
        ];
    }
    
    /**
     * Get all production usernames for notifications in a division
     * Looks up ALL production users in the specified division
     * 
     * @param string|null $division Division to filter production users
     * @return array Array of target OM usernames
     */
    private function getProductionUsernamesForDivision($division = null)
    {
        try {
            // If division is specified, find ALL production users in that division
            if ($division) {
                $stmt = $this->dbManager->query("
                    SELECT DISTINCT om_username 
                    FROM users 
                    WHERE department = 'production' 
                      AND division = ? 
                      AND om_username IS NOT NULL 
                      AND om_username != ''
                      AND is_active = 1
                    ORDER BY username
                ", [$division]);
                
                $results = $stmt->fetchAll();
                $usernames = array_column($results, 'om_username');
                
                if (!empty($usernames)) {
                    error_log("Found " . count($usernames) . " OM usernames for production users in division '{$division}': " . implode(', ', $usernames));
                    return $usernames;
                }
                
                error_log("No active production users with OM username found in division '{$division}'");
            }
            
            // Fallback: Get all active production users with OM usernames
            $stmt = $this->dbManager->query("
                SELECT DISTINCT om_username 
                FROM users 
                WHERE department = 'production' 
                  AND om_username IS NOT NULL 
                  AND om_username != ''
                  AND is_active = 1
                ORDER BY username
            ");
            
            $results = $stmt->fetchAll();
            $usernames = array_column($results, 'om_username');
            
            if (!empty($usernames)) {
                error_log("Using fallback OM usernames from all production users: " . implode(', ', $usernames));
                return $usernames;
            }
            
            error_log("No active production users with OM usernames found in system");
            return [];
            
        } catch (Exception $e) {
            error_log("Error getting production notification targets: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all target usernames for notifications
     * Looks up ALL RMW users in the same division as the production user
     * Falls back to all active RMW users if no division match found
     * 
     * @param string|null $productionDivision Production user's division
     * @return array Array of target OM usernames
     */
    private function getTargetUsernamesForNotifications($productionDivision = null)
    {
        try {
            // If production division is specified, find ALL RMW users in same division
            if ($productionDivision) {
                $stmt = $this->dbManager->query("
                    SELECT DISTINCT om_username 
                    FROM users 
                    WHERE department = 'rmw' 
                      AND division = ? 
                      AND om_username IS NOT NULL 
                      AND om_username != ''
                      AND is_active = 1
                    ORDER BY username
                ", [$productionDivision]);
                
                $results = $stmt->fetchAll();
                $usernames = array_column($results, 'om_username');
                
                if (!empty($usernames)) {
                    error_log("Found " . count($usernames) . " OM usernames for RMW users in division '{$productionDivision}': " . implode(', ', $usernames));
                    return $usernames;
                }
                
                error_log("No active RMW users with OM username found in division '{$productionDivision}'");
            }
            
            // Fallback: Get all active RMW users with OM usernames
            $stmt = $this->dbManager->query("
                SELECT DISTINCT om_username 
                FROM users 
                WHERE department = 'rmw' 
                  AND om_username IS NOT NULL 
                  AND om_username != ''
                  AND is_active = 1
                ORDER BY username
            ");
            
            $results = $stmt->fetchAll();
            $usernames = array_column($results, 'om_username');
            
            if (!empty($usernames)) {
                error_log("Using fallback OM usernames from all RMW users: " . implode(', ', $usernames));
                return $usernames;
            }
            
            error_log("No active RMW users with OM usernames found in system");
            return [];
            
        } catch (Exception $e) {
            error_log("Error getting notification targets: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Format material request message
     * 
     * @param array $requestDetails Request details
     * @return string Formatted message using HRIS-style simple HTML for OM messenger compatibility
     */
    private function formatMaterialRequestMessage($requestDetails)
    {
        $requestNumber = htmlspecialchars($requestDetails['request_number'] ?? 'Unknown');
        $createdBy = htmlspecialchars($requestDetails['created_by'] ?? 'Unknown');
        $itemCount = $requestDetails['items_count'] ?? 0;
        $notes = htmlspecialchars($requestDetails['notes'] ?? '');
        
        // Fixed URL construction for OM messenger compatibility
        $productionUrl = $_ENV['RMW_PRODUCTION_URL'] ?? 'http://36.92.174.141:75/rmw_scan';
        
        // Create direct request-specific URLs (shortened for better compatibility)
        $requestScannerUrl = $productionUrl . '/app/controllers/scanner.php?request_number=' . $requestNumber;
        $dashboardUrl = $productionUrl . '/app/controllers/rmw_dashboard.php';
        
        // Build simple text-based message following HRIS working pattern
        $message = "📋 MATERIAL REQUEST CREATED

Request: {$requestNumber}
From: {$createdBy}
Items: {$itemCount} materials";
        
        // Add notes if present
        if (!empty($notes)) {
            $message .= "
Notes: {$notes}";
        }
        
        // Add clickable links using HRIS proven format
        $message .= "

Silahkan buka request material pada tautan berikut:
<a href='{$requestScannerUrl}'>View Request</a>

Atau buka dashboard RMW:
<a href='{$dashboardUrl}'>RMW Dashboard</a>

Request Number: {$requestNumber}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Sent: " . date('g:i A') . " • RMW Material System";
        
        return $message;
    }
    
    /**
     * Log notification to database
     * 
     * @param int|null $requestId Request ID
     * @param string $notificationType Notification type
     * @param string $targetUsername Target username
     * @param string $message Message content
     * @return int Log ID
     */
    private function logNotification($requestId, $notificationType, $targetUsername, $message)
    {
        try {
            $stmt = $this->dbManager->query("
                INSERT INTO notification_logs (
                    request_id, notification_type, target_username, message, status, created_at
                ) VALUES (?, ?, ?, ?, 'pending', NOW())
            ", [$requestId, $notificationType, $targetUsername, $message]);
            
            return $this->dbManager->lastInsertId();
            
        } catch (Exception $e) {
            error_log("Failed to log notification: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update notification log with result
     * 
     * @param int $logId Log ID
     * @param string $status Status (sent, failed)
     * @param string|null $errorMessage Error message
     * @param string|null $apiResponse API response
     */
    private function updateNotificationLog($logId, $status, $errorMessage = null, $apiResponse = null)
    {
        if (!$logId) {
            return;
        }
        
        try {
            $stmt = $this->dbManager->query("
                UPDATE notification_logs 
                SET status = ?, 
                    error_message = ?, 
                    api_response = ?, 
                    sent_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ", [$status, $errorMessage, $apiResponse, $logId]);
            
        } catch (Exception $e) {
            error_log("Failed to update notification log: " . $e->getMessage());
        }
    }
    
    /**
     * Send request approval notification to ALL production users in division
     * 
     * @param int $requestId Material request ID
     * @param array $requestDetails Request details
     * @param string $division Production division for targeting
     * @param string $approvedBy RMW user who approved the request
     * @return array Response with comprehensive success/failure status
     */
    public function sendRequestApprovedNotification($requestId, $requestDetails, $division, $approvedBy)
    {
        // Get ALL production usernames for the division
        $targetUsernames = $this->getProductionUsernamesForDivision($division);
        
        if (empty($targetUsernames)) {
            return [
                'success' => false,
                'message' => 'No production usernames configured for notifications in division: ' . $division,
                'sent_count' => 0,
                'total_count' => 0
            ];
        }
        
        // Format approval message
        $message = $this->formatApprovalMessage($requestDetails, $approvedBy);
        
        $results = [];
        $successCount = 0;
        $failureCount = 0;
        
        // Send to each production user
        foreach ($targetUsernames as $targetUsername) {
            $options = [
                'request_id' => $requestId,
                'notification_type' => 'material_request_approved',
                'color' => 'FFD700' // Gold color for approval
            ];
            
            $result = $this->sendMessage($targetUsername, $message, $options);
            $results[] = [
                'username' => $targetUsername,
                'success' => $result['success'],
                'message' => $result['message']
            ];
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }
        
        return [
            'success' => $successCount > 0,
            'message' => "Approval notifications sent: {$successCount} successful, {$failureCount} failed",
            'sent_count' => $successCount,
            'failed_count' => $failureCount,
            'total_count' => count($targetUsernames),
            'details' => $results
        ];
    }
    
    /**
     * Send request ready notification to ALL production users in division
     * 
     * @param int $requestId Material request ID
     * @param array $requestDetails Request details
     * @param string $division Production division for targeting
     * @param string $readyBy RMW user who marked as ready
     * @return array Response with comprehensive success/failure status
     */
    public function sendRequestReadyNotification($requestId, $requestDetails, $division, $readyBy)
    {
        // Get ALL production usernames for the division
        $targetUsernames = $this->getProductionUsernamesForDivision($division);
        
        if (empty($targetUsernames)) {
            return [
                'success' => false,
                'message' => 'No production usernames configured for notifications in division: ' . $division,
                'sent_count' => 0,
                'total_count' => 0
            ];
        }
        
        // Format ready message
        $message = $this->formatReadyMessage($requestDetails, $readyBy);
        
        $results = [];
        $successCount = 0;
        $failureCount = 0;
        
        // Send to each production user
        foreach ($targetUsernames as $targetUsername) {
            $options = [
                'request_id' => $requestId,
                'notification_type' => 'material_request_ready',
                'color' => '90EE90' // Light green for ready status
            ];
            
            $result = $this->sendMessage($targetUsername, $message, $options);
            $results[] = [
                'username' => $targetUsername,
                'success' => $result['success'],
                'message' => $result['message']
            ];
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }
        
        return [
            'success' => $successCount > 0,
            'message' => "Ready notifications sent: {$successCount} successful, {$failureCount} failed",
            'sent_count' => $successCount,
            'failed_count' => $failureCount,
            'total_count' => count($targetUsernames),
            'details' => $results
        ];
    }
    
    /**
     * Format request approval message
     * 
     * @param array $requestDetails Request details
     * @param string $approvedBy RMW user who approved
     * @return string Formatted approval message
     */
    private function formatApprovalMessage($requestDetails, $approvedBy)
    {
        $requestNumber = htmlspecialchars($requestDetails['request_number'] ?? 'Unknown');
        $approvedByName = htmlspecialchars($approvedBy ?? 'RMW Staff');
        $productionDivision = htmlspecialchars($requestDetails['production_division'] ?? 'Unknown');
        
        // Fixed URL construction for OM messenger compatibility
        $productionUrl = $_ENV['RMW_PRODUCTION_URL'] ?? 'http://36.92.174.141:75/rmw_scan';
        $requestScannerUrl = $productionUrl . '/app/controllers/scanner.php?request_number=' . $requestNumber;
        $dashboardUrl = $productionUrl . '/app/controllers/production_dashboard.php';
        
        // Build Indonesian approval message
        $message = "✅ PERMINTAAN MATERIAL DISETUJUI

Request: {$requestNumber}
Disetujui oleh: {$approvedByName}
Divisi: {$productionDivision}

Permintaan material Anda telah disetujui oleh departemen RMW.

Silakan buka request material pada tautan berikut:
<a href='{$requestScannerUrl}'>Lihat Request</a>

Atau buka dashboard Produksi:
<a href='{$dashboardUrl}'>Dashboard Produksi</a>

Request Number: {$requestNumber}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Sent: " . date('g:i A') . " • RMW Material System";
        
        return $message;
    }
    
    /**
     * Format request ready message
     * 
     * @param array $requestDetails Request details
     * @param string $readyBy RMW user who marked as ready
     * @return string Formatted ready message
     */
    private function formatReadyMessage($requestDetails, $readyBy)
    {
        $requestNumber = htmlspecialchars($requestDetails['request_number'] ?? 'Unknown');
        $readyByName = htmlspecialchars($readyBy ?? 'RMW Staff');
        $productionDivision = htmlspecialchars($requestDetails['production_division'] ?? 'Unknown');
        
        // Fixed URL construction for OM messenger compatibility
        $productionUrl = $_ENV['RMW_PRODUCTION_URL'] ?? 'http://36.92.174.141:75/rmw_scan';
        $requestScannerUrl = $productionUrl . '/app/controllers/scanner.php?request_number=' . $requestNumber;
        $dashboardUrl = $productionUrl . '/app/controllers/production_dashboard.php';
        
        // Build Indonesian ready message
        $message = "🎯 MATERIAL SIAP DIAMBIL

Request: {$requestNumber}
Disiapkan oleh: {$readyByName}
Divisi: {$productionDivision}

Material permintaan Anda sudah SIAP untuk diambil/koleksi.

Silakan buka request material pada tautan berikut:
<a href='{$requestScannerUrl}'>Lihat Request</a>

Atau buka dashboard Produksi:
<a href='{$dashboardUrl}'>Dashboard Produksi</a>

Request Number: {$requestNumber}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Sent: " . date('g:i A') . " • RMW Material System";
        
        return $message;
    }
    
    /**
     * Get notification statistics
     * 
     * @param array $filters Optional filters
     * @return array Statistics
     */
    public function getNotificationStats($filters = [])
    {
        $whereConditions = [];
        $params = [];
        
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "created_at >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereConditions[] = "created_at <= ?";
            $params[] = $filters['date_to'];
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                COUNT(DISTINCT target_username) as unique_users,
                COUNT(DISTINCT request_id) as requests_notified
            FROM notification_logs 
            {$whereClause}
        ";
        
        $stmt = $this->dbManager->query($query, $params);
        return $stmt->fetch();
    }
}

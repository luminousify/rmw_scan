<?php
require_once '../../config.php';
require_once '../../includes/services/MaterialRequestService.php';
require_once '../../includes/services/NotificationService.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url());
    exit();
}

// SECURITY CHECK: Only Production users can create material requests
if (!isset($_SESSION['department']) || $_SESSION['department'] !== 'production') {
    // Log unauthorized access attempt
    error_log("SECURITY: Non-production user attempted to access material request creation. User: " . 
              ($_SESSION['user'] ?? 'unknown') . 
              " Department: " . ($_SESSION['department'] ?? 'unknown'));
    
    // Redirect RMW users to RMW dashboard, others to main dashboard
    if (isset($_SESSION['department']) && $_SESSION['department'] === 'rmw') {
        header('Location: ' . url('app/controllers/rmw_dashboard.php'));
    } else {
        header('Location: ' . url('app/controllers/dashboard.php'));
    }
    exit();
}

$module_name = "material_request";
$title = "Permintaan Material";
$name = $_SESSION['user'];
$idlog = $_SESSION['idlog'];
$department = $_SESSION['department'] ?? 'production';

// Get user's division
require_once '../../includes/DatabaseManager.php';
$db = DatabaseManager::getInstance();
$stmt = $db->query("SELECT division FROM users WHERE id = ?", [$idlog]);
$userDivision = $stmt->fetchColumn();

// Pagination setup
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($currentPage - 1) * $limit;

// Debug logging for pagination
error_log("=== Material Request Pagination Debug ===");
error_log("User ID: $idlog");
error_log("Current Page: $currentPage");
error_log("Limit: $limit");
error_log("Offset: $offset");

include '../common/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        include '../../includes/conn_mysql.php';
        
        if ($_POST['action'] === 'create_request') {
            // Create new material request with unique sequential numbering
            $today = date('Ymd');
            $maxRetries = 10;
            $requestNumber = null;
            
            // Generate unique request number with retry logic
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                // Get today's highest sequential number
                $stmt = $pdo->prepare("
                    SELECT request_number 
                    FROM material_requests 
                    WHERE request_number LIKE ? 
                    ORDER BY request_number DESC 
                    LIMIT 1
                ");
                $stmt->execute(["REQ-{$today}-%"]);
                $lastRequest = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($lastRequest) {
                    // Extract the sequence number from the last request
                    $lastSequence = (int)substr($lastRequest['request_number'], -4);
                    $newSequence = $lastSequence + 1;
                } else {
                    $newSequence = 1; // Start with 0001 for today
                }
                
                $requestNumber = 'REQ-' . $today . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
                
                // Verify this request number doesn't exist (safety check)
                $checkStmt = $pdo->prepare("SELECT id FROM material_requests WHERE request_number = ?");
                $checkStmt->execute([$requestNumber]);
                
                if (!$checkStmt->fetch()) {
                    break; // Found a unique request number
                }
                
                // If we get here, there was a collision (very unlikely), retry
                if ($attempt === $maxRetries) {
                    throw new Exception("Failed to generate unique request number after $maxRetries attempts");
                }
            }
            $notes = $_POST['notes'] ?? '';
            $createdBy = $_SESSION['full_name'] ?? $_SESSION['user'];

            // START TRANSACTION: Ensure atomic insert of request + items
            // This prevents race condition where AJAX polls before items are inserted
            $pdo->beginTransaction();

            try {
                // Insert material request with server timestamp
                $requestDate = date('Y-m-d H:i:s'); // Server-side accurate timestamp
                $stmt = $pdo->prepare("
                    INSERT INTO material_requests (request_number, production_user_id, request_date, notes, status, created_by)
                    VALUES (?, ?, ?, ?, 'pending', ?)
                ");
                $stmt->execute([$requestNumber, $idlog, $requestDate, $notes, $createdBy]);
                $requestId = $pdo->lastInsertId();

                // Insert request items (consolidate duplicates)
                $consolidatedItems = [];

                if (isset($_POST['items']) && is_array($_POST['items'])) {
                    // First pass: clean and validate data
                    foreach ($_POST['items'] as $item) {
                        if (!empty($item['product_id']) && !empty($item['quantity'])) {
                            // Quantity is stored as DECIMAL(15,2). Normalize user input safely.
                            $qtyRaw = trim((string)($item['quantity'] ?? ''));
                            $qtyRaw = str_replace(',', '.', $qtyRaw);
                            if (!is_numeric($qtyRaw)) {
                                continue;
                            }
                            $qty = (float)$qtyRaw;

                            $cleanItem = [
                                'product_id' => $item['product_id'],
                                'product_name' => $item['product_name'] ?? '',
                                'quantity' => $qty,
                                'unit' => $item['unit'] ?? 'pcs',
                                'description' => $item['description'] ?? ''
                            ];

                            // Group by product_id to consolidate duplicates
                            $productId = $cleanItem['product_id'];
                            if (!isset($consolidatedItems[$productId])) {
                                $consolidatedItems[$productId] = $cleanItem;
                                $consolidatedItems[$productId]['original_count'] = 1;
                            } else {
                                // Sum quantities and merge descriptions
                                $consolidatedItems[$productId]['quantity'] += $cleanItem['quantity'];
                                $consolidatedItems[$productId]['original_count']++;

                                // Merge descriptions if they exist
                                if (!empty($cleanItem['description'])) {
                                    if (!empty($consolidatedItems[$productId]['description'])) {
                                        $consolidatedItems[$productId]['description'] .= '; ' . $cleanItem['description'];
                                    } else {
                                        $consolidatedItems[$productId]['description'] = $cleanItem['description'];
                                    }
                                }
                            }
                        }
                    }

                    // Insert consolidated items
                    $stmt = $pdo->prepare("
                        INSERT INTO material_request_items (request_id, product_id, product_name, requested_quantity, unit, description)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");

                    foreach ($consolidatedItems as $item) {
                        $stmt->execute([
                            $requestId,
                            $item['product_id'],
                            $item['product_name'],
                            number_format($item['quantity'], 2, '.', ''),
                            $item['unit'],
                            $item['description']
                        ]);
                    }
                }

                // COMMIT TRANSACTION: Only commit after both request AND items are inserted
                $pdo->commit();

            } catch (Exception $e) {
                // ROLLBACK on any error
                $pdo->rollBack();
                throw new Exception("Failed to create material request: " . $e->getMessage());
            }
            
            // Send notification to RMW users via OM messenger (after consolidation)
            try {
                $notificationService = new NotificationService();
                $productionDivision = $_SESSION['division'] ?? null; // Get production user's division
                $requestDetails = [
                    'request_number' => $requestNumber,
                    'created_by' => $createdBy,
                    'items_count' => count($consolidatedItems),
                    'notes' => $notes,
                    'priority' => $_POST['priority'] ?? 'medium'
                ];
                
                $notificationResult = $notificationService->sendMaterialRequestCreatedNotification($requestId, $requestDetails, $productionDivision);
                
                if ($notificationResult['success']) {
                    error_log("Notification sent successfully for request {$requestNumber}");
                } else {
                    error_log("Failed to send notification for request {$requestNumber}: " . $notificationResult['message']);
                }
                
            } catch (Exception $e) {
                error_log("Notification service error for request {$requestNumber}: " . $e->getMessage());
                // Don't show error to user to avoid confusing them
            }
            
            // Log activity
            $stmt = $pdo->prepare("
                INSERT INTO activity_log (user_id, action, table_name, record_id, new_values) 
                VALUES (?, 'CREATE_REQUEST', 'material_requests', ?, ?)
            ");
            $stmt->execute([$idlog, $requestId, json_encode([
                'request_number' => $requestNumber,
                'created_by' => $createdBy,
                'items_count' => count($consolidatedItems),
                'notes' => $notes
            ])]);
            
            $success_message = "Permintaan material berhasil dibuat! Nomor Permintaan: " . $requestNumber;
        }
        
        // Products are loaded via server-side autocomplete endpoint; do not embed full product lists here.
        $products = [];
        
        // Get user's requests with pagination using MaterialRequestService
        $materialRequestService = new MaterialRequestService();
        $userRequests = $materialRequestService->getUserRequests($idlog, [
            'limit' => $limit,
            'offset' => $offset
        ]);
        $totalRequests = $materialRequestService->getUserRequestsCount($idlog);
        $totalPages = ceil($totalRequests / $limit);
        
        // Debug logging for service results
        error_log("Service returned " . count($userRequests) . " requests");
        error_log("Total requests from service: $totalRequests");
        error_log("Total pages calculated: $totalPages");
        error_log("Show pagination condition: " . ($totalPages > 1 ? 'TRUE' : 'FALSE'));
        
        // Get DatabaseManager instance for items summary
        $dbManager = DatabaseManager::getInstance();
        
        
        
        // Get items summary for each request using DatabaseManager
        foreach ($userRequests as &$request) {
            $stmt = $dbManager->query("
                SELECT product_name, requested_quantity, unit 
                FROM material_request_items 
                WHERE request_id = ?
            ", [$request['id']]);
            $items = $stmt->fetchAll();
            
            $request['items_summary'] = '';
            if (!empty($items)) {
                $itemStrings = [];
                foreach ($items as $item) {
                    $itemStrings[] = $item['product_name'] . ' (' . $item['requested_quantity'] . ' ' . $item['unit'] . ')';
                }
                $request['items_summary'] = implode(', ', $itemStrings);
            }
        }
        
    } catch (Exception $e) {
        $error_message = "Kesalahan: " . $e->getMessage();
        error_log("Material Request Controller Error: " . $e->getMessage());
        // Don't reset pagination variables to 0, keep them unset to maintain existing behavior
        // $totalRequests = 0;
        // $totalPages = 0;
    }
} else {
    try {
        include '../../includes/conn_mysql.php';
        
        // Products are loaded via server-side autocomplete endpoint; do not embed full product lists here.
        $products = [];
        
        // Get user's requests with pagination using MaterialRequestService
        $materialRequestService = new MaterialRequestService();
        $userRequests = $materialRequestService->getUserRequests($idlog, [
            'limit' => $limit,
            'offset' => $offset
        ]);
        $totalRequests = $materialRequestService->getUserRequestsCount($idlog);
        $totalPages = ceil($totalRequests / $limit);
        
        // Debug logging for service results
        error_log("Service returned " . count($userRequests) . " requests");
        error_log("Total requests from service: $totalRequests");
        error_log("Total pages calculated: $totalPages");
        error_log("Show pagination condition: " . ($totalPages > 1 ? 'TRUE' : 'FALSE'));
        
        // Get DatabaseManager instance for items summary
        $dbManager = DatabaseManager::getInstance();
        
        
        
        // Get items summary for each request using DatabaseManager
        foreach ($userRequests as &$request) {
            $stmt = $dbManager->query("
                SELECT product_name, requested_quantity, unit 
                FROM material_request_items 
                WHERE request_id = ?
            ", [$request['id']]);
            $items = $stmt->fetchAll();
            
            $request['items_summary'] = '';
            if (!empty($items)) {
                $itemStrings = [];
                foreach ($items as $item) {
                    $itemStrings[] = $item['product_name'] . ' (' . $item['requested_quantity'] . ' ' . $item['unit'] . ')';
                }
                $request['items_summary'] = implode(', ', $itemStrings);
            }
        }
        
    } catch (Exception $e) {
        $error_message = "Kesalahan database: " . $e->getMessage();
        error_log("Material Request Database Error: " . $e->getMessage());
        $products = [];
        $userRequests = [];
        // Don't reset pagination variables to 0 - this breaks pagination display
        // $totalRequests = 0;
        // $totalPages = 0;
    }
}

// Ensure pagination variables are properly set before including view
if (!isset($totalRequests)) {
    $totalRequests = $materialRequestService->getUserRequestsCount($idlog) ?? 0;
    error_log("totalRequests was not set, calculated: $totalRequests");
}

if (!isset($totalPages)) {
    $totalPages = ceil($totalRequests / $limit);
    error_log("totalPages was not set, calculated: $totalPages");
}

if (!isset($userRequests)) {
    $userRequests = [];
    error_log("userRequests was not set, initialized to empty array");
}

// Final debug logging before view
error_log("Final pagination state - Total: $totalRequests, Pages: $totalPages, Current: $currentPage");

include '../material_request.php';
include '../common/footer.php';
?>

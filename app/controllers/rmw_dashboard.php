<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url());
    exit();
}

// Check if user is RMW department
if (!isset($_SESSION['department']) || $_SESSION['department'] !== 'rmw') {
    header('Location: ' . url('app/controllers/material_request.php'));
    exit();
}

$module_name = "rmw_dashboard";
$title = "RMW Dashboard";
$name = $_SESSION['user'];
$pass = $_SESSION['pass'];
$idlog = $_SESSION['idlog'];
$department = $_SESSION['department'];

// Handle AJAX request for request details
if (isset($_GET['action']) && $_GET['action'] === 'get_request_details') {
    // Start output buffering to prevent any HTML/error output contamination
    ob_start();
    
    // Clear any previous output
    if (ob_get_level()) {
        ob_clean();
    }
    
    // Set JSON response header
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('X-Content-Type-Options: nosniff');
    
    try {
        // Disable error display for this AJAX request to prevent HTML in JSON
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
        
        include '../../includes/conn_sqlite.php';
        
        $requestId = $_GET['id'] ?? null;
        
        // Validate request ID
        if (!$requestId || !is_numeric($requestId) || $requestId <= 0) {
            http_response_code(400);
            throw new Exception('Invalid request ID provided');
        }
        
        // Get user's division for access control
        $stmt = $pdo->prepare("SELECT division FROM users WHERE id = ?");
        $stmt->execute([$idlog]);
        $userDivision = $stmt->fetchColumn();

        // RMW users can only view requests from their own division
        $query = "
            SELECT mr.*,
                u.full_name as production_user_name,
                u.department as production_department,
                u.division as production_division
            FROM material_requests mr
            LEFT JOIN users u ON mr.production_user_id = u.id
            WHERE mr.id = ? AND u.division = ?
        ";
        
        $stmt = $pdo->prepare($query);
        if (!$stmt) {
            throw new Exception('Failed to prepare database query');
        }

        $stmt->execute([$requestId, $userDivision]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            http_response_code(403);
            throw new Exception('Request not found or access denied');
        }
        
        // Get request items with proper error handling
        $itemsQuery = "
            SELECT 
                mri.*,
                p.category
            FROM material_request_items mri
            LEFT JOIN products p ON mri.product_id = p.product_id
            WHERE mri.request_id = ?
            ORDER BY mri.id
        ";
        
        $stmt = $pdo->prepare($itemsQuery);
        if (!$stmt) {
            throw new Exception('Failed to prepare items query');
        }
        
        $stmt->execute([$requestId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get processing info if available with error handling
        $processedBy = null;
        if (!empty($request['processed_by'])) {
            $stmt = $pdo->prepare("SELECT full_name FROM users WHERE full_name = ? LIMIT 1");
            if ($stmt) {
                $stmt->execute([$request['processed_by']]);
                $processedBy = $stmt->fetchColumn();
            }
        }
        
        // Sanitize and format the response data
        $response = [
            'success' => true,
            'request' => [
                'id' => (int)$request['id'],
                'request_number' => htmlspecialchars($request['request_number'] ?? ''),
                'status' => htmlspecialchars($request['status'] ?? 'unknown'),
                'priority' => htmlspecialchars($request['priority'] ?? 'normal'),
                'notes' => htmlspecialchars($request['notes'] ?? ''),
                'created_by' => htmlspecialchars($request['created_by'] ?? 'System'),
                'created_at' => $request['created_at'],
                'updated_at' => $request['updated_at'],
                'processed_date' => $request['processed_date'],
                'completed_date' => $request['completed_date'],
                'customer_reference' => htmlspecialchars($request['customer_reference'] ?? ''),
                'production_user_name' => htmlspecialchars($request['production_user_name'] ?? 'Unknown'),
                'production_department' => htmlspecialchars($request['production_department'] ?? 'Unknown'),
                'production_division' => htmlspecialchars($request['production_division'] ?? 'Unassigned'),
                'processed_by' => htmlspecialchars($processedBy),
                'rmw_user_id' => (int)$request['rmw_user_id'],
                'items' => array_map(function($item) {
                    return [
                        'id' => (int)$item['id'],
                        'product_id' => htmlspecialchars($item['product_id'] ?? ''),
                        'product_name' => htmlspecialchars($item['product_name'] ?? ''),
                        'requested_quantity' => (float)$item['requested_quantity'],
                        'unit' => htmlspecialchars($item['unit'] ?? ''),
                        'description' => htmlspecialchars($item['description'] ?? ''),
                        'category' => htmlspecialchars($item['category'] ?? '')
                    ];
                }, $items)
            ]
        ];
        
        // Clear any buffered output and send clean JSON
        ob_clean();
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        ob_end_flush();
        exit();
        
    } catch (Exception $e) {
        // Clear any previous output and send error response
        ob_clean();
        
        $errorResponse = [
            'success' => false,
            'error' => $e->getMessage(),
            'error_type' => get_class($e)
        ];
        
        // Log the actual error for debugging
        error_log("RMW Dashboard AJAX Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        
        echo json_encode($errorResponse, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        ob_end_flush();
        exit();
    } catch (Error $e) {
        // Handle PHP 7+ errors
        ob_clean();
        
        $errorResponse = [
            'success' => false,
            'error' => 'A system error occurred',
            'error_type' => 'SystemError'
        ];
        
        error_log("RMW Dashboard System Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        
        echo json_encode($errorResponse, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        ob_end_flush();
        exit();
    }
}

include '../common/header.php';

// Get user's division
require_once '../../includes/DatabaseManager.php';
$db = DatabaseManager::getInstance();
$stmt = $db->query("SELECT division FROM users WHERE id = ?", [$idlog]);
$userDivision = $stmt->fetchColumn();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        include '../../includes/conn_sqlite.php';
        
        if ($_POST['action'] === 'update_status') {
            $requestId = $_POST['request_id'];
            $newStatus = $_POST['status'];
            
            // Get request details for logging
            $requestQuery = "SELECT request_number FROM material_requests WHERE id = ?";
            $stmt = $pdo->prepare($requestQuery);
            $stmt->execute([$requestId]);
            $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);
            $requestNumber = $requestDetails['request_number'];
            
            $processedBy = $_SESSION['full_name'] ?? $_SESSION['user'];
      $stmt = $pdo->prepare("
                UPDATE material_requests 
                SET status = ?, rmw_user_id = ?, processed_date = ?, processed_by = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $processedDate = ($newStatus === 'diproses') ? date('Y-m-d H:i:s') : null;
            $stmt->execute([$newStatus, $idlog, $processedDate, $processedBy, $requestId]);
            
            // Log activity
            $stmt = $pdo->prepare("
                INSERT INTO activity_log (user_id, action, table_name, record_id, new_values) 
                VALUES (?, 'UPDATE_STATUS', 'material_requests', ?, ?)
            ");
            $stmt->execute([$idlog, $requestId, json_encode([
                'status' => $newStatus,
                'processed_by' => $processedBy,
                'request_number' => $requestNumber
            ])]);
            
            $statusIndo = [
                'pending' => 'Menunggu',
                'diproses' => 'Diproses', 
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ];
            $success_message = "Status permintaan diperbarui menjadi " . ($statusIndo[$newStatus] ?? ucfirst($newStatus));
        }
        
    } catch (Exception $e) {
        $error_message = "Kesalahan: " . $e->getMessage();
    }
}

try {
    include '../../includes/conn_sqlite.php';

    // Get user's division for filtering
    $stmt = $pdo->prepare("SELECT division FROM users WHERE id = ?");
    $stmt->execute([$idlog]);
    $userDivision = $stmt->fetchColumn();

    // Get filter parameters
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';
    $divisionFilter = $_GET['division'] ?? $userDivision; // Use user's division by default

    // Build query based on filters
    $whereConditions = [];
    $params = [];

    // Division-based isolation: Only show requests from user's division
    if (!empty($divisionFilter)) {
        $whereConditions[] = "u.division = ?";
        $params[] = $divisionFilter;
    }

    if ($status !== 'all') {
        $whereConditions[] = "mr.status = ?";
        $params[] = $status;
    }

    if (!empty($search)) {
        $whereConditions[] = "(mr.request_number LIKE ? OR u.full_name LIKE ? OR mri.product_name LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get requests with details - filtered by division
    $query = "
        SELECT
            mr.*,
            u.full_name as production_user_name,
            u.division as production_division,
            COUNT(mri.id) as item_count
        FROM material_requests mr
        LEFT JOIN users u ON mr.production_user_id = u.id
        LEFT JOIN material_request_items mri ON mr.id = mri.request_id
        $whereClause
        GROUP BY mr.id
        ORDER BY mr.created_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get items summary for each request
    foreach ($requests as &$request) {
        $stmt = $pdo->prepare("
            SELECT product_name, requested_quantity, unit 
            FROM material_request_items 
            WHERE request_id = ?
        ");
        $stmt->execute([$request['id']]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $request['items_summary'] = '';
        if (!empty($items)) {
            $itemStrings = [];
            foreach ($items as $item) {
                $itemStrings[] = $item['product_name'] . ' (' . $item['requested_quantity'] . ' ' . $item['unit'] . ')';
            }
            $request['items_summary'] = implode(', ', $itemStrings);
        }
    }
    
    // Get statistics
    $statsQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM material_requests
        GROUP BY status
    ";
    $stmt = $pdo->query($statsQuery);
    $stats = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stats[$row['status']] = $row['count'];
    }
    
} catch (Exception $e) {
    $error_message = "Kesalahan database: " . $e->getMessage();
    $requests = [];
    $stats = [];
}

include '../rmw_dashboard.php';
include '../common/footer.php';
?>

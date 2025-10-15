<?php
require_once '../../config.php';
require_once '../../includes/services/MaterialRequestService.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url());
    exit();
}

$module_name = "material_request";
$title = "Material Request";
$name = $_SESSION['user'];
$pass = $_SESSION['pass'];
$idlog = $_SESSION['idlog'];
$department = $_SESSION['department'] ?? 'production';

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
        include '../../includes/conn_sqlite.php';
        
        if ($_POST['action'] === 'create_request') {
            // Create new material request
            $requestNumber = 'REQ-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $notes = $_POST['notes'] ?? '';
            $createdBy = $_SESSION['full_name'] ?? $_SESSION['user'];
            
            // Insert material request with server timestamp
            $requestDate = date('Y-m-d H:i:s'); // Server-side accurate timestamp
            $stmt = $pdo->prepare("
                INSERT INTO material_requests (request_number, production_user_id, request_date, notes, status, created_by) 
                VALUES (?, ?, ?, ?, 'pending', ?)
            ");
            $stmt->execute([$requestNumber, $idlog, $requestDate, $notes, $createdBy]);
            $requestId = $pdo->lastInsertId();
            
            // Insert request items
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO material_request_items (request_id, product_id, product_name, requested_quantity, unit, description) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                foreach ($_POST['items'] as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity'])) {
                        $stmt->execute([
                            $requestId,
                            $item['product_id'],
                            $item['product_name'],
                            intval($item['quantity']),
                            $item['unit'] ?? 'pcs',
                            $item['description'] ?? ''
                        ]);
                    }
                }
            }
            
            // Log activity
            $stmt = $pdo->prepare("
                INSERT INTO activity_log (user_id, action, table_name, record_id, new_values) 
                VALUES (?, 'CREATE_REQUEST', 'material_requests', ?, ?)
            ");
            $stmt->execute([$idlog, $requestId, json_encode([
                'request_number' => $requestNumber,
                'created_by' => $createdBy,
                'items_count' => count($_POST['items']),
                'notes' => $notes
            ])]);
            
            $success_message = "Material request created successfully! Request Number: " . $requestNumber;
        }
        
        // Get products for dropdown
        $stmt = $pdo->query("SELECT product_id, product_name, unit FROM products WHERE is_active = 1 ORDER BY product_name");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get user's requests with pagination using MaterialRequestService
        $materialRequestService = new MaterialRequestService();
        $userRequests = $materialRequestService->getUserRequests($idlog, [
            'limit' => $limit,
            'page' => $currentPage - 1
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
        $error_message = "Error: " . $e->getMessage();
        error_log("Material Request Controller Error: " . $e->getMessage());
        // Don't reset pagination variables to 0, keep them unset to maintain existing behavior
        // $totalRequests = 0;
        // $totalPages = 0;
    }
} else {
    try {
        include '../../includes/conn_sqlite.php';
        
        // Get products for dropdown
        $stmt = $pdo->query("SELECT product_id, product_name, unit FROM products WHERE is_active = 1 ORDER BY product_name");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get user's requests with pagination using MaterialRequestService
        $materialRequestService = new MaterialRequestService();
        $userRequests = $materialRequestService->getUserRequests($idlog, [
            'limit' => $limit,
            'page' => $currentPage - 1
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
        $error_message = "Database error: " . $e->getMessage();
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

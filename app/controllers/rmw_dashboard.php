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

include '../common/header.php';

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
            
            $success_message = "Request status updated to " . ucfirst($newStatus);
        }
        
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

try {
    include '../../includes/conn_sqlite.php';
    
    // Get filter parameters
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    // Build query based on filters
    $whereConditions = [];
    $params = [];
    
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
    
    // Get requests with details
    $query = "
        SELECT 
            mr.*,
            u.full_name as production_user_name,
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
    $error_message = "Database error: " . $e->getMessage();
    $requests = [];
    $stats = [];
}

include '../rmw_dashboard.php';
include '../common/footer.php';
?>

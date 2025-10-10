<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url());
    exit();
}

$module_name = "my_requests";
$title = "My Requests";
$name = $_SESSION['user'];
$pass = $_SESSION['pass'];
$idlog = $_SESSION['idlog'];
$department = $_SESSION['department'] ?? 'production';

include '../common/header.php';

try {
    include '../../includes/conn_sqlite.php';
    
    // Get filter parameters
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    // Build query based on filters
    $whereConditions = ["mr.production_user_id = ?"];
    $params = [$idlog];
    
    if ($status !== 'all') {
        $whereConditions[] = "mr.status = ?";
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $whereConditions[] = "(mr.request_number LIKE ? OR mri.product_name LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    // Get user's requests with details
    $query = "
        SELECT 
            mr.*,
            COUNT(mri.id) as item_count
        FROM material_requests mr
        LEFT JOIN material_request_items mri ON mr.id = mri.request_id
        $whereClause
        GROUP BY mr.id
        ORDER BY mr.created_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $userRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get items summary for each request
    foreach ($userRequests as &$request) {
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
    
    // Get statistics for user's requests
    $statsQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM material_requests
        WHERE production_user_id = ?
        GROUP BY status
    ";
    $stmt = $pdo->prepare($statsQuery);
    $stmt->execute([$idlog]);
    $stats = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stats[$row['status']] = $row['count'];
    }
    
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
    $userRequests = [];
    $stats = [];
}

include '../my_requests.php';
include '../common/footer.php';
?>

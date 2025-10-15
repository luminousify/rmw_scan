<?php
require_once '../../config.php';
require_once '../../includes/services/MaterialRequestService.php';

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
    $materialRequestService = new MaterialRequestService();
    
    // Pagination parameters
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 10; // Show 10 per page for better pagination testing
    $offset = ($page - 1) * $limit;
    
    // Validate and sanitize filter parameters
    $filters = MaterialRequestService::validateFilters([
        'status' => $_GET['status'] ?? 'all',
        'search' => $_GET['search'] ?? '',
        'limit' => $limit,
        'offset' => $offset
    ]);
    
    // Get user's requests with filters and pagination
    $userRequests = $materialRequestService->getUserRequests($idlog, $filters);
    
    // Get total count for pagination
    $totalRequests = $materialRequestService->getUserRequestsCount($idlog, $filters);
    $totalPages = ceil($totalRequests / $limit);
    
    // Get items summary for each request
    foreach ($userRequests as &$request) {
        $itemsQuery = "
            SELECT product_name, requested_quantity, unit 
            FROM material_request_items 
            WHERE request_id = ?
        ";
        $stmt = $pdo->prepare($itemsQuery);
        $stmt->execute([$request['id']]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $request['items_summary'] = '';
        if (!empty($items)) {
            $itemStrings = [];
            foreach ($items as $item) {
                $itemStrings[] = htmlspecialchars($item['product_name']) . ' (' . $item['requested_quantity'] . ' ' . $item['unit'] . ')';
            }
            $request['items_summary'] = implode(', ', $itemStrings);
        }
    }
    
    // Get statistics for user's requests
    $stats = $materialRequestService->getUserRequestStats($idlog);
    
} catch (Exception $e) {
    error_log("MaterialRequestService Error: " . $e->getMessage());
    $error_message = "Unable to load your requests. Please try again.";
    $userRequests = [];
    $stats = [];
}

include '../my_requests.php';
include '../common/footer.php';
?>

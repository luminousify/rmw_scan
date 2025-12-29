<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

try {
    include '../../includes/conn_mysql.php';
    
    $requestId = $_GET['id'] ?? null;
    
    if (!$requestId || !is_numeric($requestId)) {
        throw new Exception('Invalid request ID');
    }
    
    $idlog = $_SESSION['idlog'];
    
    // Get request details with security check (user can only view their own requests)
    $query = "
        SELECT mr.*, u.full_name as user_name, u.department
        FROM material_requests mr
        LEFT JOIN users u ON mr.production_user_id = u.id
        WHERE mr.id = ? AND mr.production_user_id = ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$requestId, $idlog]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        throw new Exception('Request not found or access denied');
    }
    
    // Get request items
    $itemsQuery = "
        SELECT 
            mri.id,
            mri.request_id,
            mri.product_id,
            mri.product_name,
            mri.requested_quantity,
            mri.unit,
            mri.description,
            mri.approved_quantity,
            mri.notes,
            mri.created_at,
            p.category
        FROM material_request_items mri
        LEFT JOIN products p ON mri.product_id = p.product_id
        WHERE mri.request_id = ?
        ORDER BY mri.id
    ";
    
    $stmt = $pdo->prepare($itemsQuery);
    $stmt->execute([$requestId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $response = [
        'success' => true,
        'request' => [
            'id' => $request['id'],
            'request_number' => $request['request_number'],
            'status' => $request['status'],
            'priority' => $request['priority'],
            'notes' => $request['notes'],
            'created_by' => $request['created_by'] ?? 'System',
            'created_at' => $request['created_at'],
            'updated_at' => $request['updated_at'],
            'processed_date' => $request['processed_date'],
            'completed_date' => $request['completed_date'],
            'customer_reference' => $request['customer_reference'],
            'user_name' => $request['user_name'],
            'department' => $request['department'],
            'items' => $items
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>

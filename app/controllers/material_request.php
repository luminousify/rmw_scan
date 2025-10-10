<?php
require_once '../../config.php';
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
            
            // Insert material request
            $stmt = $pdo->prepare("
                INSERT INTO material_requests (request_number, production_user_id, notes, status, created_by) 
                VALUES (?, ?, ?, 'pending', ?)
            ");
            $stmt->execute([$requestNumber, $idlog, $notes, $createdBy]);
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
        
        // Get user's requests
        $stmt = $pdo->prepare("
            SELECT mr.*, 
                   COUNT(mri.id) as item_count
            FROM material_requests mr
            LEFT JOIN material_request_items mri ON mr.id = mri.request_id
            WHERE mr.production_user_id = ?
            GROUP BY mr.id
            ORDER BY mr.created_at DESC
        ");
        $stmt->execute([$idlog]);
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
        
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
} else {
    try {
        include '../../includes/conn_sqlite.php';
        
        // Get products for dropdown
        $stmt = $pdo->query("SELECT product_id, product_name, unit FROM products WHERE is_active = 1 ORDER BY product_name");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get user's requests
        $stmt = $pdo->prepare("
            SELECT mr.*, 
                   COUNT(mri.id) as item_count
            FROM material_requests mr
            LEFT JOIN material_request_items mri ON mr.id = mri.request_id
            WHERE mr.production_user_id = ?
            GROUP BY mr.id
            ORDER BY mr.created_at DESC
        ");
        $stmt->execute([$idlog]);
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
        
    } catch (Exception $e) {
        $error_message = "Database error: " . $e->getMessage();
        $products = [];
        $userRequests = [];
    }
}

include '../material_request.php';
include '../common/footer.php';
?>

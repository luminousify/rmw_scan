<?php
/**
 * Script to add sample material request data for pagination testing
 */

// Load configuration
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/DatabaseManager.php';

// Initialize database connection
$dbManager = DatabaseManager::getInstance();
$pdo = $dbManager->getConnection();

try {
    // Get current user ID (assuming production user with ID 1)
    $stmt = $pdo->query("SELECT id FROM users WHERE department = 'production' LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $user['id'] ?? 1;
    
    // Also add requests for user ID 5 (prod) to test pagination
    $userId5 = 5;
    
    // Get available products
    $stmt = $pdo->query("SELECT product_id, product_name, unit FROM products WHERE is_active = 1");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        throw new Exception("No products found. Please add products first.");
    }
    
    $totalToAdd = 5; // Add 5 more sample requests for user ID 5
    $added = 0;
    
    for ($i = 1; $i <= $totalToAdd; $i++) {
        // Generate request number (start from 200 to avoid conflicts)
        $requestNumber = 'MR' . date('Ym') . str_pad($i + 200, 4, '0', STR_PAD_LEFT);
        
        // Check if request number already exists
        $stmt = $pdo->prepare("SELECT id FROM material_requests WHERE request_number = ?");
        $stmt->execute([$requestNumber]);
        
        if ($stmt->fetch()) {
            continue; // Skip if already exists
        }
        
        // Create material request
        $stmt = $pdo->prepare("
            INSERT INTO material_requests (
                request_number, production_user_id, request_date, notes, status
            ) VALUES (?, ?, ?, ?, ?)
        ");
        
        $statuses = ['pending', 'diproses', 'completed'];
        $status = $statuses[array_rand($statuses)];
        
        $notes = "Sample request #$i for testing pagination functionality. "
                . "This is a " . $status . " request with multiple items.";
        
        $requestDate = date('Y-m-d H:i:s', strtotime("-" . rand(1, 30) . " days"));
        
        $stmt->execute([
            $requestNumber,
            $userId5, // Use user ID 5 instead of 1
            $requestDate,
            $notes,
            $status
        ]);
        
        $requestId = $pdo->lastInsertId();
        
        // Add 1-3 items per request
        $numItems = rand(1, 3);
        $selectedProducts = array_rand($products, min($numItems, count($products)));
        
        if (!is_array($selectedProducts)) {
            $selectedProducts = [$selectedProducts];
        }
        
        foreach ($selectedProducts as $productIndex) {
            $product = $products[$productIndex];
            
            $stmt = $pdo->prepare("
                INSERT INTO material_request_items (
                    request_id, product_id, product_name, requested_quantity, 
                    unit, description, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $quantity = rand(1, 100);
            $itemStatus = $status === 'completed' ? 'completed' : 'pending';
            $description = "Request for {$product['product_name']} - Item " . ($productIndex + 1);
            
            $stmt->execute([
                $requestId,
                $product['product_id'],
                $product['product_name'],
                $quantity,
                $product['unit'],
                $description,
                $itemStatus
            ]);
        }
        
        $added++;
    }
    
    echo "Successfully added $added sample material requests!\n";
    echo "Total requests in database: ";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM material_requests");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $result['total'] . "\n";
    
    echo "\nPagination should now be visible on the material request page.\n";
    echo "Default page size is typically 10 records per page.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

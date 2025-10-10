<?php
/**
 * Database Migration Script
 * 
 * Migrates data from Microsoft Access to SQLite
 * Run this script once to migrate existing data
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/conn_sqlite.php';

echo "<h2>RMW Database Migration</h2>\n";
echo "<pre>\n";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    echo "Starting migration process...\n";
    
    // Step 1: Migrate users from Access (if accessible)
    echo "Step 1: Migrating users...\n";
    try {
        // Try to connect to Access database
        $accessFile = 'C:\laragon\www\cku_scan\FGW ALL2021_be.mdb';
        if (file_exists($accessFile)) {
            $accessDSN = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=" . $accessFile . ";";
            $accessPDO = new PDO($accessDSN, '', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            
            // Migrate users from Log_user table
            $accessStmt = $accessPDO->query("SELECT * FROM Log_user");
            $accessUsers = $accessStmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($accessUsers as $user) {
                // Map Access user to RMW user
                $department = 'production'; // Default department
                if (strpos(strtolower($user['nameLog']), 'rmw') !== false || 
                    strpos(strtolower($user['nameLog']), 'warehouse') !== false) {
                    $department = 'rmw';
                }
                
                // Insert into SQLite users table
                $sqliteStmt = $pdo->prepare("
                    INSERT OR IGNORE INTO users (username, password, department, full_name) 
                    VALUES (?, ?, ?, ?)
                ");
                $sqliteStmt->execute([
                    $user['nameLog'],
                    $user['passLog'],
                    $department,
                    $user['nameLog']
                ]);
                
                echo "  Migrated user: " . $user['nameLog'] . " (department: " . $department . ")\n";
            }
            
            echo "  Users migration completed: " . count($accessUsers) . " users migrated\n";
        } else {
            echo "  Access database not found, skipping user migration\n";
        }
    } catch (Exception $e) {
        echo "  Warning: Could not migrate users from Access: " . $e->getMessage() . "\n";
    }
    
    // Step 2: Migrate products from Access
    echo "Step 2: Migrating products...\n";
    try {
        if (isset($accessPDO)) {
            $accessStmt = $accessPDO->query("SELECT * FROM Products");
            $accessProducts = $accessStmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($accessProducts as $product) {
                $sqliteStmt = $pdo->prepare("
                    INSERT OR IGNORE INTO products (product_id, product_name, category, unit, description) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $sqliteStmt->execute([
                    $product['Product ID'],
                    $product['Product Name'],
                    'Raw Materials', // Default category
                    'pcs', // Default unit
                    $product['Product Name'] // Use product name as description
                ]);
                
                echo "  Migrated product: " . $product['Product ID'] . " - " . $product['Product Name'] . "\n";
            }
            
            echo "  Products migration completed: " . count($accessProducts) . " products migrated\n";
        } else {
            echo "  Access database not found, using default products\n";
        }
    } catch (Exception $e) {
        echo "  Warning: Could not migrate products from Access: " . $e->getMessage() . "\n";
    }
    
    // Step 3: Create sample material requests for testing
    echo "Step 3: Creating sample data...\n";
    
    // Get a production user ID
    $stmt = $pdo->query("SELECT id FROM users WHERE department = 'production' LIMIT 1");
    $productionUserId = $stmt->fetchColumn();
    
    if ($productionUserId) {
        // Create sample material request
        $stmt = $pdo->prepare("
            INSERT INTO material_requests (request_number, production_user_id, priority, notes, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $requestNumber = 'REQ-' . date('Ymd') . '-001';
        $stmt->execute([$requestNumber, $productionUserId, 'medium', 'Sample material request for testing', 'pending']);
        $requestId = $pdo->lastInsertId();
        
        // Add sample items
        $stmt = $pdo->prepare("
            INSERT INTO material_request_items (request_id, product_id, product_name, requested_quantity, unit, description) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $sampleItems = [
            [$requestId, 'MAT001', 'Steel Rod 10mm', 50, 'pcs', 'Steel rod for construction'],
            [$requestId, 'MAT002', 'Aluminum Sheet 2mm', 20, 'sheet', 'Aluminum sheet for fabrication'],
            [$requestId, 'MAT003', 'Rubber Gasket', 100, 'pcs', 'Rubber gaskets for sealing']
        ];
        
        foreach ($sampleItems as $item) {
            $stmt->execute($item);
        }
        
        echo "  Created sample request: " . $requestNumber . " with " . count($sampleItems) . " items\n";
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "\nMigration completed successfully!\n";
    echo "Database location: " . __DIR__ . "/rmw.db\n";
    echo "You can now start using the RMW system.\n";
    
} catch (Exception $e) {
    // Rollback on error
    if (isset($pdo)) {
        $pdo->rollback();
    }
    
    echo "\nMigration failed: " . $e->getMessage() . "\n";
    echo "Please check the error and try again.\n";
}

echo "</pre>\n";
?>

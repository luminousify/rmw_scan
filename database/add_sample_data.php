<?php
/**
 * Add sample data to StockDetailVer table for testing
 */

require_once dirname(__DIR__) . '/includes/DatabaseManager.php';

try {
    echo "Adding sample data to StockDetailVer table...\n";
    
    $dbManager = new DatabaseManager('sqlite');
    $pdo = $dbManager->getConnection();
    
    // Check if table exists
    if (!$dbManager->tableExists('StockDetailVer')) {
        echo "  ERROR: StockDetailVer table does not exist. Please run migration first.\n";
        exit(1);
    }
    
    // Clear existing sample data
    $pdo->exec("DELETE FROM StockDetailVer WHERE CustNoRef LIKE 'INJ/%'");
    echo "  Cleared existing sample data.\n";
    
    // Sample data
    $sampleData = [
        [
            'StockRefNo' => 'STK-001',
            'StockDate' => '2024-12-01 10:00:00',
            'Product_ID' => 'MAT001',
            'Unit' => 'pcs',
            'Customer' => 'PT. Test Customer',
            'CustNoRef' => 'INJ/FG/1887-1',
            'RecdTotal' => 10,
            'Keterangan' => 'Test material 1 - Cement 50kg'
        ],
        [
            'StockRefNo' => 'STK-002',
            'StockDate' => '2024-12-01 11:00:00',
            'Product_ID' => 'MAT002',
            'Unit' => 'pcs',
            'Customer' => 'PT. Test Customer',
            'CustNoRef' => 'INJ/FG/1887-1',
            'RecdTotal' => 5,
            'Keterangan' => 'Test material 2 - Steel Rod 10mm'
        ],
        [
            'StockRefNo' => 'STK-003',
            'StockDate' => '2024-12-01 12:00:00',
            'Product_ID' => 'MAT003',
            'Unit' => 'cans',
            'Customer' => 'PT. Test Customer',
            'CustNoRef' => 'INJ/FG/1887-1',
            'RecdTotal' => 2,
            'Keterangan' => 'Test material 3 - Paint White 5L'
        ],
        // Another customer reference with different materials
        [
            'StockRefNo' => 'STK-004',
            'StockDate' => '2024-12-02 09:00:00',
            'Product_ID' => 'MAT001',
            'Unit' => 'pcs',
            'Customer' => 'CV. Construction Co',
            'CustNoRef' => 'FG/PROD/2024-001',
            'RecdTotal' => 15,
            'Keterangan' => 'Cement 50kg - Different quantity'
        ],
        [
            'StockRefNo' => 'STK-005',
            'StockDate' => '2024-12-02 10:00:00',
            'Product_ID' => 'MAT002',
            'Unit' => 'pcs',
            'Customer' => 'CV. Construction Co',
            'CustNoRef' => 'FG/PROD/2024-001',
            'RecdTotal' => 5,
            'Keterangan' => 'Steel Bar 10mm - Different name'
        ],
        [
            'StockRefNo' => 'STK-006',
            'StockDate' => '2024-12-02 11:00:00',
            'Product_ID' => 'MAT004',
            'Unit' => 'pcs',
            'Customer' => 'CV. Construction Co',
            'CustNoRef' => 'FG/PROD/2024-001',
            'RecdTotal' => 100,
            'Keterangan' => 'Bricks Red - Extra item'
        ],
        // Third customer reference
        [
            'StockRefNo' => 'STK-007',
            'StockDate' => '2024-12-03 08:00:00',
            'Product_ID' => 'MAT001',
            'Unit' => 'pcs',
            'Customer' => 'PT. Building Supplies',
            'CustNoRef' => 'MAT/RMW/2024-42',
            'RecdTotal' => 10,
            'Keterangan' => 'Cement 50kg'
        ],
        [
            'StockRefNo' => 'STK-008',
            'StockDate' => '2024-12-03 09:00:00',
            'Product_ID' => 'MAT002',
            'Unit' => 'pcs',
            'Customer' => 'PT. Building Supplies',
            'CustNoRef' => 'MAT/RMW/2024-42',
            'RecdTotal' => 5,
            'Keterangan' => 'Steel Rod 10mm'
        ]
    ];
    
    // Insert sample data
    $stmt = $pdo->prepare("
        INSERT INTO StockDetailVer (
            StockRefNo, StockDate, Product_ID, Unit, Customer, CustNoRef, 
            RecdTotal, Keterangan, status, Verifikasi
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 0)
    ");
    
    foreach ($sampleData as $data) {
        $stmt->execute([
            $data['StockRefNo'],
            $data['StockDate'],
            $data['Product_ID'],
            $data['Unit'],
            $data['Customer'],
            $data['CustNoRef'],
            $data['RecdTotal'],
            $data['Keterangan']
        ]);
    }
    
    echo "  Inserted " . count($sampleData) . " sample records.\n";
    
    // Verify data
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM StockDetailVer");
    $stmt->execute();
    $count = $stmt->fetch()['count'];
    
    echo "  Total records in StockDetailVer: $count\n";
    
    // Show sample of the data
    $stmt = $pdo->prepare("
        SELECT CustNoRef, Customer, COUNT(*) as item_count, SUM(RecdTotal) as total_quantity
        FROM StockDetailVer 
        WHERE CustNoRef LIKE 'INJ/%' OR CustNoRef LIKE 'FG/%' OR CustNoRef LIKE 'MAT/%'
        GROUP BY CustNoRef, Customer
        ORDER BY CustNoRef
    ");
    $stmt->execute();
    
    echo "\n  Sample customer references:\n";
    while ($row = $stmt->fetch()) {
        echo "    - {$row['CustNoRef']}: {$row['Customer']} ({$row['item_count']} items, {$row['total_quantity']} total quantity)\n";
    }
    
    echo "\n✅ Sample data added successfully!\n";
    echo "\nYou can now test with these CustNoRef values:\n";
    echo "  - INJ/FG/1887-1 (PT. Test Customer - 3 items)\n";
    echo "  - FG/PROD/2024-001 (CV. Construction Co - 3 items)\n";
    echo "  - MAT/RMW/2024-42 (PT. Building Supplies - 2 items)\n";
    
} catch (Exception $e) {
    echo "  ERROR: Failed to add sample data: " . $e->getMessage() . "\n";
    exit(1);
}
?>

<?php
/**
 * Migration 003: Add missing columns to material_requests table
 * 
 * This migration adds the missing 'processed_by' and 'completed_by' columns
 * that are referenced in the rmw_dashboard.php controller but don't exist
 * in the actual database table.
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/conn_mysql.php';

echo "Starting migration 003: Add missing columns to material_requests table\n";
echo "================================================================\n\n";

try {
    // Check current table structure
    echo "1. Checking current table structure...\n";
    $query = "PRAGMA table_info(material_requests)";
    $stmt = $pdo->query($query);
    $existingColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $columnNames = array_column($existingColumns, 'name');
    echo "   Found " . count($columnNames) . " columns\n";
    
    // Define columns to add
    $columnsToAdd = [
        'processed_by' => "ALTER TABLE material_requests ADD COLUMN processed_by VARCHAR(100)",
        'completed_by' => "ALTER TABLE material_requests ADD COLUMN completed_by VARCHAR(100)"
    ];
    
    echo "\n2. Adding missing columns...\n";
    $addedColumns = 0;
    
    foreach ($columnsToAdd as $columnName => $alterSQL) {
        if (!in_array($columnName, $columnNames)) {
            echo "   Adding column: $columnName\n";
            
            try {
                $pdo->exec($alterSQL);
                echo "   ✅ Successfully added '$columnName' column\n";
                $addedColumns++;
            } catch (Exception $e) {
                echo "   ❌ Failed to add '$columnName': " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ⏭️  Column '$columnName' already exists, skipping\n";
        }
    }
    
    echo "\n3. Verifying changes...\n";
    
    // Check if columns were added successfully
    $stmt = $pdo->query("PRAGMA table_info(material_requests)");
    $newColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $newColumnNames = array_column($newColumns, 'name');
    
    $success = true;
    foreach (['processed_by', 'completed_by'] as $requiredColumn) {
        if (in_array($requiredColumn, $newColumnNames)) {
            echo "   ✅ Column '$requiredColumn' exists\n";
        } else {
            echo "   ❌ Column '$requiredColumn' is still missing\n";
            $success = false;
        }
    }
    
    // Check current data count
    $countQuery = "SELECT COUNT(*) as total FROM material_requests";
    $result = $pdo->query($countQuery);
    $total = $result->fetchColumn();
    echo "   📊 Current records in material_requests: $total\n";
    
    if ($success && $addedColumns > 0) {
        echo "\n🎉 Migration completed successfully!\n";
        echo "   Added $addedColumns missing columns\n";
        echo "   The 'processed_by' SQL error should now be resolved\n";
    } elseif ($success) {
        echo "\n✅ No columns needed to be added - table is already up to date\n";
    } else {
        echo "\n❌ Migration completed with errors\n";
        echo "   Some columns may still be missing\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "   Error occurred in: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Migration 003 completed\n";
?>

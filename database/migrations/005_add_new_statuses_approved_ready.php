<?php
/**
 * Migration: Add 'approved' and 'ready' statuses to material_requests
 * Date: 2025-01-XX
 * Description: Updates status ENUM to support new workflow:
 *   pending → approved → ready → completed
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/DatabaseManager.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========================================\n";
echo "  Migration: Add approved & ready statuses\n";
echo "========================================\n\n";

try {
    $db = DatabaseManager::getInstance();
    $pdo = $db->getConnection();
    
    // Check current status enum values
    echo "[1/4] Checking current status definition...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM material_requests WHERE Field = 'status'");
    $statusCol = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Current status type: " . $statusCol['Type'] . "\n\n";
    
    // For MySQL, we need to modify the ENUM
    echo "[2/4] Updating status ENUM...\n";
    
    // Step 1: First, add new statuses while keeping old ones
    $pdo->exec("ALTER TABLE material_requests 
                MODIFY COLUMN status ENUM('pending', 'diproses', 'approved', 'ready', 'completed', 'cancelled') 
                DEFAULT 'pending'");
    echo "  ✓ Status ENUM updated (temporary: includes diproses)\n";
    
    // Step 2: Update existing 'diproses' to 'approved'
    $pdo->exec("UPDATE material_requests SET status = 'approved' WHERE status = 'diproses'");
    $affected = $pdo->query("SELECT ROW_COUNT()")->fetchColumn();
    if ($affected > 0) {
        echo "  Updated {$affected} requests from 'diproses' to 'approved'\n";
    }
    
    // Step 3: Remove 'diproses' from ENUM (now that data is migrated)
    $pdo->exec("ALTER TABLE material_requests 
                MODIFY COLUMN status ENUM('pending', 'approved', 'ready', 'completed', 'cancelled') 
                DEFAULT 'pending'");
    echo "  ✓ Status ENUM finalized (removed diproses)\n\n";
    
    // Verify the change
    echo "[3/4] Verifying status update...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM material_requests WHERE Field = 'status'");
    $newStatusCol = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  New status type: " . $newStatusCol['Type'] . "\n\n";
    
    // Add new field for ready_date (when RMW marks as ready)
    echo "[4/4] Adding ready_date field...\n";
    try {
        $pdo->exec("ALTER TABLE material_requests 
                    ADD COLUMN ready_date DATETIME NULL AFTER processed_date");
        echo "  ✓ Added ready_date field\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  ⚠ ready_date field already exists\n";
        } else {
            throw $e;
        }
    }
    
    // Add approved_by field
    try {
        $pdo->exec("ALTER TABLE material_requests 
                    ADD COLUMN approved_by VARCHAR(100) NULL AFTER processed_by");
        echo "  ✓ Added approved_by field\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  ⚠ approved_by field already exists\n";
        } else {
            throw $e;
        }
    }
    
    // Add ready_by field
    try {
        $pdo->exec("ALTER TABLE material_requests 
                    ADD COLUMN ready_by VARCHAR(100) NULL AFTER approved_by");
        echo "  ✓ Added ready_by field\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  ⚠ ready_by field already exists\n";
        } else {
            throw $e;
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    echo "\nNew workflow:\n";
    echo "  pending → approved → ready → completed\n";
    
} catch (Exception $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>


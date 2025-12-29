<?php
/**
 * Migration: Add Status Notification Types
 * Version: 011
 * Description: Add new notification types for status updates (approved, ready)
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/DatabaseManager.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========================================\n";
echo "  Migration: Add Status Notification Types\n";
echo "========================================\n\n";

try {
    $db = DatabaseManager::getInstance();
    $pdo = $db->getConnection();
    
    // Check current notification_type enum values
    echo "[1/2] Checking current notification_type definition...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM notification_logs WHERE Field = 'notification_type'");
    $typeCol = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Current notification_type: " . $typeCol['Type'] . "\n\n";
    
    // Update notification_logs table to support new status notification types
    echo "[2/2] Adding new notification types...\n";
    
    $pdo->exec("ALTER TABLE notification_logs 
                MODIFY COLUMN notification_type ENUM(
                    'material_request_created', 
                    'material_request_approved',
                    'material_request_ready', 
                    'status_updated', 
                    'other'
                ) DEFAULT 'material_request_created'");
    
    echo "  ✓ Added new notification types (material_request_approved, material_request_ready)\n";
    
    // Add index for better performance on new notification types
    try {
        $pdo->exec("CREATE INDEX idx_notification_logs_request_type ON notification_logs(request_id, notification_type)");
        echo "  ✓ Added performance index for request_id and notification_type\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "  ⚠ Index already exists\n";
        } else {
            throw $e;
        }
    }
    
    // Verify the change
    echo "\n[Verification] Checking updated notification_type definition...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM notification_logs WHERE Field = 'notification_type'");
    $newTypeCol = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  New notification_type: " . $newTypeCol['Type'] . "\n";
    
    echo "\n✓ Migration completed successfully!\n";
    echo "\nNew notification types available:\n";
    echo "  - material_request_created (existing)\n";
    echo "  - material_request_approved (new)\n";
    echo "  - material_request_ready (new)\n";
    echo "  - status_updated (existing)\n";
    echo "  - other (existing)\n";
    
} catch (Exception $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>

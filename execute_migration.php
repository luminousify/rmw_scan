<?php
/**
 * Execute SQL migration for notification divisions
 * Run this file to create the user_notification_divisions table
 */

require_once 'config.php';

try {
    include 'includes/conn_mysql.php';

    echo "=== Migration: Add Notification Divisions Table ===\n\n";

    // Step 1: Create the table
    echo "Step 1: Creating user_notification_divisions table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS user_notification_divisions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        division VARCHAR(50) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_division (user_id, division),
        INDEX idx_user_id (user_id),
        INDEX idx_division (division)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);
    echo "✓ Table created successfully\n\n";

    // Step 2: Check if table exists
    echo "Step 2: Verifying table exists...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'user_notification_divisions'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Table 'user_notification_divisions' exists\n\n";
    } else {
        throw new Exception("Table creation failed");
    }

    // Step 3: Migrate existing data
    echo "Step 3: Migrating existing RMW user divisions...\n";
    $sql = "INSERT IGNORE INTO user_notification_divisions (user_id, division)
            SELECT id, division
            FROM users
            WHERE department = 'rmw'
              AND division IS NOT NULL
              AND division != ''";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $migratedCount = $stmt->rowCount();
    echo "✓ Migrated {$migratedCount} existing division assignments\n\n";

    // Step 4: Show current data
    echo "Step 4: Current notification divisions:\n";
    echo str_repeat("-", 80) . "\n";
    $sql = "SELECT u.id, u.username, u.full_name, u.department, u.division as current_division,
                   GROUP_CONCAT(und.division ORDER BY und.division) as notification_divisions
            FROM users u
            LEFT JOIN user_notification_divisions und ON u.id = und.user_id
            WHERE u.department = 'rmw'
            GROUP BY u.id
            ORDER BY u.username";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        echo "User ID: {$row['id']}\n";
        echo "  Username: {$row['username']}\n";
        echo "  Full Name: {$row['full_name']}\n";
        echo "  Current Division: " . ($row['current_division'] ?: 'None') . "\n";
        echo "  Notification Divisions: " . ($row['notification_divisions'] ?: 'None') . "\n";
        echo str_repeat("-", 80) . "\n";
    }

    echo "\n=== Migration Complete ===\n";
    echo "Table 'user_notification_divisions' has been created and populated.\n";
    echo "You can now assign multiple divisions to RMW users.\n\n";

    echo "Example SQL to add multiple divisions to user ID 5:\n";
    echo "INSERT IGNORE INTO user_notification_divisions (user_id, division) VALUES\n";
    echo "(5, 'Injection'),\n";
    echo "(5, 'Assembly'),\n";
    echo "(5, 'Quality Control');\n\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>

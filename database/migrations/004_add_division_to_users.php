<?php
/**
 * Migration 004: Add division column to users table
 *
 * This migration adds the 'division' column to the users table to differentiate
 * users within each department (e.g., Assembly, Quality Control, Packaging in Production)
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/DatabaseManager.php';

echo "Starting migration 004: Add division column to users table\n";
echo "============================================================\n\n";

try {
    // Use DatabaseManager for database operations
    $db = DatabaseManager::getInstance();

    // Check current table structure - try both SQLite and MySQL methods
    echo "1. Checking current users table structure...\n";

    try {
        // Try MySQL first
        $stmt = $db->query("SHOW COLUMNS FROM users");
        $existingColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($existingColumns, 'Field');
    } catch (Exception $e) {
        // If MySQL fails, try SQLite
        $stmt = $db->query("PRAGMA table_info(users)");
        $existingColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($existingColumns, 'name');
    }

    echo "   Found " . count($columnNames) . " columns\n";

    // Check if division column exists
    if (!in_array('division', $columnNames)) {
        echo "\n2. Adding division column...\n";
        echo "   Adding column: division (VARCHAR 50)\n";

        try {
            $db->query("ALTER TABLE users ADD COLUMN division VARCHAR(50)");
            echo "   ✅ Successfully added 'division' column\n";
        } catch (Exception $e) {
            echo "   ❌ Failed to add 'division' column: " . $e->getMessage() . "\n";
            throw $e;
        }
    } else {
        echo "\n2. Division column already exists, skipping addition\n";
    }

    // Add index on division column for better performance
    echo "\n3. Adding indexes for better performance...\n";
    try {
        $db->query("CREATE INDEX IF NOT EXISTS idx_users_department ON users(department)");
        echo "   ✅ Index idx_users_department created\n";
    } catch (Exception $e) {
        echo "   ⚠️  Could not create idx_users_department: " . $e->getMessage() . "\n";
    }

    try {
        $db->query("CREATE INDEX IF NOT EXISTS idx_users_division ON users(division)");
        echo "   ✅ Index idx_users_division created\n";
    } catch (Exception $e) {
        echo "   ⚠️  Could not create idx_users_division: " . $e->getMessage() . "\n";
    }

    // Update existing users with default divisions based on their department
    echo "\n4. Assigning default divisions to existing users...\n";

    // Production department divisions
    $productionDivisions = ['Assembly', 'Quality Control', 'Packaging', 'Maintenance'];
    // RMW department divisions
    $rmwDivisions = ['Receiving', 'Warehousing', 'Shipping', 'Inventory Control'];
    // Admin department divisions
    $adminDivisions = ['Management', 'IT Support', 'Finance', 'HR'];

    $updateCount = 0;

    // Get users without division
    $stmt = $db->query("SELECT id, username, department FROM users WHERE division IS NULL OR division = ''");
    $usersWithoutDivision = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "   Found " . count($usersWithoutDivision) . " users without division assigned\n";

    if (count($usersWithoutDivision) > 0) {
        // Assign divisions based on department
        foreach ($usersWithoutDivision as $user) {
            $department = $user['department'];
            $division = '';

            if ($department === 'production') {
                $division = $productionDivisions[array_rand($productionDivisions)];
            } elseif ($department === 'rmw') {
                $division = $rmwDivisions[array_rand($rmwDivisions)];
            } elseif ($department === 'admin') {
                $division = $adminDivisions[array_rand($adminDivisions)];
            }

            if (!empty($division)) {
                try {
                    $stmt = $db->prepare("UPDATE users SET division = ? WHERE id = ?");
                    $stmt->execute([$division, $user['id']]);
                    $updateCount++;
                    echo "   ✅ Updated user '{$user['username']}' to division: $division\n";
                } catch (Exception $e) {
                    echo "   ❌ Failed to update user '{$user['username']}': " . $e->getMessage() . "\n";
                }
            }
        }
    } else {
        echo "   ℹ️  All users already have divisions assigned\n";
    }

    // Verify changes
    echo "\n5. Verifying changes...\n";

    try {
        // Try MySQL first
        $stmt = $db->query("SHOW COLUMNS FROM users");
        $newColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $newColumnNames = array_column($newColumns, 'Field');
    } catch (Exception $e) {
        // If MySQL fails, try SQLite
        $stmt = $db->query("PRAGMA table_info(users)");
        $newColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $newColumnNames = array_column($newColumns, 'name');
    }

    $success = true;
    if (in_array('division', $newColumnNames)) {
        echo "   ✅ Column 'division' exists\n";
    } else {
        echo "   ❌ Column 'division' is missing\n";
        $success = false;
    }

    // Check division assignment statistics
    $countQuery = "SELECT department, division, COUNT(*) as count
                   FROM users
                   GROUP BY department, division
                   ORDER BY department, division";
    $result = $db->query($countQuery);
    $divisionStats = $result->fetchAll(PDO::FETCH_ASSOC);

    echo "   📊 User division statistics:\n";
    foreach ($divisionStats as $stat) {
        echo "      - {$stat['department']} > " . ($stat['division'] ?? 'NULL') . ": {$stat['count']} user(s)\n";
    }

    // Check for users without division
    $nullCountQuery = "SELECT COUNT(*) as count FROM users WHERE division IS NULL OR division = ''";
    $result = $db->query($nullCountQuery);
    $nullCount = $result->fetchColumn();

    if ($nullCount > 0) {
        echo "   ⚠️  Warning: $nullCount user(s) still without division\n";
    }

    if ($success) {
        echo "\n🎉 Migration completed successfully!\n";
        echo "   Updated $updateCount users with default divisions\n";
        echo "   Division column is now available for organizing users\n";
    } else {
        echo "\n❌ Migration completed with errors\n";
    }

} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "   Error occurred in: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Migration 004 completed\n";
?>

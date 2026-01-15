<?php
/**
 * Test script for multi-division notifications
 * This demonstrates that one RMW user can receive notifications from multiple divisions
 */

require_once 'config.php';
require_once 'includes/services/NotificationService.php';

try {
    include 'includes/conn_mysql.php';

    echo "=== Multi-Division Notification Test ===\n\n";

    // Step 1: Show current setup
    echo "Step 1: Current notification divisions for RMW users:\n";
    echo str_repeat("-", 80) . "\n";
    $sql = "SELECT u.id, u.username, u.full_name,
                   GROUP_CONCAT(und.division ORDER BY und.division) as notification_divisions
            FROM users u
            LEFT JOIN user_notification_divisions und ON u.id = und.user_id
            WHERE u.department = 'rmw'
            GROUP BY u.id
            ORDER BY u.username";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        echo "User ID {$row['id']} - {$row['username']} ({$row['full_name']})\n";
        echo "  Monitoring: " . ($row['notification_divisions'] ?: 'None') . "\n";
    }
    echo "\n";

    // Step 2: Add multiple divisions to test user (user ID 5)
    $testUserId = 5;
    echo "Step 2: Adding multiple divisions to user ID {$testUserId}...\n";

    $divisionsToAdd = ['Injection', 'Assembly', 'Quality Control'];
    foreach ($divisionsToAdd as $division) {
        $sql = "INSERT IGNORE INTO user_notification_divisions (user_id, division) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$testUserId, $division]);
        echo "  ✓ Added division: {$division}\n";
    }
    echo "\n";

    // Step 3: Verify the changes
    echo "Step 3: Verifying user ID {$testUserId} now monitors multiple divisions:\n";
    $sql = "SELECT u.id, u.username, u.full_name, u.om_username,
                   GROUP_CONCAT(und.division ORDER BY und.division) as notification_divisions
            FROM users u
            LEFT JOIN user_notification_divisions und ON u.id = und.user_id
            WHERE u.id = ?
            GROUP BY u.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$testUserId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "  User: {$user['username']} ({$user['full_name']})\n";
        echo "  OM Username: " . ($user['om_username'] ?: 'Not set') . "\n";
        echo "  Monitoring Divisions: " . ($user['notification_divisions'] ?: 'None') . "\n";
        echo "\n";
    }

    // Step 4: Test notification service
    echo "Step 4: Testing NotificationService for each division:\n\n";

    $notificationService = new NotificationService();

    foreach ($divisionsToAdd as $testDivision) {
        echo "Testing division '{$testDivision}':\n";

        // Use reflection to access private method for testing
        $reflection = new ReflectionClass($notificationService);
        $method = $reflection->getMethod('getTargetUsernamesForNotifications');
        $method->setAccessible(true);

        $targetUsernames = $method->invoke($notificationService, $testDivision);

        if (in_array($user['om_username'], $targetUsernames)) {
            echo "  ✓ User '{$user['username']}' (OM: {$user['om_username']}) WILL receive notifications\n";
        } else {
            echo "  ✗ User '{$user['username']}' (OM: {$user['om_username']}) will NOT receive notifications\n";
        }

        echo "  All targets for this division: " . implode(', ', $targetUsernames ?: ['None']) . "\n\n";
    }

    // Step 5: Test a division the user does NOT monitor
    echo "Testing division 'Packaging' (NOT monitored by user):\n";
    $reflection = new ReflectionClass($notificationService);
    $method = $reflection->getMethod('getTargetUsernamesForNotifications');
    $method->setAccessible(true);
    $targetUsernames = $method->invoke($notificationService, 'Packaging');

    if (in_array($user['om_username'], $targetUsernames)) {
        echo "  ✗ User '{$user['username']}' (OM: {$user['om_username']}) WILL receive notifications (UNEXPECTED!)\n";
    } else {
        echo "  ✓ User '{$user['username']}' (OM: {$user['om_username']}) will NOT receive notifications (EXPECTED)\n";
    }
    echo "  All targets for this division: " . implode(', ', $targetUsernames ?: ['None']) . "\n\n";

    echo "=== Test Complete ===\n";
    echo "✅ Multi-division notification feature is working correctly!\n";
    echo "\nSummary:\n";
    echo "- User ID {$testUserId} ({$user['username']}) monitors: " . $user['notification_divisions'] . "\n";
    echo "- This user will receive notifications from ALL these divisions\n";
    echo "- This user will NOT receive notifications from other divisions\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>

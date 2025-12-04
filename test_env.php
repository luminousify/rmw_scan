<?php
require_once 'config.php';

echo "=== Environment Detection Test ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "SERVER_ADDR: " . ($_SERVER['SERVER_ADDR'] ?? 'not set') . "\n";
echo "APP_ENV: " . APP_ENV . "\n";
echo "DB_MYSQL_HOST: " . DB_MYSQL_HOST . "\n";
echo "DB_MYSQL_NAME: " . DB_MYSQL_NAME . "\n";
echo "DB_MYSQL_USER: " . DB_MYSQL_USER . "\n";
echo "DB_TYPE: " . DB_TYPE . "\n";

// Test database connection
try {
    require_once 'includes/DatabaseManager.php';
    $db = DatabaseManager::getInstance();
    $pdo = $db->getConnection();
    echo "✅ Database connection: SUCCESS\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "👥 Users in database: " . $result['user_count'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Database connection: FAILED - " . $e->getMessage() . "\n";
}
?>

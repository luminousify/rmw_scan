<?php
/**
 * MySQL Database Connection for RMW System
 * This is a fallback when SQLite is not available
 */

// Database configuration
$host = 'localhost';
$dbname = 'rmw_system';
$username = 'root';
$password = '';

// Connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => true,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
    
    // Test connection
    $stmt = $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // If database doesn't exist, try to create it
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, $options);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        
        // Create tables from schema
        $schema = file_get_contents(__DIR__ . '/../database/schema_mysql.sql');
        $pdo->exec($schema);
        
    } catch (PDOException $e2) {
        throw new Exception("MySQL connection failed: " . $e2->getMessage());
    }
}

// Helper function for compatibility
if (!function_exists('getDBType')) {
    function getDBType() {
        return 'mysql';
    }
}
?>

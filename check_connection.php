<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Test database connection
    $conn = new PDO("mysql:host=" . DB_MYSQL_HOST . ";dbname=" . DB_MYSQL_NAME, DB_MYSQL_USER, DB_MYSQL_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo json_encode([
        'status' => 'ok',
        'message' => 'Database connection successful'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
}
?>

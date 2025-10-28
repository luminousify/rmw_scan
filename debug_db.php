<?php
require_once 'config.php';
require_once 'includes/DatabaseManager.php';

try {
    $dbManager = DatabaseManager::getInstance();
    $pdo = $dbManager->getConnection();
    
    echo "=== Database Connection Test ===\n";
    echo "Database Type: " . DB_TYPE . "\n";
    echo "SQLite Path: " . DB_SQLITE_PATH . "\n";
    echo "File exists: " . (file_exists(DB_SQLITE_PATH) ? 'YES' : 'NO') . "\n";
    echo "File size: " . (file_exists(DB_SQLITE_PATH) ? filesize(DB_SQLITE_PATH) . ' bytes' : 'N/A') . "\n\n";
    
    echo "=== Users Table Contents ===\n";
    $query = "SELECT * FROM users";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "No users found in database!\n";
    } else {
        echo "Found " . count($users) . " users:\n";
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Username: {$user['username']}, Password: {$user['password']}, Department: {$user['department']}, Active: {$user['is_active']}\n";
        }
    }
    
    echo "\n=== Testing Login Query ===\n";
    $testUser = 'prod';
    $testPass = 'prod123';
    
    $query = "SELECT * FROM users WHERE username=:user AND password=:pass AND is_active=1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user', $testUser, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $testPass, PDO::PARAM_STR);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "Login test SUCCESSFUL for prod/prod123\n";
        echo "User data: " . print_r($result, true) . "\n";
    } else {
        echo "Login test FAILED for prod/prod123\n";
        
        // Let's check if the user exists at all
        $query2 = "SELECT * FROM users WHERE username=:user";
        $stmt2 = $pdo->prepare($query2);
        $stmt2->bindParam(':user', $testUser, PDO::PARAM_STR);
        $stmt2->execute();
        $userExists = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if ($userExists) {
            echo "User 'prod' exists but login failed. User data:\n";
            echo print_r($userExists, true) . "\n";
        } else {
            echo "User 'prod' does not exist in database!\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>

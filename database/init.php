<?php
/**
 * Simple Database Initialization Script
 */

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Database file path
$dbFile = __DIR__ . '/rmw.db';

try {
    // Create SQLite connection
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Initializing RMW Database...\n";
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schema);
    
    echo "Database initialized successfully!\n";
    echo "Database location: $dbFile\n";
    echo "Test users created:\n";
    echo "  - Username: prod, Password: prod123 (Production)\n";
    echo "  - Username: prod1, Password: prod123 (Production Worker 1)\n";
    echo "  - Username: prod2, Password: prod123 (Production Worker 2)\n";
    echo "  - Username: rmw, Password: rmw123 (RMW Administrator)\n";
    echo "  - Username: rmw1, Password: rmw123 (RMW Staff 1)\n";
    echo "  - Username: rmw2, Password: rmw123 (RMW Staff 2)\n";
    echo "  - Username: admin, Password: admin123 (System Administrator)\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

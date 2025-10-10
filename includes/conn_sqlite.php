<?php
/**
 * SQLite Database Connection Configuration
 * 
 * Establishes connection to SQLite database for RMW system
 * Compatible with MySQL migration path
 */

date_default_timezone_set('Asia/Jakarta');

// Initialize logging
$logFile = __DIR__ . '/conn.log';
$logMessage = '[' . date('Y-m-d H:i:s') . '] ';

// Database configuration
$dbFile = __DIR__ . '/../database/rmw.db';
// Convert to absolute path to avoid issues
$dbFile = realpath($dbFile) ?: $dbFile;
$dsn = "sqlite:" . $dbFile;

// Log connection attempt
file_put_contents($logFile, $logMessage . "Attempting to connect to SQLite database at $dbFile\n", FILE_APPEND);

// Connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 15,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false // SQLite doesn't support prepared statement emulation
];

try {
    // Ensure database directory exists
    $dbDir = dirname($dbFile);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
        file_put_contents($logFile, $logMessage . "Created database directory: $dbDir\n", FILE_APPEND);
    }
    
    // Check if database file exists, if not create it
    if (!file_exists($dbFile)) {
        file_put_contents($logFile, $logMessage . "Database file does not exist, will be created\n", FILE_APPEND);
    }
    
    // Check file permissions
    if (file_exists($dbFile) && !is_writable($dbFile)) {
        throw new Exception("Database file is not writable: $dbFile");
    }
    
    if (!is_writable($dbDir)) {
        throw new Exception("Database directory is not writable: $dbDir");
    }
    
    // Create PDO instance
    $pdo = new PDO($dsn, '', '', $options);
    
    // Enable foreign key constraints
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    // Check if database needs initialization
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
    if (!$stmt->fetch()) {
        // Initialize database schema
        $schemaFile = __DIR__ . '/../database/schema.sql';
        if (file_exists($schemaFile)) {
            $schema = file_get_contents($schemaFile);
            $pdo->exec($schema);
            file_put_contents($logFile, $logMessage . "Database schema initialized from $schemaFile\n", FILE_APPEND);
        } else {
            throw new Exception("Schema file not found: $schemaFile");
        }
    }
    
    file_put_contents($logFile, $logMessage . "Successfully connected to SQLite database at $dbFile\n", FILE_APPEND);
    
} catch (PDOException $e) {
    $errorMsg = "SQLite PDO error: " . $e->getMessage() . " (DSN: $dsn)";
    file_put_contents($logFile, $logMessage . $errorMsg . "\n", FILE_APPEND);
    throw new Exception("Database connection failed: " . $e->getMessage());
    
} catch (Exception $e) {
    $errorMsg = "System error: " . $e->getMessage();
    file_put_contents($logFile, $logMessage . $errorMsg . "\n", FILE_APPEND);
    throw new Exception("System error: " . $e->getMessage());
}

// Helper function for MySQL compatibility
function getDBType() {
    return 'sqlite';
}

// Helper function to get table prefix (for future MySQL migration)
function getTablePrefix() {
    return '';
}

// Helper function for date formatting
function formatDBDate($date = null) {
    if ($date === null) {
        $date = new DateTime();
    }
    return $date->format('Y-m-d H:i:s');
}

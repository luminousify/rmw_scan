<?php
/**
 * Database Connection Configuration
 * 
 * Establishes connection to Microsoft Access database using PDO ODBC
 */

date_default_timezone_set('Asia/Bangkok');

// Initialize logging
$logFile = __DIR__ . '/conn.log';
$logMessage = '[' . date('Y-m-d H:i:s') . '] ';

// Database configuration
$mdbFile = 'C:\laragon\www\cku_scan\FGW ALL2021_be.mdb'; //prod
//$mdbFile = 'Z:\FGW_ALL2021_be.mdb'; //dev
//$mdbFile = 'C:\Users\User\Downloads\FGW ALL2021_be.mdb'; //local
$dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=" . $mdbFile . ";";

// Log connection attempt
file_put_contents($logFile, $logMessage . "Attempting to connect to database at $mdbFile\n", FILE_APPEND);

// Connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 15,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => true
];

try {
    // Validate database file accessibility
    if (!file_exists($mdbFile)) {
        throw new Exception("Database file not found or not accessible");
    }

    // Create PDO instance
    $pdo = new PDO($dsn, '', '', $options);
    file_put_contents($logFile, $logMessage . "Successfully connected to database\n", FILE_APPEND);
    
} catch (PDOException $e) {
    $errorMsg = "Connection failed: " . $e->getMessage();
    file_put_contents($logFile, $logMessage . $errorMsg . "\n", FILE_APPEND);
    die("Connection Error: Unable to connect to the database. Please contact system administrator. Cause: ".$e->getMessage());
    
} catch (Exception $e) {
    $errorMsg = "System error: " . $e->getMessage();
    file_put_contents($logFile, $logMessage . $errorMsg . "\n", FILE_APPEND);
    die("System Error: An unexpected error occurred. Please contact system administrator.");
}
<?php
/**
 * Unified Database Connection Wrapper
 * 
 * This file provides database connection based on DB_TYPE configuration
 * Routes to SQLite or MySQL connection as needed
 */

require_once __DIR__ . '/DatabaseManager.php';

try {
    // Get database instance and connection based on DB_TYPE
    $dbManager = DatabaseManager::getInstance();
    $pdo = $dbManager->getConnection();
    
    // Log successful connection
    $logMessage = '[' . date('Y-m-d H:i:s') . '] DatabaseManager connected successfully\n';
    file_put_contents(__DIR__ . '/conn.log', $logMessage, FILE_APPEND);
    
} catch (Exception $e) {
    // Log error
    $logMessage = '[' . date('Y-m-d H:i:s') . '] DatabaseManager connection failed: ' . $e->getMessage() . "\n";
    file_put_contents(__DIR__ . '/conn.log', $logMessage, FILE_APPEND);
    throw new Exception("Database connection failed: " . $e->getMessage());
}

// Helper function for compatibility
if (!function_exists('getDBType')) {
    function getDBType() {
        return DB_TYPE;
    }
}

// Helper function to get table prefix (for future multi-db support)
if (!function_exists('getTablePrefix')) {
    function getTablePrefix() {
        return '';
    }
}

// Helper function for date formatting
if (!function_exists('formatDBDate')) {
    function formatDBDate($date = null) {
        if ($date === null) {
            $date = new DateTime();
        }
        return $date->format('Y-m-d H:i:s');
    }
}

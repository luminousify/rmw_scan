<?php
/**
 * Legacy SQLite Database Connection Wrapper
 * 
 * This file provides backward compatibility while using the new DatabaseManager
 * for improved connection handling and retry logic.
 */

require_once __DIR__ . '/DatabaseManager.php';

try {
    // Get database instance and connection
    $dbManager = DatabaseManager::getInstance();
    $pdo = $dbManager->getConnection();
    
    // Log successful connection for backward compatibility
    $logMessage = '[' . date('Y-m-d H:i:s') . '] Successfully connected using DatabaseManager\n';
    file_put_contents(__DIR__ . '/conn.log', $logMessage, FILE_APPEND);
    
} catch (Exception $e) {
    // Log error for backward compatibility
    $logMessage = '[' . date('Y-m-d H:i:s') . '] DatabaseManager connection failed: ' . $e->getMessage() . "\n";
    file_put_contents(__DIR__ . '/conn.log', $logMessage, FILE_APPEND);
    throw new Exception("Database connection failed: " . $e->getMessage());
}

// Helper function for MySQL compatibility
if (!function_exists('getDBType')) {
    function getDBType() {
        return 'sqlite';
    }
}

// Helper function to get table prefix (for future MySQL migration)
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

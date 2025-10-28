<?php
// Dynamic path configuration
// This file handles all base URLs and paths dynamically

// Get the base URL dynamically
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    // Get the directory path from the document root
    //$scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);

    // Find the base directory (cku_scan)
    $pathParts = explode('/', $scriptPath);
    $baseIndex = array_search('rmw_scan', $pathParts);
    
    if ($baseIndex !== false) {
        $basePath = implode('/', array_slice($pathParts, 0, $baseIndex + 1));
    } else {
        // If cku_scan is not found, use the current directory
        $basePath = $scriptPath;
    }
    
    // Remove trailing slash if present
    $basePath = rtrim($basePath, '/');
    
    return $protocol . $host . $basePath;
}

// Get the base path for includes (filesystem path)
function getBasePath() {
    // Get the current file's directory
    $currentDir = str_replace('\\', '/', __DIR__);
    
    // Since config.php is in the root of cku_scan, this is our base path
    return $currentDir;
}

// Define constants for easy access
define('BASE_URL', getBaseUrl());
define('BASE_PATH', getBasePath());

// Helper function to get URL for different sections
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '');
}

// Helper function to get filesystem path
function path($path = '') {
    $path = ltrim($path, '/\\');
    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . $path : '');
}

// Common URLs
define('ASSETS_URL', url('includes'));
define('APP_URL', url('app'));
define('CONTROLLERS_URL', url('app/controllers'));

// Common paths
define('INCLUDES_PATH', path('includes'));
define('APP_PATH', path('app'));
define('APP_BAK_PATH', path('app_bak'));

// Database Configuration
define('DB_TYPE', 'mysql'); // 'sqlite' or 'mysql'
define('DB_SQLITE_PATH', path('database/rmw.db'));
define('DB_MYSQL_HOST', 'localhost');
define('DB_MYSQL_NAME', 'rmw');
define('DB_MYSQL_USER', 'root');
define('DB_MYSQL_PASS', '');

// Application Configuration
define('APP_NAME', 'RMW System');
define('APP_VERSION', '2.0.0');
define('APP_ENV', 'development'); // 'development' or 'production'
?>
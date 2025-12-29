<?php
// Dynamic path configuration
// This file handles all base URLs and paths dynamically

// --- Session isolation for rmw_scan ---
// This app is deployed on a host that also runs another PHP app using the default PHP session cookie.
// If both apps share the same session cookie name/path, they will overwrite each other's session data.
// Symptom: login succeeds but RMW dashboard logs "Missing: division" and redirects back to login.
//
// Fix: use a unique session name + restrict cookie path to /rmw_scan BEFORE any session_start().
if (function_exists('session_status') && session_status() === PHP_SESSION_NONE) {
    // Unique cookie name so we don't collide with other apps (e.g., CodeIgniter using PHPSESSID).
    @session_name('RMWSCANSESSID');

    // Restrict cookie to this app only to avoid cross-app leakage.
    $cookiePath = '/rmw_scan';
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) == 443);

    // PHP 7.2 signature: session_set_cookie_params(lifetime, path, domain, secure, httponly)
    // (SameSite not supported natively in PHP 7.2 without workarounds; leave default.)
    @session_set_cookie_params(0, $cookiePath, '', (bool)$isHttps, true);
}

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

// Load environment variables from .env file (best practice)
function loadEnv($file) {
    if (!file_exists($file)) {
        return;
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set as environment variable
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Load local configuration override if exists (fallback)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Environment Detection (fallback if not set)
$isProduction = ($_ENV['APP_ENV'] ?? 'development') === 'production' ||
               ($_SERVER['HTTP_HOST'] === '36.92.174.141') || 
               ($_SERVER['SERVER_ADDR'] === '36.92.174.141');

// Database Configuration with Environment Variables
$dbType = $_ENV['DB_TYPE'] ?? 'mysql';
$dbHost = $_ENV['DB_MYSQL_HOST'] ?? '36.92.174.141:3333';
$dbName = $_ENV['DB_MYSQL_NAME'] ?? 'rmw_system';
$dbUser = $_ENV['DB_MYSQL_USER'] ?? 'endang';
$dbPass = $_ENV['DB_MYSQL_PASS'] ?? 'endangthea0';
$appEnv = $_ENV['APP_ENV'] ?? ($isProduction ? 'production' : 'development');

// Use local overrides if available (backward compatibility)
if (defined('LOCAL_DB_MYSQL_HOST')) {
    $dbHost = LOCAL_DB_MYSQL_HOST;
}
if (defined('LOCAL_DB_MYSQL_NAME')) {
    $dbName = LOCAL_DB_MYSQL_NAME;
}
if (defined('LOCAL_DB_MYSQL_USER')) {
    $dbUser = LOCAL_DB_MYSQL_USER;
}
if (defined('LOCAL_DB_MYSQL_PASS')) {
    $dbPass = LOCAL_DB_MYSQL_PASS;
}
if (defined('LOCAL_APP_ENV')) {
    $appEnv = LOCAL_APP_ENV;
}

// Define constants
define('DB_TYPE', $dbType);
define('DB_MYSQL_HOST', $dbHost);
define('DB_MYSQL_NAME', $dbName);
define('DB_MYSQL_USER', $dbUser);
define('DB_MYSQL_PASS', $dbPass);
define('APP_ENV', $appEnv);

// Application Configuration
define('APP_NAME', 'RMW System');
define('APP_VERSION', '2.0.0');

// Scanner Configuration
define('AUTO_COMPLETE_ON_PERFECT_MATCH', true); // Enable/disable auto-completion on perfect match
define('AUTO_COMPLETE_DELAY_SECONDS', 0); // 0 = immediate, >0 = delay in seconds before auto-completing
?>
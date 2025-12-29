<?php
/**
 * Health Check Endpoint for Production Monitoring
 * 
 * Usage: http://your-domain.com/health_check.php
 * Returns: JSON with health status
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/DatabaseManager.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$health = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'environment' => defined('APP_ENV') ? APP_ENV : 'unknown',
    'checks' => []
];

// Check database connection
try {
    $db = DatabaseManager::getInstance();
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    
    $health['checks']['database'] = [
        'status' => 'pass',
        'message' => 'Database connected',
        'users_count' => $result['count']
    ];
} catch (Exception $e) {
    $health['status'] = 'unhealthy';
    $health['checks']['database'] = [
        'status' => 'fail',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ];
    http_response_code(503);
}

// Check configuration files
$requiredFiles = [
    '.env' => 'Environment configuration',
    'includes/DatabaseManager.php' => 'Database manager',
    'includes/services/NotificationService.php' => 'Notification service'
];

foreach ($requiredFiles as $file => $description) {
    $filePath = __DIR__ . '/' . $file;
    $health['checks']['config_' . basename($file)] = [
        'status' => file_exists($filePath) ? 'pass' : 'fail',
        'message' => $description . ': ' . (file_exists($filePath) ? 'found' : 'missing')
    ];
    
    if (!file_exists($filePath)) {
        $health['status'] = 'unhealthy';
    }
}

// Output health status
echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>

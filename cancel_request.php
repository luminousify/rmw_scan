<?php
require_once 'config.php';
require_once 'includes/services/MaterialRequestService.php';
require_once 'includes/utils/ResponseHelper.php';

session_start();

// Set JSON response header
ResponseHelper::setJsonHeaders();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    ResponseHelper::error('Unauthorized', 401);
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ResponseHelper::error('Method not allowed', 405);
}

// Validate CSRF token (if implemented)
// TODO: Implement CSRF protection

// Get and validate request ID
$requestId = $_POST['request_id'] ?? null;
if (!$requestId || !is_numeric($requestId) || $requestId <= 0) {
    ResponseHelper::error('Invalid request ID', 400);
}

try {
    $materialRequestService = new MaterialRequestService();
    
    // Cancel the request using the service
    $result = $materialRequestService->cancelRequest($requestId, $_SESSION['idlog']);
    
    if ($result['success']) {
        ResponseHelper::success($result['message'], $result, 200);
    } else {
        ResponseHelper::error($result['error'], 400);
    }
    
} catch (Exception $e) {
    error_log("Cancel request error: " . $e->getMessage());
    ResponseHelper::error('An unexpected error occurred. Please try again.', 500);
}
?>

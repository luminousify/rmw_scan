<?php
// Suppress all error output for API endpoint - we want clean JSON only
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/DatabaseManager.php';

// Start session and verify authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug logging
error_log("=== Dashboard Updates Request ===");
error_log("Timestamp: " . date('Y-m-d H:i:s'));
error_log("Session status: " . (isset($_SESSION['loggedin']) ? 'active' : 'inactive'));
error_log("GET params: " . json_encode($_GET));

// Check if session is valid and user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    error_log("ERROR: Session not logged in");
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Session expired or invalid',
        'requires_refresh' => true,
        'error_code' => 'SESSION_EXPIRED',
        'debug_info' => [
            'session_exists' => isset($_SESSION),
            'loggedin_set' => isset($_SESSION['loggedin']),
            'loggedin_value' => $_SESSION['loggedin'] ?? 'not set'
        ]
    ]);
    exit();
}

// Check if user is RMW department
if (!isset($_SESSION['department']) || $_SESSION['department'] !== 'rmw') {
    error_log("ERROR: Department check failed. Department: " . ($_SESSION['department'] ?? 'not set'));
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Access denied - RMW department required',
        'error_code' => 'ACCESS_DENIED',
        'debug_info' => [
            'user_department' => $_SESSION['department'] ?? 'not set',
            'required_department' => 'rmw'
        ]
    ]);
    exit();
}

// Validate user ID exists in session
if (!isset($_SESSION['idlog']) || empty($_SESSION['idlog'])) {
    error_log("ERROR: User ID not set in session");
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid user session',
        'requires_refresh' => true,
        'error_code' => 'INVALID_USER',
        'debug_info' => [
            'idlog_set' => isset($_SESSION['idlog']),
            'idlog_value' => $_SESSION['idlog'] ?? 'not set'
        ]
    ]);
    exit();
}

// Set response headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

try {
    $startTime = microtime(true);
    include dirname(dirname(__DIR__)) . '/includes/conn_mysql.php';
    
    error_log("Database connection successful");
    
    $idlog = $_SESSION['idlog'];
    $userDivision = $_SESSION['division'] ?? '';
    $lastUpdate = $_GET['last_update'] ?? null;
    $page = max(1, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]));
    $perPage = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT, ['options' => ['default' => 10]]);
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    error_log("Request parameters - page: $page, per_page: $perPage, status: $status, division: $userDivision");
    
    // Ensure valid per_page value
    if (!in_array($perPage, [5, 10, 25, 50])) {
        $perPage = 10;
    }
    
    $offset = ($page - 1) * $perPage;
    
    // Get user's division for filtering
    $stmt = $pdo->prepare("SELECT division FROM users WHERE id = ?");
    $stmt->execute([$idlog]);
    $userDivision = $stmt->fetchColumn();
    
    // Build WHERE conditions
    $whereConditions = [];
    $params = [];
    
    // Division-based filtering
    if (!empty($userDivision)) {
        $whereConditions[] = "u.division = ?";
        $params[] = $userDivision;
    }
    
    // Add timestamp filter if provided
    if ($lastUpdate && is_numeric($lastUpdate)) {
        $lastUpdateDate = date('Y-m-d H:i:s', $lastUpdate);
        $whereConditions[] = "mr.updated_at > ?";
        $params[] = $lastUpdateDate;
    }
    
    // Status filter
    if ($status !== 'all') {
        $whereConditions[] = "mr.status = ?";
        $params[] = $status;
    }
    
    // Search filter
    if (!empty($search)) {
        $whereConditions[] = "(mr.request_number LIKE ? OR u.full_name LIKE ? OR EXISTS (SELECT 1 FROM material_request_items mri_search WHERE mri_search.request_id = mr.id AND mri_search.product_name LIKE ?))";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $response = [
        'success' => true,
        'timestamp' => time(),
        'data' => [
            'statistics' => [],
            'requests' => [],
            'pagination' => [],
            'has_changes' => false
        ]
    ];
    
    // Get updated statistics
    $statsQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM material_requests mr
        LEFT JOIN users u ON mr.production_user_id = u.id
        " . (!empty($userDivision) ? "WHERE u.division = ?" : "") . "
        GROUP BY status
    ";
    
    $statsParams = !empty($userDivision) ? [$userDivision] : [];
    $stmt = $pdo->prepare($statsQuery);
    $stmt->execute($statsParams);
    $stats = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stats[$row['status']] = $row['count'];
    }
    
    $response['data']['statistics'] = [
        'pending' => $stats['pending'] ?? 0,
        'approved' => $stats['approved'] ?? 0,
        'ready' => $stats['ready'] ?? 0,
        'completed' => $stats['completed'] ?? 0,
        'cancelled' => $stats['cancelled'] ?? 0
    ];
    
    // Get updated requests
    $requestsQuery = "
        SELECT DISTINCT
            mr.*,
            u.full_name as production_user_name,
            u.division as production_division,
            COALESCE(COUNT(DISTINCT mri.id), 0) as item_count
        FROM material_requests mr
        LEFT JOIN users u ON mr.production_user_id = u.id
        LEFT JOIN material_request_items mri ON mr.id = mri.request_id
        $whereClause
        GROUP BY mr.id, u.full_name, u.division
        ORDER BY mr.updated_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $requestsParams = array_merge($params, [(int)$perPage, (int)$offset]);
    $stmt = $pdo->prepare($requestsQuery);
    $stmt->execute($requestsParams);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format requests for response
    $formattedRequests = [];
    foreach ($requests as $request) {
        $formattedRequests[] = [
            'id' => (int)$request['id'],
            'request_number' => htmlspecialchars($request['request_number']),
            'production_user_name' => htmlspecialchars($request['production_user_name'] ?? 'Unknown'),
            'production_division' => htmlspecialchars($request['production_division'] ?? 'Unassigned'),
            'created_at' => $request['created_at'],
            'updated_at' => $request['updated_at'],
            'status' => htmlspecialchars($request['status']),
            'item_count' => (int)$request['item_count'],
            'is_new' => false, // Will be determined on client side
            'updated_fields' => [] // Will be populated if specific fields changed
        ];
    }
    
    $response['data']['requests'] = $formattedRequests;
    
    // Get total count for pagination
    $countQuery = "
        SELECT COUNT(DISTINCT mr.id) as total
        FROM material_requests mr
        LEFT JOIN users u ON mr.production_user_id = u.id
        LEFT JOIN material_request_items mri ON mr.id = mri.request_id
        $whereClause
    ";
    
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $countResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalRecords = $countResult ? (int)$countResult['total'] : 0;
    $totalPages = ceil($totalRecords / $perPage);
    
    $response['data']['pagination'] = [
        'current_page' => $page,
        'per_page' => $perPage,
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'has_prev' => $page > 1,
        'has_next' => $page < $totalPages,
        'prev_page' => max(1, $page - 1),
        'next_page' => min($totalPages, $page + 1),
        'start_record' => $totalRecords > 0 ? $offset + 1 : 0,
        'end_record' => $totalRecords > 0 ? min($offset + $perPage, $totalRecords) : 0
    ];
    
    // Determine if there are changes
    $response['data']['has_changes'] = !empty($formattedRequests) || $lastUpdate === null;
    
    // Include server info for debugging
    $response['server_info'] = [
        'php_version' => PHP_VERSION,
        'current_time' => date('Y-m-d H:i:s'),
        'last_update_param' => $lastUpdate,
        'division_filter' => $userDivision,
        'query_time' => microtime(true)
    ];
    
    // Clear any output buffer (warnings, notices, whitespace)
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Clear any output buffer before sending error response
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    $errorTime = microtime(true);
    $duration = round(($errorTime - $startTime) * 1000, 2);
    
    error_log("Dashboard Updates Error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    error_log("Query duration before error: {$duration}ms");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'error_details' => $e->getMessage(),
        'error_type' => 'Exception',
        'timestamp' => time(),
        'debug_info' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'duration_ms' => $duration
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    // Clear any output buffer before sending error response
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    $errorTime = microtime(true);
    $duration = round(($errorTime - $startTime) * 1000, 2);
    
    error_log("Dashboard Updates System Error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    error_log("Query duration before error: {$duration}ms");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'System error occurred',
        'error_details' => $e->getMessage(),
        'error_type' => 'Error',
        'timestamp' => time(),
        'debug_info' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'duration_ms' => $duration
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
?>

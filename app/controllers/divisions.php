<?php
/**
 * Divisions API Controller
 *
 * Handles AJAX requests for division-related operations
 */

session_start();
require_once __DIR__ . '/../../includes/DatabaseManager.php';
require_once __DIR__ . '/../../includes/services/DivisionService.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $divisionService = new DivisionService();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'getDivisionsByDepartment':
            $department = $_GET['department'] ?? '';
            if (empty($department)) {
                echo json_encode(['success' => false, 'message' => 'Department is required']);
                exit;
            }

            $divisions = $divisionService->getDivisionsByDepartment($department);
            echo json_encode([
                'success' => true,
                'data' => $divisions
            ]);
            break;

        case 'getUsersByDivision':
            $department = $_GET['department'] ?? '';
            $division = $_GET['division'] ?? null;

            if (empty($department)) {
                echo json_encode(['success' => false, 'message' => 'Department is required']);
                exit;
            }

            $users = $divisionService->getUsersByDepartment($department, $division);
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
            break;

        case 'updateUserDivision':
            // Only allow admin and management users to update divisions
            $userDepartment = $_SESSION['department'] ?? '';
            if (!in_array($userDepartment, ['admin', 'rmw'])) {
                echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
                exit;
            }

            $userId = $_POST['user_id'] ?? 0;
            $division = $_POST['division'] ?? '';

            if (empty($userId)) {
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
                exit;
            }

            $result = $divisionService->updateUserDivision($userId, $division);
            echo json_encode($result);
            break;

        case 'getDivisionStatistics':
            $stats = $divisionService->getDivisionStatistics();
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;

        case 'getUsersGrouped':
            $groupedUsers = $divisionService->getUsersGroupedByDivision();
            echo json_encode([
                'success' => true,
                'data' => $groupedUsers
            ]);
            break;

        case 'getFilteredRequests':
            $department = $_GET['department'] ?? null;
            $division = $_GET['division'] ?? null;

            $requests = $divisionService->getFilteredRequests($department, $division);
            echo json_encode([
                'success' => true,
                'data' => $requests
            ]);
            break;

        case 'getAllDivisionOptions':
            $options = $divisionService->getAllDivisions();
            echo json_encode([
                'success' => true,
                'data' => $options
            ]);
            break;

        case 'validateDivision':
            $department = $_POST['department'] ?? '';
            $division = $_POST['division'] ?? '';

            $isValid = $divisionService->isValidDivision($department, $division);
            echo json_encode([
                'success' => true,
                'valid' => $isValid
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    error_log("Division API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>

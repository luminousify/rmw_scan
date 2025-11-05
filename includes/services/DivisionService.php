<?php
/**
 * Division Service
 *
 * Provides business logic for managing divisions within departments
 */

require_once __DIR__ . '/../DatabaseManager.php';

class DivisionService
{
    private $db;

    // Default divisions for each department
    private $defaultDivisions = [
        'production' => ['Injection', 'Assembly', 'Quality Control', 'Packaging', 'Maintenance', 'Fabrication'],
        'rmw' => ['Injection', 'Receiving', 'Warehousing', 'Shipping', 'Inventory Control', 'Quality Assurance'],
        'admin' => ['Management', 'IT Support', 'Finance', 'HR', 'General Affairs']
    ];

    public function __construct()
    {
        $this->db = DatabaseManager::getInstance();
    }

    /**
     * Get all divisions for a department
     *
     * @param string $department Department name
     * @return array Array of divisions
     */
    public function getDivisionsByDepartment($department)
    {
        $divisions = $this->db->getDivisionsByDepartment($department);

        // If no divisions exist yet, return defaults
        if (empty($divisions) && isset($this->defaultDivisions[$department])) {
            return $this->defaultDivisions[$department];
        }

        return $divisions;
    }

    /**
     * Get users by department and division
     *
     * @param string $department Department name
     * @param string|null $division Division name (optional)
     * @return array Array of users
     */
    public function getUsersByDepartment($department, $division = null)
    {
        return $this->db->getUsersByDepartment($department, $division);
    }

    /**
     * Update user's division
     *
     * @param int $userId User ID
     * @param string $division Division name
     * @return array Result with success status and message
     */
    public function updateUserDivision($userId, $division)
    {
        try {
            // Validate division is not empty
            if (empty($division)) {
                return ['success' => false, 'message' => 'Division cannot be empty'];
            }

            // Update the division
            $result = $this->db->updateUserDivision($userId, $division);

            if ($result) {
                // Log the activity
                $this->logDivisionChange($userId, null, $division);

                return [
                    'success' => true,
                    'message' => 'Division updated successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to update division'];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating division: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all users grouped by department and division
     *
     * @return array Grouped users
     */
    public function getUsersGroupedByDivision()
    {
        return $this->db->getUsersGroupedByDivision();
    }

    /**
     * Get division statistics
     *
     * @return array Statistics
     */
    public function getDivisionStatistics()
    {
        return $this->db->getDivisionStatistics();
    }

    /**
     * Get material requests with division information
     *
     * @return array Material requests
     */
    public function getMaterialRequestsWithDivision()
    {
        return $this->db->getMaterialRequestsWithDivision();
    }

    /**
     * Get filtered requests based on department and division
     *
     * @param string|null $department Department filter
     * @param string|null $division Division filter
     * @return array Filtered requests
     */
    public function getFilteredRequests($department = null, $division = null)
    {
        $requests = $this->getMaterialRequestsWithDivision();
        $filtered = [];

        foreach ($requests as $request) {
            $match = true;

            // Filter by department
            if ($department !== null) {
                if ($request['production_department'] !== $department) {
                    $match = false;
                }
            }

            // Filter by division
            if ($division !== null && $match) {
                if ($request['production_division'] !== $division) {
                    $match = false;
                }
            }

            if ($match) {
                $filtered[] = $request;
            }
        }

        return $filtered;
    }

    /**
     * Get available divisions for user selection
     *
     * @return array All divisions organized by department
     */
    public function getAllDivisions()
    {
        return $this->defaultDivisions;
    }

    /**
     * Log division change activity
     *
     * @param int $userId User ID
     * @param string|null $oldDivision Old division
     * @param string $newDivision New division
     */
    private function logDivisionChange($userId, $oldDivision, $newDivision)
    {
        try {
            // Get user details
            $stmt = $this->db->query("SELECT username, full_name FROM users WHERE id = ?", [$userId]);
            $user = $stmt->fetch();

            if ($user) {
                $action = 'division_updated';
                $details = "Division updated from " . ($oldDivision ?? 'None') . " to " . $newDivision;

                // You can implement activity logging here if needed
                // For now, we'll just log to error log
                error_log("Division Update: User {$user['username']} ({$user['full_name']}) - {$details}");
            }
        } catch (Exception $e) {
            // Silently fail logging
            error_log("Failed to log division change: " . $e->getMessage());
        }
    }

    /**
     * Validate division-department combination
     *
     * @param string $department Department name
     * @param string $division Division name
     * @return bool True if valid
     */
    public function isValidDivision($department, $division)
    {
        if (empty($division)) {
            return true; // Allow empty division (nullable)
        }

        $defaultDivisions = $this->defaultDivisions[$department] ?? [];
        return in_array($division, $defaultDivisions);
    }

    /**
     * Get division options for dropdown
     *
     * @param string $department Department name
     * @return array Division options
     */
    public function getDivisionOptions($department)
    {
        $divisions = $this->getDivisionsByDepartment($department);
        $options = [];

        foreach ($divisions as $division) {
            $options[] = [
                'value' => $division,
                'label' => $division
            ];
        }

        return $options;
    }
}

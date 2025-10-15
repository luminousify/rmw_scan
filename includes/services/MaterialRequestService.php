<?php

require_once __DIR__ . '/../DatabaseManager.php';

/**
 * MaterialRequestService
 * Handles business logic for material requests using unified DatabaseManager
 */
class MaterialRequestService
{
    private $dbManager;

    public function __construct()
    {
        $this->dbManager = DatabaseManager::getInstance();
    }

    /**
     * Get user's material requests with optional filters and pagination
     */
    public function getUserRequests($userId, $filters = [])
    {
        $whereConditions = ["mr.production_user_id = ?"];
        $params = [$userId];
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $whereConditions[] = "mr.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(mr.request_number LIKE ? OR mri.product_name LIKE ?)";
            $searchParam = "%{$filters['search']}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        // Add pagination
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "
            SELECT 
                mr.*,
                COUNT(mri.id) as item_count
            FROM material_requests mr
            LEFT JOIN material_request_items mri ON mr.id = mri.request_id
            $whereClause
            GROUP BY mr.id
            ORDER BY mr.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->dbManager->query($query, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get total count of user's requests for pagination
     */
    public function getUserRequestsCount($userId, $filters = [])
    {
        $whereConditions = ["mr.production_user_id = ?"];
        $params = [$userId];
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $whereConditions[] = "mr.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "mr.request_number LIKE ?";
            $params[] = "%{$filters['search']}%";
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $query = "SELECT COUNT(*) as total FROM material_requests mr $whereClause";
        $stmt = $this->dbManager->query($query, $params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    /**
     * Get request statistics for a user
     */
    public function getUserRequestStats($userId)
    {
        $query = "
            SELECT 
                status,
                COUNT(*) as count
            FROM material_requests
            WHERE production_user_id = ?
            GROUP BY status
        ";
        
        $stmt = $this->dbManager->query($query, [$userId]);
        $results = $stmt->fetchAll();
        $stats = [];
        
        foreach ($results as $row) {
            $stats[$row['status']] = $row['count'];
        }
        
        return $stats;
    }

    /**
     * Get request details with items
     */
    public function getRequestDetails($requestId, $userId)
    {
        // Get request info
        $requestQuery = "
            SELECT * FROM material_requests 
            WHERE id = ? AND production_user_id = ?
        ";
        
        $stmt = $this->dbManager->query($requestQuery, [$requestId, $userId]);
        $request = $stmt->fetch();
        
        if (!$request) {
            return null;
        }
        
        // Get request items
        $itemsQuery = "
            SELECT * FROM material_request_items 
            WHERE request_id = ?
            ORDER BY created_at ASC
        ";
        
        $stmt = $this->dbManager->query($itemsQuery, [$requestId]);
        $request['items'] = $stmt->fetchAll();
        
        return $request;
    }

    /**
     * Cancel a material request
     */
    public function cancelRequest($requestId, $userId)
    {
        try {
            $this->dbManager->beginTransaction();
            
            // Check if request exists and belongs to user
            $stmt = $this->dbManager->query("SELECT id, status, request_number FROM material_requests WHERE id = ? AND production_user_id = ?", [$requestId, $userId]);
            $request = $stmt->fetch();
            
            if (!$request) {
                throw new Exception('Request not found or unauthorized');
            }
            
            if ($request['status'] !== 'pending') {
                throw new Exception('Only pending requests can be cancelled');
            }
            
            // Update request status
            $this->dbManager->query("UPDATE material_requests SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$requestId]);
            
            // Update items status (handle constraint error gracefully)
            try {
                $this->dbManager->query("UPDATE material_request_items SET status = 'cancelled' WHERE request_id = ?", [$requestId]);
            } catch (Exception $e) {
                // If constraint error, just update to 'rejected' instead
                if (strpos($e->getMessage(), 'CHECK constraint failed') !== false) {
                    $this->dbManager->query("UPDATE material_request_items SET status = 'rejected' WHERE request_id = ?", [$requestId]);
                } else {
                    throw $e; // Re-throw if it's not a constraint error
                }
            }
            
            // Log the cancellation
            $this->dbManager->query("INSERT INTO activity_log (user_id, action, table_name, record_id, old_values, new_values) VALUES (?, 'CANCEL_REQUEST', 'material_requests', ?, ?, ?)", [
                $userId,
                $requestId,
                json_encode(['status' => $request['status']]),
                json_encode(['status' => 'cancelled'])
            ]);
            
            $this->dbManager->commit();
            
            return [
                'success' => true,
                'message' => 'Request cancelled successfully',
                'request_number' => $request['request_number']
            ];
            
        } catch (Exception $e) {
            $this->dbManager->rollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate request filters
     */
    public static function validateFilters($filters)
    {
        $validated = [];
        
        // Status validation
        $validStatuses = ['all', 'pending', 'diproses', 'completed', 'cancelled'];
        if (!empty($filters['status']) && in_array($filters['status'], $validStatuses)) {
            $validated['status'] = $filters['status'];
        }
        
        // Search validation
        if (!empty($filters['search'])) {
            $validated['search'] = trim($filters['search']);
        }
        
        return $validated;
    }
}

<?php

/**
 * Unified Database Manager
 * 
 * Provides centralized database connection management with retry logic
 * and consistent error handling across the application.
 */

class DatabaseManager
{
    private static $instance = null;
    private $pdo = null;
    private $config = [];
    private $connectionAttempts = 0;
    private $maxRetries = 3;
    private $retryDelay = 1000; // milliseconds
    
    /**
     * Private constructor to enforce singleton pattern
     */
    private function __construct()
    {
        $this->loadConfiguration();
        $this->establishConnection();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load database configuration
     */
    private function loadConfiguration()
    {
        $this->config = [
            'type' => DB_TYPE,
            'mysql_host' => DB_MYSQL_HOST,
            'mysql_name' => DB_MYSQL_NAME,
            'mysql_user' => DB_MYSQL_USER,
            'mysql_pass' => DB_MYSQL_PASS,
            'timeout' => 15,
            'charset' => 'utf8mb4'
        ];
    }
    
    /**
     * Establish database connection with retry logic
     */
    private function establishConnection()
    {
        $this->connectionAttempts = 0;
        
        while ($this->connectionAttempts < $this->maxRetries) {
            try {
                $this->createConnection();
                return; // Success, exit retry loop
            } catch (Exception $e) {
                $this->connectionAttempts++;
                $this->logConnectionAttempt($e->getMessage());
                
                if ($this->connectionAttempts < $this->maxRetries) {
                    usleep($this->retryDelay * 1000); // Convert to microseconds
                } else {
                    throw new Exception("Database connection failed after {$this->maxRetries} attempts: " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Create database connection based on type
     */
    private function createConnection()
    {
        if ($this->config['type'] === 'mysql') {
            $this->createMySQLConnection();
        } else {
            throw new Exception("Unsupported database type: {$this->config['type']}");
        }
    }
    
    /**
     * Create MySQL connection
     */
    private function createMySQLConnection()
    {
        $host = $this->config['mysql_host'];
        $dbname = $this->config['mysql_name'];
        $username = $this->config['mysql_user'];
        $password = $this->config['mysql_pass'];
        
        $dsn = "mysql:host={$host};dbname={$dbname};charset={$this->config['charset']}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => $this->config['timeout'],
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->config['charset']}"
        ];
        
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
            $this->logMessage("MySQL connection established successfully");
        } catch (PDOException $e) {
            // Try to create database if it doesn't exist
            try {
                $dsnNoDb = "mysql:host={$host};charset={$this->config['charset']}";
                $pdoTemp = new PDO($dsnNoDb, $username, $password, $options);
                $pdoTemp->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET {$this->config['charset']} COLLATE {$this->config['charset']}_unicode_ci");
                $pdoTemp = null;
                
                // Try connecting again
                $this->pdo = new PDO($dsn, $username, $password, $options);
                $this->logMessage("MySQL database created and connection established");
            } catch (PDOException $e2) {
                throw new Exception("MySQL connection failed: " . $e2->getMessage());
            }
        }
    }
    
    /**
     * Get PDO instance
     */
    public function getConnection()
    {
        if ($this->pdo === null) {
            $this->establishConnection();
        }
        return $this->pdo;
    }
    
    /**
     * Execute query with error handling
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError("Query failed: " . $e->getMessage(), ['sql' => $sql, 'params' => $params]);
            throw $e;
        }
    }

    /**
     * Prepare SQL statement
     */
    public function prepare($sql)
    {
        return $this->getConnection()->prepare($sql);
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit()
    {
        return $this->getConnection()->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback()
    {
        return $this->getConnection()->rollback();
    }
    
    /**
     * Get last insert ID
     */
    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }
    
    /**
     * Log connection attempt
     */
    private function logConnectionAttempt($message)
    {
        $logMessage = '[' . date('Y-m-d H:i:s') . "] Connection attempt {$this->connectionAttempts}: {$message}\n";
        file_put_contents(__DIR__ . '/conn.log', $logMessage, FILE_APPEND);
    }
    
    /**
     * Log general message
     */
    private function logMessage($message)
    {
        $logMessage = '[' . date('Y-m-d H:i:s') . "] {$message}\n";
        file_put_contents(__DIR__ . '/conn.log', $logMessage, FILE_APPEND);
    }
    
    /**
     * Log error
     */
    private function logError($message, $context = [])
    {
        $contextStr = !empty($context) ? ' Context: ' . json_encode($context) : '';
        $logMessage = '[' . date('Y-m-d H:i:s') . "] ERROR: {$message}{$contextStr}\n";
        file_put_contents(__DIR__ . '/conn.log', $logMessage, FILE_APPEND);
    }
    
    /**
     * Check if connection is alive
     */
    public function isConnectionAlive()
    {
        try {
            $this->getConnection()->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Reconnect if connection is lost
     */
    public function reconnect()
    {
        $this->pdo = null;
        $this->establishConnection();
    }
    
    /**
     * Get database statistics
     */
    public function getStats()
    {
        return [
            'connection_attempts' => $this->connectionAttempts,
            'is_connected' => $this->isConnectionAlive(),
            'database_type' => $this->config['type'],
            'database_path' => $this->config['sqlite_path']
        ];
    }
    
    /**
     * Validate customer reference number
     * 
     * @param string $custNoRef Customer reference number to validate
     * @return array Validation result with 'valid' and 'message' keys
     */
    public function validateCustNoRef($custNoRef)
    {
        try {
            if (empty($custNoRef)) {
                return ['valid' => false, 'message' => 'Nomor Bon wajib diisi'];
            }
            
            // First check if the customer reference exists at all
            $stmt = $this->query(
                "SELECT COUNT(*) as count FROM StockDetailVer WHERE CustNoRef = ?",
                [$custNoRef]
            );
            
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                return ['valid' => false, 'message' => 'Nomor Bon tidak ditemukan: ' . $custNoRef];
            }
            
            // Check the actual status of the customer reference
            $stmt = $this->query(
                "SELECT DISTINCT status FROM StockDetailVer WHERE CustNoRef = ?",
                [$custNoRef]
            );
            
            $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Accept any status (active, pending, etc.) since the data exists
            if (!empty($statuses)) {
                $statusList = implode(', ', $statuses);
                return [
                    'valid' => true, 
                    'message' => 'Nomor Bon ditemukan dengan status: ' . $statusList,
                    'statuses' => $statuses,
                    'record_count' => $result['total_count']
                ];
            } else {
                return ['valid' => false, 'message' => 'Nomor Bon ditemukan tetapi tidak memiliki status yang valid'];
            }
            
        } catch (Exception $e) {
            $this->logError("Error validating customer reference: " . $e->getMessage(), ['custNoRef' => $custNoRef]);
            return ['valid' => false, 'message' => 'Terjadi kesalahan saat memvalidasi Nomor Bon: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate LPB_SJ_No (Nomor Bon)
     * 
     * @param string $lpbSjNo Nomor Bon to validate
     * @return array Validation result with 'valid' and 'message' keys
     */
    public function validateLpbSjNo($lpbSjNo)
    {
        try {
            if (empty($lpbSjNo)) {
                return ['valid' => false, 'message' => 'Nomor Bon (LPB/SJ) wajib diisi'];
            }
            
            error_log("[Scanner] Validating LPB_SJ_No: " . $lpbSjNo);
            
            // Check if the LPB_SJ_No exists and get verification status
            $stmt = $this->query(
                "SELECT 
                    COUNT(*) as total_count,
                    COUNT(CASE WHEN Verifikasi = 1 THEN 1 END) as verified_count,
                    COUNT(CASE WHEN Verifikasi = 0 THEN 1 END) as unverified_count
                FROM StockDetailVer 
                WHERE LPB_SJ_No = ?",
                [$lpbSjNo]
            );
            
            $result = $stmt->fetch();
            
            if ($result['total_count'] == 0) {
                error_log("[Scanner] LPB_SJ_No not found: " . $lpbSjNo);
                return ['valid' => false, 'message' => 'Nomor Bon tidak ditemukan: ' . $lpbSjNo];
            }
            
            // Check if any records are already verified
            if ($result['verified_count'] > 0) {
                error_log("[Scanner] LPB_SJ_No already verified: " . $lpbSjNo . " - " . $result['verified_count'] . " verified records");
                return [
                    'valid' => false, 
                    'message' => 'Nomor Bon sudah diverifikasi. Tidak dapat memproses ulang.',
                    'verified_count' => $result['verified_count'],
                    'unverified_count' => $result['unverified_count'],
                    'already_verified' => true
                ];
            }
            
            error_log("[Scanner] LPB_SJ_No found: " . $lpbSjNo . " with " . $result['total_count'] . " records, " . $result['unverified_count'] . " unverified");
            
            // Check the actual status
            $stmt = $this->query(
                "SELECT DISTINCT status FROM StockDetailVer WHERE LPB_SJ_No = ?",
                [$lpbSjNo]
            );
            
            $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($statuses)) {
                $statusList = implode(', ', $statuses);
                return [
                    'valid' => true, 
                    'message' => 'Nomor Bon ditemukan dengan status: ' . $statusList,
                    'statuses' => $statuses,
                    'record_count' => $result['total_count']
                ];
            } else {
                return ['valid' => false, 'message' => 'Nomor Bon ditemukan tetapi tidak memiliki status yang valid'];
            }
            
        } catch (Exception $e) {
            $this->logError("Error validating LPB_SJ_No: " . $e->getMessage(), ['lpbSjNo' => $lpbSjNo]);
            return ['valid' => false, 'message' => 'Terjadi kesalahan saat memvalidasi Nomor Bon: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get StockDetailVer materials by LPB_SJ_No
     * 
     * @param string $lpbSjNo LPB_SJ_No (Nomor Bon)
     * @return array|null Array with 'items' key containing materials or null if not found
     */
    public function getStockDetailVerMaterialsByLpbSjNo($lpbSjNo)
    {
        try {
            if (empty($lpbSjNo)) {
                return null;
            }
            
            error_log("[Scanner] Fetching materials for LPB_SJ_No: " . $lpbSjNo);

            // Aggregate by Product_ID for the given LPB_SJ_No.
            // Business rule for scanner comparison:
            // - compare requested_quantity vs SUM(ShipTotal) per Product_ID
            $stmt = $this->query(
                "SELECT
                    LPB_SJ_No,
                    Product_ID,
                    MAX(Unit) AS Unit,
                    SUM(COALESCE(ShipTotal, 0)) AS ShipTotalSum,
                    MAX(StockDate) AS LastStockDate,
                    MAX(CustNoRef) AS CustNoRef,
                    MAX(Customer) AS Customer
                 FROM StockDetailVer
                 WHERE LPB_SJ_No = ?
                 GROUP BY LPB_SJ_No, Product_ID
                 ORDER BY LastStockDate DESC",
                [$lpbSjNo]
            );

            $rows = $stmt->fetchAll();

            if (empty($rows)) {
                error_log("[Scanner] No materials found for LPB_SJ_No: " . $lpbSjNo);
                return null;
            }

            error_log("[Scanner] Found " . count($rows) . " aggregated Product_ID rows for LPB_SJ_No: " . $lpbSjNo);

            // Extract details from first record
            $firstRow = $rows[0];

            // Map materials to expected format
            $mappedMaterials = [];
            foreach ($rows as $row) {
                $mappedMaterials[] = [
                    'product_id' => $row['Product_ID'] ?? 'N/A',
                    // StockDetailVer doesn't provide a name; keep product_id for display compatibility
                    'product_name' => $row['Product_ID'] ?? 'Unknown Product',
                    'quantity' => $row['ShipTotalSum'] ?? 0,
                    'unit' => $row['Unit'] ?? 'pcs',
                    'description' => '',
                    'stock_ref_no' => '',
                    'customer' => $row['Customer'] ?? '',
                    'lpb_sj_no' => $row['LPB_SJ_No'] ?? '',
                    'stock_date' => $row['LastStockDate'] ?? ''
                ];
            }

            return [
                'lpb_sj_no' => $firstRow['LPB_SJ_No'] ?? 'N/A',
                'customer_reference' => $firstRow['CustNoRef'] ?? 'N/A',
                'customer_name' => $firstRow['Customer'] ?? 'N/A',
                'items' => $mappedMaterials,
                'count' => count($mappedMaterials),
                'raw_data' => $rows
            ];
        } catch (Exception $e) {
            $this->logError("Error getting StockDetailVer materials by LPB: " . $e->getMessage(), ['lpbSjNo' => $lpbSjNo]);
            return null;
        }
    }
    
    /**
     * Get StockDetailVer materials by customer reference
     * 
     * @param string $custNoRef Customer reference number
     * @return array|null Array with 'items' key containing materials or null if not found
     */
    public function getStockDetailVerMaterials($custNoRef)
    {
        try {
            if (empty($custNoRef)) {
                return null;
            }
            
            $stmt = $this->query(
                "SELECT * FROM StockDetailVer WHERE CustNoRef = ? ORDER BY StockDate DESC",
                [$custNoRef]
            );
            
            $materials = $stmt->fetchAll();
            
            if (empty($materials)) {
                return null;
            }
            
            // Extract customer reference details from first record
            $firstRecord = $materials[0];
            
            // Map materials to expected format
            $mappedMaterials = [];
            foreach ($materials as $material) {
                $mappedMaterials[] = [
                    'product_id' => $material['Product_ID'] ?? 'N/A',
                    'product_name' => $material['Product_ID'] ?? 'Unknown Product', // StockDetailVer doesn't have product_name, use Product_ID
                    'quantity' => $material['RecdTotal'] ?? 0, // Use received quantity as default
                    'unit' => $material['Unit'] ?? 'pcs',
                    'description' => $material['Keterangan'] ?? '',
                    'stock_ref_no' => $material['StockRefNo'] ?? '',
                    'customer' => $material['Customer'] ?? '',
                    'lpb_sj_no' => $material['LPB_SJ_No'] ?? '',
                    'stock_date' => $material['StockDate'] ?? ''
                ];
            }
            
            // Return in expected format with proper field mapping
            return [
                'customer_reference' => $firstRecord['CustNoRef'] ?? 'N/A',
                'customer_name' => $firstRecord['Customer'] ?? 'N/A',
                'items' => $mappedMaterials,
                'count' => count($mappedMaterials),
                'cust_no_ref' => $custNoRef,
                'raw_data' => $materials // Keep raw data for debugging if needed
            ];
        } catch (Exception $e) {
            $this->logError("Error getting StockDetailVer materials: " . $e->getMessage(), ['custNoRef' => $custNoRef]);
            return null;
        }
    }
    
    /**
     * Get users by department and optional division
     *
     * @param string $department Department name (production, rmw, admin)
     * @param string|null $division Division name (optional)
     * @return array Array of users
     */
    public function getUsersByDepartment($department, $division = null)
    {
        try {
            if ($division === null) {
                $stmt = $this->query(
                    "SELECT id, username, full_name, department, division, email, is_active
                     FROM users
                     WHERE department = ? AND is_active = 1
                     ORDER BY full_name",
                    [$department]
                );
            } else {
                $stmt = $this->query(
                    "SELECT id, username, full_name, department, division, email, is_active
                     FROM users
                     WHERE department = ? AND division = ? AND is_active = 1
                     ORDER BY full_name",
                    [$department, $division]
                );
            }

            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logError("Error getting users by department: " . $e->getMessage(), [
                'department' => $department,
                'division' => $division
            ]);
            return [];
        }
    }

    /**
     * Get all divisions for a department
     *
     * @param string $department Department name
     * @return array Array of unique divisions
     */
    public function getDivisionsByDepartment($department)
    {
        try {
            $stmt = $this->query(
                "SELECT DISTINCT division
                 FROM users
                 WHERE department = ? AND (division IS NOT NULL AND division != '')
                 ORDER BY division",
                [$department]
            );

            $divisions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $divisions ?: [];
        } catch (Exception $e) {
            $this->logError("Error getting divisions: " . $e->getMessage(), ['department' => $department]);
            return [];
        }
    }

    /**
     * Get all users grouped by department and division
     *
     * @return array Array of users organized by department and division
     */
    public function getUsersGroupedByDivision()
    {
        try {
            $stmt = $this->query(
                "SELECT id, username, full_name, department, division, email, is_active
                 FROM users
                 WHERE is_active = 1
                 ORDER BY department, division, full_name"
            );

            $users = $stmt->fetchAll();
            $grouped = [];

            foreach ($users as $user) {
                $dept = $user['department'];
                $div = $user['division'] ?? 'Unassigned';

                if (!isset($grouped[$dept])) {
                    $grouped[$dept] = [];
                }
                if (!isset($grouped[$dept][$div])) {
                    $grouped[$dept][$div] = [];
                }

                $grouped[$dept][$div][] = $user;
            }

            return $grouped;
        } catch (Exception $e) {
            $this->logError("Error grouping users by division: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update user's division
     *
     * @param int $userId User ID
     * @param string $division Division name
     * @return bool Success status
     */
    public function updateUserDivision($userId, $division)
    {
        try {
            $stmt = $this->prepare("UPDATE users SET division = ? WHERE id = ?");
            return $stmt->execute([$division, $userId]);
        } catch (Exception $e) {
            $this->logError("Error updating user division: " . $e->getMessage(), [
                'userId' => $userId,
                'division' => $division
            ]);
            return false;
        }
    }

    /**
     * Get division statistics
     *
     * @return array Statistics about users by department and division
     */
    public function getDivisionStatistics()
    {
        try {
            $stmt = $this->query(
                "SELECT
                    department,
                    COALESCE(division, 'Unassigned') as division,
                    COUNT(*) as user_count
                 FROM users
                 GROUP BY department, division
                 ORDER BY department, division"
            );

            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logError("Error getting division statistics: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get material requests with user division information
     *
     * @return array Material requests with division details
     */
    public function getMaterialRequestsWithDivision()
    {
        try {
            $stmt = $this->query(
                "SELECT
                    mr.*,
                    pu.full_name as production_user_name,
                    pu.department as production_department,
                    pu.division as production_division,
                    ru.full_name as rmw_user_name,
                    ru.department as rmw_department,
                    ru.division as rmw_division
                 FROM material_requests mr
                 LEFT JOIN users pu ON mr.production_user_id = pu.id
                 LEFT JOIN users ru ON mr.rmw_user_id = ru.id
                 ORDER BY mr.request_date DESC"
            );

            return $stmt->fetchAll();
        } catch (Exception $e) {
            $this->logError("Error getting material requests with division: " . $e->getMessage());
            return [];
        }
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}

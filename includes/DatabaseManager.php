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
            'sqlite_path' => DB_SQLITE_PATH,
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
        if ($this->config['type'] === 'sqlite') {
            $this->createSQLiteConnection();
        } elseif ($this->config['type'] === 'mysql') {
            $this->createMySQLConnection();
        } else {
            throw new Exception("Unsupported database type: {$this->config['type']}");
        }
    }
    
    /**
     * Create SQLite connection
     */
    private function createSQLiteConnection()
    {
        $dbFile = $this->config['sqlite_path'];
        
        // Ensure database directory exists
        $dbDir = dirname($dbFile);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        // Check file permissions
        if (file_exists($dbFile) && !is_writable($dbFile)) {
            throw new Exception("Database file is not writable: {$dbFile}");
        }
        
        if (!is_writable($dbDir)) {
            throw new Exception("Database directory is not writable: {$dbDir}");
        }
        
        $dsn = "sqlite:" . $dbFile;
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => $this->config['timeout'],
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $this->pdo = new PDO($dsn, '', '', $options);
        
        // Enable foreign key constraints
        $this->pdo->exec('PRAGMA foreign_keys = ON');
        
        // Initialize database if needed
        $this->initializeDatabase();
    }
    
    /**
     * Initialize database schema if needed
     */
    private function initializeDatabase()
    {
        if ($this->config['type'] === 'sqlite') {
            $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
            if (!$stmt->fetch()) {
                $schemaFile = __DIR__ . '/../database/schema.sql';
                if (file_exists($schemaFile)) {
                    $schema = file_get_contents($schemaFile);
                    $this->pdo->exec($schema);
                    $this->logMessage("Database schema initialized");
                } else {
                    throw new Exception("Schema file not found: {$schemaFile}");
                }
            }
        }
        // For MySQL, schema should be pre-created via migration script
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
                return ['valid' => false, 'message' => 'Customer reference number is required'];
            }
            
            // First check if the customer reference exists at all
            $stmt = $this->query(
                "SELECT COUNT(*) as count FROM StockDetailVer WHERE CustNoRef = ?",
                [$custNoRef]
            );
            
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                return ['valid' => false, 'message' => 'Customer reference not found: ' . $custNoRef];
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
                    'message' => 'Customer reference found with status: ' . $statusList,
                    'statuses' => $statuses,
                    'record_count' => $result['count']
                ];
            } else {
                return ['valid' => false, 'message' => 'Customer reference found but no valid status'];
            }
            
        } catch (Exception $e) {
            $this->logError("Error validating customer reference: " . $e->getMessage(), ['custNoRef' => $custNoRef]);
            return ['valid' => false, 'message' => 'Error validating customer reference: ' . $e->getMessage()];
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
    
    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}

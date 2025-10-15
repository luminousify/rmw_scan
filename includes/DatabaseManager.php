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
            
            $stmt = $this->query(
                "SELECT COUNT(*) as count FROM StockDetailVer WHERE CustNoRef = ? AND status = 'active'",
                [$custNoRef]
            );
            
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return ['valid' => true, 'message' => 'Customer reference found and active'];
            } else {
                return ['valid' => false, 'message' => 'Customer reference not found or inactive'];
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
     * @return array|null Array of materials or null if not found
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
            
            return $materials;
        } catch (Exception $e) {
            $this->logError("Error getting StockDetailVer materials: " . $e->getMessage(), ['custNoRef' => $custNoRef]);
            return null;
        }
    }
    
    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}

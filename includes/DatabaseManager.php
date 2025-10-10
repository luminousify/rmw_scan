<?php
/**
 * Database Manager Class
 * 
 * Handles database connections for different types (SQLite, MySQL)
 * Provides unified interface for database operations
 */

class DatabaseManager {
    private $dbType;
    private $connection;
    private $config;
    
    public function __construct($type = 'sqlite') {
        $this->dbType = $type;
        $this->loadConfig();
        $this->connect();
    }
    
    private function loadConfig() {
        $this->config = [
            'sqlite' => [
                'dbFile' => __DIR__ . '/../database/rmw.db',
                'dsn' => 'sqlite:' . __DIR__ . '/../database/rmw.db'
            ],
            'mysql' => [
                'host' => 'localhost',
                'dbname' => 'rmw',
                'username' => 'root',
                'password' => '',
                'dsn' => 'mysql:host=localhost;dbname=rmw;charset=utf8mb4'
            ]
        ];
    }
    
    private function connect() {
        try {
            switch($this->dbType) {
                case 'sqlite':
                    $this->connectSQLite();
                    break;
                case 'mysql':
                    $this->connectMySQL();
                    break;
                default:
                    throw new Exception("Unsupported database type: " . $this->dbType);
            }
        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    private function connectSQLite() {
        $config = $this->config['sqlite'];
        
        // Ensure database directory exists
        $dbDir = dirname($config['dbFile']);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 15,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $this->connection = new PDO($config['dsn'], '', '', $options);
        $this->connection->exec('PRAGMA foreign_keys = ON');
    }
    
    private function connectMySQL() {
        $config = $this->config['mysql'];
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 15,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
        
        $this->connection = new PDO(
            $config['dsn'], 
            $config['username'], 
            $config['password'], 
            $options
        );
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function getDBType() {
        return $this->dbType;
    }
    
    public function getTablePrefix() {
        return '';
    }
    
    public function formatDBDate($date = null) {
        if ($date === null) {
            $date = new DateTime();
        }
        return $date->format('Y-m-d H:i:s');
    }
    
    // Helper method for SQLite AUTOINCREMENT vs MySQL AUTO_INCREMENT
    public function getAutoIncrementKeyword() {
        return ($this->dbType === 'sqlite') ? 'AUTOINCREMENT' : 'AUTO_INCREMENT';
    }
    
    // Helper method for boolean handling
    public function formatBoolean($value) {
        if ($this->dbType === 'sqlite') {
            return $value ? 1 : 0;
        } else {
            return $value ? true : false;
        }
    }
    
    // Helper method for limit syntax
    public function formatLimit($limit, $offset = 0) {
        if ($this->dbType === 'sqlite') {
            return "LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        } else {
            return "LIMIT " . intval($offset) . ", " . intval($limit);
        }
    }
    
    // Method to check if table exists
    public function tableExists($tableName) {
        try {
            if ($this->dbType === 'sqlite') {
                $stmt = $this->connection->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
            } else {
                $stmt = $this->connection->prepare("SHOW TABLES LIKE ?");
            }
            $stmt->execute([$tableName]);
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Method to get table schema
    public function getTableSchema($tableName) {
        try {
            if ($this->dbType === 'sqlite') {
                $stmt = $this->connection->prepare("PRAGMA table_info($tableName)");
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                $stmt = $this->connection->prepare("DESCRIBE $tableName");
                $stmt->execute();
                return $stmt->fetchAll();
            }
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Method to close connection
    public function close() {
        $this->connection = null;
    }
    
    // StockDetailVer related methods
    
    /**
     * Get StockDetailVer materials by CustNoRef
     * @param string $custNoRef Customer reference number
     * @return array StockDetailVer records
     */
    public function getStockDetailVerByCustRef($custNoRef) {
        try {
            $stmt = $this->connection->prepare("
                SELECT 
                    id,
                    StockDateNo,
                    StockDate,
                    StockRefNo,
                    Ke_Dari,
                    LPB_SJ_No,
                    Mode,
                    Product_ID,
                    Unit,
                    PONO,
                    SuppID,
                    SuppRef,
                    SuppRefDate,
                    Customer,
                    CustNoRef,
                    CustRefDate,
                    Keterangan,
                    RecdTotal,
                    ShipTotal,
                    CumInv,
                    RecdNG,
                    ShipNG,
                    CumInvNG,
                    NoSJSales,
                    timetransfer,
                    status,
                    Verifikasi,
                    copy,
                    Oldqty,
                    Revby,
                    KodekaitProduksi,
                    transferby,
                    Revisitime
                FROM StockDetailVer 
                WHERE CustNoRef = ? 
                ORDER BY Product_ID ASC
            ");
            
            $stmt->execute([$custNoRef]);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting StockDetailVer data: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get StockDetailVer materials in standardized format for comparison
     * @param string $custNoRef Customer reference number
     * @return array Standardized material data
     */
    public function getStockDetailVerMaterials($custNoRef) {
        try {
            $records = $this->getStockDetailVerByCustRef($custNoRef);
            
            if (empty($records)) {
                return null;
            }
            
            // Extract customer info from first record
            $firstRecord = $records[0];
            
            $materials = [];
            foreach ($records as $record) {
                // Map StockDetailVer fields to standard format
                $materials[] = [
                    'id' => $record['id'],
                    'product_id' => $record['Product_ID'],
                    'product_name' => $record['Product_ID'], // Using Product_ID as name for now
                    'quantity' => $record['RecdTotal'] ?? 0,
                    'unit' => $record['Unit'] ?? 'pcs',
                    'description' => $record['Keterangan'] ?? '',
                    'status' => $record['status'] ?? 'pending',
                    'stock_ref_no' => $record['StockRefNo'],
                    'lpb_sj_no' => $record['LPB_SJ_No'],
                    'supplier' => $record['SuppID'],
                    'supplier_ref' => $record['SuppRef'],
                    'verification_status' => $record['Verifikasi'] ?? 0
                ];
            }
            
            // Return standardized format
            return [
                'customer_reference' => $firstRecord['CustNoRef'],
                'customer_name' => $firstRecord['Customer'] ?? 'Unknown Customer',
                'document_number' => $firstRecord['LPB_SJ_No'] ?? '',
                'reference_date' => $firstRecord['CustRefDate'] ?? $firstRecord['StockDate'] ?? '',
                'items' => $materials
            ];
            
        } catch (Exception $e) {
            error_log("Error getting standardized StockDetailVer materials: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Validate CustNoRef format
     * @param string $custNoRef Customer reference number to validate
     * @return array Validation result with 'valid' boolean and 'message' string
     */
    public function validateCustNoRef($custNoRef) {
        // Remove whitespace and convert to uppercase
        $custNoRef = trim(strtoupper($custNoRef));
        
        // Check if empty
        if (empty($custNoRef)) {
            return ['valid' => false, 'message' => 'Customer reference is required'];
        }
        
        // Check format - must contain at least 2 parts separated by "/"
        if (!strpos($custNoRef, '/')) {
            return ['valid' => false, 'message' => 'Invalid format. Must contain "/" separator (e.g., INJ/FG/1887-1)'];
        }
        
        // Check for minimum parts (at least 2 parts separated by "/")
        $parts = explode('/', $custNoRef);
        if (count($parts) < 2) {
            return ['valid' => false, 'message' => 'Invalid format. Must have at least 2 parts separated by "/"'];
        }
        
        // Check if it exists in database
        try {
            $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM StockDetailVer WHERE CustNoRef = ?");
            $stmt->execute([$custNoRef]);
            $count = $stmt->fetch()['count'];
            
            if ($count == 0) {
                return ['valid' => false, 'message' => "Customer reference '$custNoRef' not found in StockDetailVer"];
            }
            
        } catch (Exception $e) {
            error_log("Error validating CustNoRef in database: " . $e->getMessage());
            return ['valid' => false, 'message' => 'Database error during validation'];
        }
        
        return ['valid' => true, 'message' => 'Valid customer reference'];
    }
    
    /**
     * Check if StockDetailVer table exists and has data
     * @return array Status information
     */
    public function checkStockDetailVerStatus() {
        try {
            if (!$this->tableExists('StockDetailVer')) {
                return ['exists' => false, 'has_data' => false, 'message' => 'StockDetailVer table does not exist'];
            }
            
            $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM StockDetailVer");
            $stmt->execute();
            $count = $stmt->fetch()['count'];
            
            return [
                'exists' => true, 
                'has_data' => $count > 0, 
                'record_count' => $count,
                'message' => $count > 0 ? "StockDetailVer has $count records" : "StockDetailVer table is empty"
            ];
            
        } catch (Exception $e) {
            return ['exists' => false, 'has_data' => false, 'message' => 'Error checking StockDetailVer: ' . $e->getMessage()];
        }
    }

    // Destructor
    public function __destruct() {
        $this->close();
    }
}

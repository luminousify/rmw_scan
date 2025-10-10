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
    
    // Destructor
    public function __destruct() {
        $this->close();
    }
}

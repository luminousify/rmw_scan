<?php
/**
 * Migration: Create StockDetailVer Table
 * 
 * Creates the StockDetailVer table for stock verification and tracking
 * Converts MySQL schema to SQLite-compatible format
 */

require_once dirname(__DIR__) . '/../config.php';
require_once dirname(__DIR__) . '/../includes/DatabaseManager.php';

class StockDetailVerMigration {
    private $db;
    private $pdo;
    
    public function __construct() {
        $this->db = new DatabaseManager('sqlite');
        $this->pdo = $this->db->getConnection();
    }
    
    /**
     * Create the StockDetailVer table
     */
    public function up() {
        try {
            echo "Creating StockDetailVer table...\n";
            
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Check if table already exists
            if ($this->db->tableExists('StockDetailVer')) {
                echo "  Table StockDetailVer already exists. Skipping creation.\n";
                $this->pdo->rollBack();
                return true;
            }
            
            // SQLite-compatible CREATE TABLE statement
            $sql = "
                CREATE TABLE StockDetailVer (
                    -- Auto-increment ID for SQLite compatibility
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    
                    -- Fields from second image
                    StockDateNo TEXT,
                    StockDate DATETIME,
                    StockRefNo TEXT UNIQUE NOT NULL,
                    Ke_Dari TEXT,
                    LPB_SJ_No TEXT,
                    Mode TEXT,
                    Product_ID TEXT,
                    Unit TEXT,
                    PONO TEXT,
                    SuppID TEXT,
                    SuppRef TEXT,
                    SuppRefDate DATETIME,
                    Customer TEXT,
                    CustNoRef TEXT,
                    CustRefDate DATETIME,
                    Keterangan TEXT,
                    RecdTotal REAL,
                    ShipTotal REAL,
                    CumInv REAL,

                    -- Fields from first image
                    RecdNG REAL,
                    ShipNG REAL,
                    CumInvNG REAL,
                    NoSJSales TEXT,
                    timetransfer DATETIME,
                    status TEXT,
                    Verifikasi INTEGER DEFAULT 0,
                    copy INTEGER DEFAULT 0,
                    Oldqty REAL,
                    Revby TEXT,
                    KodekaitProduksi TEXT,
                    transferby TEXT,
                    Revisitime DATETIME
                );
            ";
            
            $this->pdo->exec($sql);
            echo "  StockDetailVer table created successfully.\n";
            
            // Create indexes for performance
            $this->createIndexes();
            
            // Commit transaction
            $this->pdo->commit();
            echo "  Migration completed successfully!\n";
            
            return true;
            
        } catch (Exception $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            echo "  ERROR: Failed to create StockDetailVer table: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Create indexes for the StockDetailVer table
     */
    private function createIndexes() {
        $indexes = [
            'CREATE INDEX IF NOT EXISTS idx_stockdetailver_nosjsales ON StockDetailVer(NoSJSales);',
            'CREATE INDEX IF NOT EXISTS idx_stockdetailver_product_id ON StockDetailVer(Product_ID);',
            'CREATE INDEX IF NOT EXISTS idx_stockdetailver_stockdate ON StockDetailVer(StockDate);',
            'CREATE INDEX IF NOT EXISTS idx_stockdetailver_stockrefno ON StockDetailVer(StockRefNo);',
            'CREATE INDEX IF NOT EXISTS idx_stockdetailver_status ON StockDetailVer(status);',
            'CREATE INDEX IF NOT EXISTS idx_stockdetailver_verifikasi ON StockDetailVer(Verifikasi);'
        ];
        
        foreach ($indexes as $indexSql) {
            try {
                $this->pdo->exec($indexSql);
                echo "  Created index: " . substr($indexSql, strpos($indexSql, 'idx_'), 20) . "...\n";
            } catch (Exception $e) {
                echo "  Warning: Failed to create index: " . $e->getMessage() . "\n";
            }
        }
    }
    
    /**
     * Drop the StockDetailVer table (rollback)
     */
    public function down() {
        try {
            echo "Dropping StockDetailVer table...\n";
            
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Drop indexes first
            $this->dropIndexes();
            
            // Drop table
            $this->pdo->exec("DROP TABLE IF EXISTS StockDetailVer");
            
            // Commit transaction
            $this->pdo->commit();
            echo "  StockDetailVer table dropped successfully.\n";
            
            return true;
            
        } catch (Exception $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            echo "  ERROR: Failed to drop StockDetailVer table: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Drop indexes for the StockDetailVer table
     */
    private function dropIndexes() {
        $indexes = [
            'idx_stockdetailver_nosjsales',
            'idx_stockdetailver_product_id',
            'idx_stockdetailver_stockdate',
            'idx_stockdetailver_stockrefno',
            'idx_stockdetailver_status',
            'idx_stockdetailver_verifikasi'
        ];
        
        foreach ($indexes as $indexName) {
            try {
                $this->pdo->exec("DROP INDEX IF EXISTS $indexName");
                echo "  Dropped index: $indexName\n";
            } catch (Exception $e) {
                echo "  Warning: Failed to drop index $indexName: " . $e->getMessage() . "\n";
            }
        }
    }
    
    /**
     * Get table schema information
     */
    public function getTableInfo() {
        try {
            $schema = $this->db->getTableSchema('StockDetailVer');
            
            if (empty($schema)) {
                echo "  Table StockDetailVer does not exist.\n";
                return false;
            }
            
            echo "  StockDetailVer table schema:\n";
            foreach ($schema as $column) {
                $nullable = $column['notnull'] ? 'NOT NULL' : 'NULL';
                $default = $column['dflt_value'] !== null ? " DEFAULT " . $column['dflt_value'] : '';
                echo "    - {$column['name']}: {$column['type']} $nullable$default\n";
            }
            
            return $schema;
            
        } catch (Exception $e) {
            echo "  ERROR: Failed to get table info: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test basic table operations
     */
    public function testTable() {
        try {
            echo "  Testing StockDetailVer table operations...\n";
            
            // Test insert
            $stmt = $this->pdo->prepare("
                INSERT INTO StockDetailVer (
                    StockRefNo, StockDate, Product_ID, Unit, status, Verifikasi, 
                    RecdTotal, ShipTotal, CumInv
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $testData = [
                'TEST-001',
                date('Y-m-d H:i:s'),
                'MAT001',
                'pcs',
                'pending',
                0,
                100.0,
                0.0,
                100.0
            ];
            
            $stmt->execute($testData);
            $insertId = $this->pdo->lastInsertId();
            echo "    ✓ Insert test record (ID: $insertId)\n";
            
            // Test select
            $stmt = $this->pdo->prepare("SELECT * FROM StockDetailVer WHERE id = ?");
            $stmt->execute([$insertId]);
            $record = $stmt->fetch();
            
            if ($record && $record['StockRefNo'] === 'TEST-001') {
                echo "    ✓ Select test record successful\n";
            } else {
                throw new Exception("Select test failed");
            }
            
            // Test update
            $stmt = $this->pdo->prepare("UPDATE StockDetailVer SET status = ? WHERE id = ?");
            $stmt->execute(['completed', $insertId]);
            
            if ($stmt->rowCount() > 0) {
                echo "    ✓ Update test record successful\n";
            } else {
                throw new Exception("Update test failed");
            }
            
            // Test delete
            $stmt = $this->pdo->prepare("DELETE FROM StockDetailVer WHERE id = ?");
            $stmt->execute([$insertId]);
            
            if ($stmt->rowCount() > 0) {
                echo "    ✓ Delete test record successful\n";
            } else {
                throw new Exception("Delete test failed");
            }
            
            echo "  ✓ All table operations test passed!\n";
            return true;
            
        } catch (Exception $e) {
            echo "  ✗ Table operations test failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    $migration = new StockDetailVerMigration();
    
    $action = $argv[1] ?? 'up';
    
    echo "StockDetailVer Migration\n";
    echo "========================\n";
    
    switch ($action) {
        case 'up':
            $migration->up();
            break;
            
        case 'down':
            $migration->down();
            break;
            
        case 'info':
            $migration->getTableInfo();
            break;
            
        case 'test':
            $migration->testTable();
            break;
            
        default:
            echo "Usage: php 001_create_stock_detail_ver.php [up|down|info|test]\n";
            echo "  up    - Create the table\n";
            echo "  down  - Drop the table\n";
            echo "  info  - Show table schema\n";
            echo "  test  - Test table operations\n";
            break;
    }
}
?>

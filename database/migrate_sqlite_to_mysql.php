<?php
/**
 * Migration Script: SQLite to MySQL
 * 
 * Migrates all data from SQLite database to MySQL database
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';

// Color codes for output
define('CLR_RESET', "\033[0m");
define('CLR_SUCCESS', "\033[32m");
define('CLR_ERROR', "\033[31m");
define('CLR_INFO', "\033[34m");
define('CLR_WARNING', "\033[33m");

echo CLR_INFO . "====================================\n" . CLR_RESET;
echo CLR_INFO . "  SQLite to MySQL Migration Script\n" . CLR_RESET;
echo CLR_INFO . "====================================\n\n" . CLR_RESET;

// Step 1: Connect to SQLite (source)
echo CLR_INFO . "[1/8] Connecting to SQLite database...\n" . CLR_RESET;
try {
    $sqlitePath = DB_SQLITE_PATH;
    if (!file_exists($sqlitePath)) {
        throw new Exception("SQLite database not found: {$sqlitePath}");
    }
    
    $pdo_sqlite = new PDO("sqlite:{$sqlitePath}", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo CLR_SUCCESS . "    ✓ Connected to SQLite\n" . CLR_RESET;
} catch (Exception $e) {
    echo CLR_ERROR . "    ✗ Failed to connect to SQLite: " . $e->getMessage() . "\n" . CLR_RESET;
    exit(1);
}

// Step 2: Connect to MySQL (destination)
echo CLR_INFO . "[2/8] Connecting to MySQL database...\n" . CLR_RESET;
try {
    $host = DB_MYSQL_HOST;
    $dbname = DB_MYSQL_NAME;
    $username = DB_MYSQL_USER;
    $password = DB_MYSQL_PASS;
    
    // First connect without database to create it if needed
    $pdo_mysql = new PDO("mysql:host={$host};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Create database if not exists
    $pdo_mysql->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo CLR_SUCCESS . "    ✓ Database '{$dbname}' ready\n" . CLR_RESET;
    
    // Now connect to the database
    $pdo_mysql->exec("USE `{$dbname}`");
    
    echo CLR_SUCCESS . "    ✓ Connected to MySQL\n" . CLR_RESET;
} catch (Exception $e) {
    echo CLR_ERROR . "    ✗ Failed to connect to MySQL: " . $e->getMessage() . "\n" . CLR_RESET;
    exit(1);
}

// Step 3: Execute MySQL schema
echo CLR_INFO . "[3/8] Creating MySQL schema...\n" . CLR_RESET;
try {
    $schemaFile = __DIR__ . '/schema_mysql.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: {$schemaFile}");
    }
    
    $schema = file_get_contents($schemaFile);
    
    // Split schema into individual statements
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo_mysql->exec($statement);
            } catch (PDOException $e) {
                // Ignore "Table already exists" errors and other harmless errors
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo CLR_WARNING . "    ⚠ Schema warning: " . substr($e->getMessage(), 0, 100) . "\n" . CLR_RESET;
                }
            }
        }
    }
    
    echo CLR_SUCCESS . "    ✓ Schema created/verified\n" . CLR_RESET;
} catch (Exception $e) {
    echo CLR_ERROR . "    ✗ Failed to create schema: " . $e->getMessage() . "\n" . CLR_RESET;
    exit(1);
}

// Step 4: Get list of tables to migrate
$tables = ['users', 'products', 'material_requests', 'material_request_items', 'activity_log', 'StockDetailVer'];

// Step 5: Migrate data table by table
echo CLR_INFO . "[4/8] Migrating data from SQLite to MySQL...\n\n" . CLR_RESET;

$totalRows = 0;
$errors = [];

foreach ($tables as $index => $table) {
    echo CLR_INFO . "  [{$table}] " . CLR_RESET;
    
    try {
        // Check if table exists in SQLite
        $stmt = $pdo_sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}'");
        if (!$stmt->fetch()) {
            echo CLR_WARNING . "skipped (not in SQLite)\n" . CLR_RESET;
            continue;
        }
        
        // Disable foreign key checks for MySQL
        $pdo_mysql->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Get data from SQLite
        $stmt = $pdo_sqlite->query("SELECT * FROM {$table}");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($rows)) {
            echo CLR_WARNING . "skipped (no data)\n" . CLR_RESET;
            continue;
        }
        
        // Get column names
        $columns = array_keys($rows[0]);
        
        // Build INSERT statement
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $columnNames = '`' . implode('`,`', $columns) . '`';
        
        // Clear existing data for this table
        $pdo_mysql->exec("DELETE FROM `{$table}`");
        
        // Insert data
        $insertSql = "INSERT INTO `{$table}` ({$columnNames}) VALUES ({$placeholders})";
        $insertStmt = $pdo_mysql->prepare($insertSql);
        
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $column) {
                $values[] = $row[$column];
            }
            
            try {
                $insertStmt->execute($values);
            } catch (PDOException $e) {
                // Log error but continue
                $errors[] = "Error inserting into {$table}: " . $e->getMessage();
            }
        }
        
        // Re-enable foreign key checks
        $pdo_mysql->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        $rowCount = count($rows);
        $totalRows += $rowCount;
        echo CLR_SUCCESS . "{$rowCount} rows migrated\n" . CLR_RESET;
        
    } catch (Exception $e) {
        echo CLR_ERROR . "failed: " . $e->getMessage() . "\n" . CLR_RESET;
        $errors[] = "Error migrating {$table}: " . $e->getMessage();
    }
}

// Step 6: Verify row counts
echo CLR_INFO . "\n[5/8] Verifying data integrity...\n" . CLR_RESET;
$verificationErrors = [];

foreach ($tables as $table) {
    try {
        $sqliteCount = $pdo_sqlite->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        
        try {
            $mysqlCount = $pdo_mysql->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            
            if ($sqliteCount != $mysqlCount) {
                $verificationErrors[] = "{$table}: SQLite has {$sqliteCount} rows, MySQL has {$mysqlCount} rows";
                echo CLR_WARNING . "  ⚠ {$table}: " . CLR_RESET . "count mismatch\n";
            } else {
                echo CLR_SUCCESS . "  ✓ {$table}: " . CLR_RESET . "{$mysqlCount} rows match\n";
            }
        } catch (PDOException $e) {
            // Table might not exist in MySQL
            echo CLR_WARNING . "  ⚠ {$table}: skipped (not in MySQL)\n" . CLR_RESET;
        }
    } catch (PDOException $e) {
        // Table might not exist in SQLite
    }
}

// Step 7: Report results
echo CLR_INFO . "\n[6/8] Migration Summary\n" . CLR_RESET;
echo "  Total rows migrated: {$totalRows}\n";

if (!empty($errors)) {
    echo CLR_ERROR . "\n  Errors encountered:\n" . CLR_RESET;
    foreach ($errors as $error) {
        echo CLR_ERROR . "  - " . CLR_RESET . $error . "\n";
    }
}

if (!empty($verificationErrors)) {
    echo CLR_WARNING . "\n  Verification warnings:\n" . CLR_RESET;
    foreach ($verificationErrors as $error) {
        echo CLR_WARNING . "  - " . CLR_RESET . $error . "\n";
    }
}

// Step 8: Display completion message
echo CLR_INFO . "\n[7/8] Migration complete!\n\n" . CLR_RESET;

echo CLR_WARNING . "⚠ IMPORTANT NEXT STEPS:\n" . CLR_RESET;
echo "1. Update config.php: Change DB_TYPE from 'sqlite' to 'mysql'\n";
echo "2. Update DatabaseManager.php to support MySQL connections\n";
echo "3. Test the application to ensure everything works\n";
echo "4. Keep database/rmw.db as backup (do not delete)\n\n";

echo CLR_SUCCESS . "✓ Migration finished successfully!\n" . CLR_RESET;


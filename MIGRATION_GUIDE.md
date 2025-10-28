# SQLite to MySQL Migration Guide

## Overview
This guide documents the migration of the RMW Scan system from SQLite to MySQL database.

## Files Modified

### Core Database Files
- `database/schema_mysql.sql` - Updated MySQL schema to match SQLite exactly
- `includes/DatabaseManager.php` - Added MySQL connection support
- `includes/conn.php` - NEW: Unified connection handler
- `includes/conn_sqlite.php` - Now routes to unified connector
- `config.php` - Changed DB_TYPE from 'sqlite' to 'mysql'

### Migration Script
- `database/migrate_sqlite_to_mysql.php` - NEW: Complete data migration script

## Migration Steps

### Prerequisites
Before running the migration, ensure:
1. MySQL service is running on localhost
2. Database credentials in `config.php` are correct:
   - Host: localhost
   - Database: rmw
   - User: root
   - Password: (empty by default)
3. PDO_MySQL extension is installed and enabled in PHP

### Enabling PDO_MySQL (if needed)

**For Laragon/XAMPP/Windows:**
1. Open php.ini (located in your PHP installation directory)
2. Find the line: `;extension=pdo_mysql`
3. Remove the semicolon to uncomment: `extension=pdo_mysql`
4. Restart web server

**Check if enabled:**
```bash
php -m | findstr pdo_mysql
```

Should output: `pdo_mysql` (not empty)

### Running the Migration

1. **Backup SQLite Database:**
   ```bash
   copy database\rmw.db database\rmw.db.backup
   ```

2. **Run Migration Script:**
   ```bash
   php database\migrate_sqlite_to_mysql.php
   ```

3. **Verify Migration:**
   - Check migration script output for any errors
   - Verify row counts match between SQLite and MySQL
   - Test application login and basic functionality

### Post-Migration Testing

Test the following features to ensure everything works:
1. **Authentication:**
   - Login with existing users (prod, rmw, etc.)
   - Session management

2. **Material Requests:**
   - Create new material requests
   - View existing requests
   - Pagination works correctly

3. **RMW Dashboard:**
   - View pending requests
   - Process requests
   - Scanner integration

4. **Data Integrity:**
   - StockDetailVer data is accessible
   - QR code tracking works
   - Activity logs are recorded

### Rollback Plan

If migration fails or issues arise:

1. **Change DB_TYPE back to SQLite:**
   Edit `config.php`:
   ```php
   define('DB_TYPE', 'sqlite');
   ```

2. **Restore backup if needed:**
   ```bash
   copy database\rmw.db.backup database\rmw.db
   ```

3. **System will continue working with SQLite**

## Schema Differences Handled

The migration script handles the following differences:

### Data Type Conversions
- SQLite INTEGER → MySQL INT AUTO_INCREMENT
- SQLite TEXT → MySQL VARCHAR/TEXT
- SQLite REAL → MySQL DECIMAL
- SQLite BOOLEAN → MySQL TINYINT(1)
- SQLite DATETIME → MySQL DATETIME

### SQL Syntax Differences
- `INSERT OR IGNORE` → `INSERT IGNORE`
- `AUTOINCREMENT` → `AUTO_INCREMENT`
- CHECK constraints → ENUM types

### Field Mappings
All fields from SQLite schema are preserved in MySQL including:
- Additional fields in `material_requests` table
- `customer_reference` field
- All StockDetailVer actorization fields
- Proper status enums

## Backup Strategy

The SQLite database file (`database/rmw.db`) is kept as a permanent backup:
- **Location:** `database/rmw.db`
- **Backup Location:** `database/rmw.db.backup`
- **Do NOT delete** the SQLite file - it serves as data recovery point

## Troubleshooting

### Issue: "could not find driver"
**Cause:** PDO_MySQL extension not enabled
**Solution:** Enable `extension=pdo_mysql` in php.ini and restart server

### Issue: "Access denied for user"
**Cause:** MySQL credentials incorrect
**Solution:** Verify credentials in `config.php` and MySQL user permissions

### Issue: "Table already exists"
**Solution:** This is normal during re-runs. The script handles this gracefully.

### Issue: "Row count mismatch"
**Cause:** Data insertion errors during migration
**Solution:** Check migration script output for specific errors and re-run if needed

## Technical Notes

### DatabaseManager Changes
- Added `createMySQLConnection()` method
- Added MySQL-specific configuration loading
- Updated `initializeDatabase()` to skip SQLite-specific initialization for MySQL
- Maintains singleton pattern for both database types

### Connection Routing
- `includes/conn.php` - Unified connection handler (routes based on DB_TYPE)
- `includes/conn_sqlite.php` - Legacy wrapper for backward compatibility
- All controllers automatically use the correct database via DatabaseManager

### Foreign Key Handling
- Migration script temporarily disables foreign key checks during data migration
- Re-enables them after all data is migrated
- Ensures data integrity throughout the process

## Success Criteria

Migration is successful when:
- ✓ All tables exist in MySQL with correct schema
- ✓ Row counts match between SQLite and MySQL
- ✓ Application successfully connects to MySQL
- ✓ All features work identically to SQLite version
- ✓ SQLite backup file preserved

## Support

For issues or questions:
1. Check the migration script output for specific errors
2. Review `includes/conn.log` for connection issues
3. Verify MySQL service is running
4. Ensure PDO_MySQL extension is enabled
5. Check MySQL user permissions


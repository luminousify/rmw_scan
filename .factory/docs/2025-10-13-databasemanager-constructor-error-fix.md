# DatabaseManager Constructor Error Fix Plan

## Root Cause
The error occurs because `scanner.php` tries to call `new DatabaseManager('sqlite')` but the DatabaseManager constructor is `private` (singleton pattern). The code should use `DatabaseManager::getInstance()` instead.

## Fix Steps
1. **Fix scanner.php line 252**: Replace `new DatabaseManager('sqlite')` with `DatabaseManager::getInstance()`
2. **Fix scanner.php line 28**: Same replacement for consistency
3. **Add missing methods**: Implement `validateCustNoRef()` and `getStockDetailVerMaterials()` in DatabaseManager
4. **Fix migration file**: Update `database/migrations/001_create_stock_detail_ver.php` line 17
5. **Audit and standardize**: Check all files for incorrect DatabaseManager usage
6. **Test thoroughly**: Verify scanner functionality and database operations work correctly

## Validation
- Access the scanner URL without fatal errors
- Test customer reference validation
- Verify StockDetailVer data retrieval
- Ensure all DatabaseManager usage follows singleton pattern

This addresses both the immediate error and prevents similar issues across the application.
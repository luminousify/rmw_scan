# Bug Fix Plan: Scanner Customer Reference Validation Issue

## Root Cause Analysis
Based on investigation, the issue has multiple potential causes:

1. **Database Connection Mismatch**: The system appears to be using ODBC connection to Microsoft Access database, not SQLite as assumed in the DatabaseManager validation logic.

2. **SQL Syntax Issues**: The debug log shows SQL syntax errors with ODBC/Access driver, particularly around boolean value handling.

3. **Data Format Inconsistency**: The scanned QR code "INJ/FG/1887-1" may not match the exact format stored in CustNoRef field.

4. **Status Field Logic**: The validation requires status = 'active', but the actual status values in the database may differ.

## Fix Plan Steps

### Step 1: Diagnose Database Configuration
- Check actual DB_TYPE setting in config.php
- Verify if the system is using Access ODBC or SQLite
- Examine the StockDetailVer table structure in the actual database

### Step 2: Test Data Availability
- Create a debug script to query StockDetailVer for "INJ/FG/1887-1"
- Check if the customer reference exists and what its status value is
- Verify the exact format of CustNoRef data in the database

### Step 3: Fix Validation Logic
- Update DatabaseManager::validateCustNoRef() to handle the correct database type
- Fix SQL syntax issues for ODBC/Access compatibility
- Add proper error logging to capture the actual database errors

### Step 4: Improve Error Handling
- Add detailed logging to show the exact query being executed
- Include the actual database error messages in the response
- Add debugging information to show what data was found

### Step 5: Testing & Validation
- Test with the specific QR code "INJ/FG/1887-1"
- Verify the comparison logic works correctly
- Test edge cases and error conditions

This plan addresses the fundamental database connectivity and data validation issues while providing better error visibility.
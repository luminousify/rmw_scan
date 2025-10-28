# Bug Fix Plan: Undefined Array Key Warnings in CLI Context

## Root Cause Analysis
The issue occurs because the `config.php` file accesses `$_SERVER` superglobal variables (`SERVER_PORT` and `HTTP_HOST`) without checking if they exist. These variables are only available when PHP runs under a web server (Apache/Nginx), but not when executing PHP scripts from the command line (CLI).

**Specific Issues:**
- Line 7: `$_SERVER['SERVER_PORT']` - Undefined in CLI
- Line 8: `$_SERVER['HTTP_HOST']` - Undefined in CLI

## Fix Plan Steps

### Step 1: Fix getBaseUrl() Function in config.php
- Replace direct array access with null coalescing operators
- Add CLI detection and fallback handling
- Provide sensible defaults for CLI environment

### Step 2: Update Environment Variable Access
- Use `$_SERVER['KEY'] ?? 'default_value'` pattern throughout
- Add context-aware defaults (CLI vs web)
- Maintain backward compatibility for web usage

### Step 3: Add Environment Detection
- Create helper function to detect CLI vs web context
- Provide appropriate base URL defaults for each environment
- Ensure URL helpers work in both contexts

### Step 4: Test CLI and Web Functionality
- Test CLI scripts don't generate warnings
- Verify web application still works correctly
- Test URL generation in both contexts

### Step 5: Update Related Functions
- Review other functions that might access $_SERVER
- Apply consistent null coalescing pattern
- Add error handling for missing environment variables

## Expected Code Changes
- Update `getBaseUrl()` with proper null checking
- Add CLI environment detection
- Provide fallback values for missing $_SERVER keys
- Ensure all URL helpers work in CLI context

This approach will eliminate the warnings while maintaining full functionality in both web and CLI environments.
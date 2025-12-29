# Production Deployment Guide

## Overview
This guide explains how to deploy the RMW Scan application to production without git stash conflicts.

## 🏆 **Best Practice Solution: Environment Variables (.env files)**

### **Industry Standard Approach**
Using `.env` files is the most widely adopted solution for managing environment-specific configurations.

### **Deployment Steps (Best Practice)**

1. **On Production Server:**
   ```bash
   # Copy production environment file
   cp .env.production .env
   
   # Verify configuration
   php test_env.php
   ```

2. **Pull Latest Code:**
   ```bash
   git pull origin main
   ```

3. **Test Application:**
   ```bash
   php test_env.php
   ```

### **Environment File Options:**

**Option A: Use Pre-defined Files (Easiest)**
- Development: `cp .env.development .env`
- Production: `cp .env.production .env`

**Option B: Custom Configuration**
```bash
# Copy template
cp .env.example .env

# Edit for your environment
nano .env
```

### **Environment File Structure:**
```bash
# .env (production example)
APP_ENV=production
DB_TYPE=mysql
DB_MYSQL_HOST=36.92.174.141:3333
DB_MYSQL_NAME=rmw_system
DB_MYSQL_USER=<YOUR_DB_USER>
DB_MYSQL_PASS=<YOUR_DB_PASSWORD>

## **Alternative Solutions (Fallback Options)**

### Solution 2: Automatic Environment Detection
The `config.php` includes automatic detection as fallback:

**Production Detection:**
- HTTP_HOST contains `36.92.174.141`
- SERVER_ADDR is `36.92.174.141`

**Automatic Database Settings:**
- **Default**: `36.92.174.141:3333` (remote MySQL)
- **Override**: Set `DB_MYSQL_HOST` environment variable if needed

### Solution 3: Local Configuration Override
Create a `config.local.php` file for manual override:

**Steps:**
1. Copy the example file:
   ```bash
   cp config.local.example.php config.local.php
   ```

2. Edit `config.local.php` for production:
   ```php
   <?php
   // Production Database Settings
   define('LOCAL_DB_MYSQL_HOST', '36.92.174.141:3333');
   define('LOCAL_DB_MYSQL_NAME', 'rmw_system');
   define('LOCAL_DB_MYSQL_USER', '<YOUR_DB_USER>');
   define('LOCAL_DB_MYSQL_PASS', '<YOUR_DB_PASSWORD>');
   define('LOCAL_APP_ENV', 'production');
   ?>
   ```

## Deployment Process

### Without Git Stash (Recommended)

1. **Ensure production has local config:**
   ```bash
   # On production server
   cp config.local.example.php config.local.php
   # Edit config.local.php with local database settings
   ```

2. **Pull latest changes:**
   ```bash
   git pull origin main
   ```

3. **Test configuration:**
   ```bash
   php test_env.php
   ```

### With Git Stash (Alternative)

1. **Stash local changes:**
   ```bash
   git stash push -m "Local production config"
   ```

2. **Pull latest changes:**
   ```bash
   git pull origin main
   ```

3. **Restore local changes:**
   ```bash
   git stash pop
   ```

## Testing Database Connection

Run the environment test script to verify configuration:

```bash
php test_env.php
```

**Expected Output:**
```
=== Environment Detection Test ===
HTTP_HOST: 36.92.174.141
SERVER_ADDR: 36.92.174.141
APP_ENV: production
DB_MYSQL_HOST: 36.92.174.141:3333
DB_MYSQL_NAME: rmw_system
DB_MYSQL_USER: endang
DB_TYPE: mysql
✅ Database connection: SUCCESS
👥 Users in database: XX
```

## Troubleshooting

### Connection Issues
- Check MySQL service is running: `systemctl status mysql`
- Verify MySQL port: `netstat -tlnp | grep :3306`
- Test MySQL connection: `mysql -h 36.92.174.141 -P 3333 -u endang -p rmw_system`

### Configuration Issues
- Verify `config.local.php` exists and has correct permissions
- Check PHP error logs: `/var/log/php_errors.log`
- Test environment detection: `php -r "require 'config.php'; echo APP_ENV; echo DB_MYSQL_HOST;"`

## Security Notes

- `config.local.php` is gitignored and won't be committed
- Ensure production MySQL has proper user permissions
- Consider using different passwords for production vs development
- Keep database credentials secure and don't expose them in version control

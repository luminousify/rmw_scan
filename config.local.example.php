<?php
/**
 * Local Configuration Override
 * 
 * Copy this file to config.local.php and modify for your environment.
 * This file is gitignored and will override config.php settings.
 * 
 * Usage:
 * 1. Copy config.local.example.php to config.local.php
 * 2. Modify the database settings below
 * 3. config.local.php will be automatically loaded by config.php
 */

// Local Database Configuration
define('LOCAL_DB_MYSQL_HOST', '127.0.0.1:3306');  // Override for local MySQL
define('LOCAL_DB_MYSQL_NAME', 'rmw_system');
define('LOCAL_DB_MYSQL_USER', 'endang');
define('LOCAL_DB_MYSQL_PASS', 'endangthea0');

// Override application environment if needed
define('LOCAL_APP_ENV', 'production');

?>

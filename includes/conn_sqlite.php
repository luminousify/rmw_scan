<?php
/**
 * Legacy SQLite Database Connection Wrapper
 * 
 * This file now routes to the unified connection handler (conn.php)
 * for backward compatibility with existing includes.
 */

// Simply include the unified connection handler
require_once __DIR__ . '/conn.php';

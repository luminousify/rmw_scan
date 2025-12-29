<?php
    require_once '../config.php';
    session_start();
    session_unset();
    session_destroy();
    // Also clear the cookie for this app's session name to avoid "sticky" cookies in browser.
    if (function_exists('session_name')) {
        $sn = session_name();
        if (!headers_sent()) {
            setcookie($sn, '', time() - 3600, '/rmw_scan', '', false, true);
        }
    }
    header('Location: ' . url());
    exit();
?>


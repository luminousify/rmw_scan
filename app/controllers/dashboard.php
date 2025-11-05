<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url());
    exit();
}

$module_name = "dashboard";
$title = "Dashboard";
$name = $_SESSION['user'];
$pass = $_SESSION['pass'];
$idlog = $_SESSION['idlog'];
$department = $_SESSION['department'] ?? 'production';

// Get user's division
require_once '../../includes/DatabaseManager.php';
$db = DatabaseManager::getInstance();
$stmt = $db->query("SELECT division FROM users WHERE id = ?", [$idlog]);
$userDivision = $stmt->fetchColumn();

include '../common/header.php';
include '../dash.php';
include '../common/footer.php';
?>
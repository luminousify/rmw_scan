<?php
session_start();

require_once __DIR__ . '/../includes/DatabaseManager.php';
require_once __DIR__ . '/../includes/services/DivisionService.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Check if user has admin or rmw privileges
$userDepartment = $_SESSION['department'] ?? '';
if (!in_array($userDepartment, ['admin', 'rmw'])) {
    header('Location: dash.php');
    exit;
}

$divisionService = new DivisionService();
$groupedUsers = $divisionService->getUsersGroupedByDivision();
$statistics = $divisionService->getDivisionStatistics();

$name = $_SESSION['full_name'] ?? 'User';
$department = $_SESSION['department'] ?? 'production';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Division Management - <?= APP_NAME ?></title>
    <link href="../includes/css/main.css" rel="stylesheet">
    <link href="../includes/css/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        .base-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: transform 0.2s; }
        .card-hover:hover { transform: translateY(-5px); }
        .division-card { border-left: 4px solid; }
        .division-card.production { border-left-color: #0d6efd; }
        .division-card.rmw { border-left-color: #198754; }
        .division-card.admin { border-left-color: #ffc107; }
        .user-item { padding: 10px; border-radius: 8px; margin-bottom: 5px; background: #f8f9fa; }
        .user-item:hover { background: #e9ecef; }
    </style>
</head>
<body>
    <?php include 'common/header.php'; ?>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1"><i class="bi bi-diagram-3"></i> Division Management</h2>
                        <p class="text-muted mb-0">Manage divisions within departments</p>
                    </div>
                    <div>
                        <a href="dash.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <?php foreach ($statistics as $stat): ?>
            <div class="col-md-3">
                <div class="card card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= ucfirst($stat['department']) ?></h6>
                                <h4 class="mb-0"><?= htmlspecialchars($stat['division']) ?></h4>
                                <small class="text-muted"><?= $stat['user_count'] ?> user(s)</small>
                            </div>
                            <div class="text-<?= $stat['department'] === 'production' ? 'primary' : ($stat['department'] === 'rmw' ? 'success' : 'warning') ?>">
                                <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Division Management -->
        <div class="row">
            <?php if (!empty($groupedUsers)): ?>
                <?php foreach ($groupedUsers as $dept => $divisions): ?>
                <div class="col-12 mb-4">
                    <div class="card division-card <?= $dept ?>">
                        <div class="card-header base-blue text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-building"></i>
                                <?= ucfirst($dept) ?> Department
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($divisions)): ?>
                                <?php foreach ($divisions as $divisionName => $users): ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">
                                            <i class="bi bi-diagram-2"></i>
                                            Division: <?= htmlspecialchars($divisionName) ?>
                                            <span class="badge bg-secondary ms-2"><?= count($users) ?> users</span>
                                        </h6>
                                    </div>

                                    <div class="row">
                                        <?php foreach ($users as $user): ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="user-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            @<?= htmlspecialchars($user['username']) ?>
                                                        </small>
                                                        <?php if (!empty($user['email'])): ?>
                                                        <br>
                                                        <small><?= htmlspecialchars($user['email']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-end">
                                                        <?php if ($user['is_active']): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactive</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No users in this department</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No users found in the system.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../includes/js/jquery-3.7.0.min.js"></script>
    <script>
        // Add any interactive features here
        console.log('Division Management Page Loaded');
    </script>
</body>
</html>

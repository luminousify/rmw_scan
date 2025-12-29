<?php
require_once '../../config.php';
require_once '../../includes/DatabaseManager.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url() . '?error=unauthorized');
    exit();
}

// Standard session variable setup (like rmw_dashboard.php)
$module_name = "settings";
$title = "Settings";
$name = $_SESSION['user'];
$idlog = $_SESSION['idlog'];
$department = $_SESSION['department'];

// Initialize variables
$current_password = $new_password = $confirm_password = '';
$errors = [];
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate form data
    if (empty($current_password)) {
        $errors[] = 'Current password is required';
    }

    if (empty($new_password)) {
        $errors[] = 'New password is required';
    } elseif (strlen($new_password) < 8) {
        $errors[] = 'New password must be at least 8 characters long';
    }

    if (empty($confirm_password)) {
        $errors[] = 'Please confirm your new password';
    } elseif ($new_password !== $confirm_password) {
        $errors[] = 'New password and confirmation do not match';
    }

    // If no validation errors, proceed with password change
    if (empty($errors)) {
        try {
            // Get database connection
            $db = DatabaseManager::getInstance()->getConnection();
            
            // Get current user data
            $username = $_SESSION['user'] ?? '';
            $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $errors[] = 'User not found';
            } else {
                // Verify current password using plain text comparison (like login system)
                if ($current_password !== $user['password']) {
                    $errors[] = 'Current password is incorrect';
                } else {
                    // Update password as plain text (maintaining consistency with login system)
                    $update_stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                    
                    if ($update_stmt->execute([$new_password, $user['id']])) {
                        $success_message = 'Password changed successfully!';
                        // Clear form fields
                        $current_password = $new_password = $confirm_password = '';
                    } else {
                        $errors[] = 'Failed to update password. Please try again.';
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Database error in settings: " . $e->getMessage());
            $errors[] = 'A database error occurred. Please try again.';
        }
    }
}

// Get user's division (exact same pattern as rmw_dashboard.php)
require_once '../../includes/DatabaseManager.php';
$db = DatabaseManager::getInstance();
$stmt = $db->query("SELECT division FROM users WHERE id = ?", [$idlog]);
$userDivision = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?= ($department === 'rmw' ? 'RMW System' : 'Scan No Bon') ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo url('includes/css/output.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo url('includes/css/bootstrap-icons/bootstrap-icons.css'); ?>">
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?php echo url('app/controllers/dashboard.php'); ?>" class="text-xl font-bold text-gray-900">
                        <?= ($department === 'rmw' ? 'RMW System' : 'Scan No Bon') ?>
                    </a>
                </div>
                
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- User info -->
                    <span class="text-sm font-medium text-gray-700"><?=strtoupper($name)?> (<?=ucfirst($department ?? 'rmw')?>)</span>
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button class="p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100" onclick="toggleDropdown()">
                            <i class="bi bi-person text-xl"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                            <a href="<?php echo url('app/controllers/settings.php'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="bi bi-gear mr-2"></i>
                                Settings
                            </a>
                            <a href="<?php echo url('app/logout.php'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="bi bi-box-arrow-right mr-2"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <?php include '../common/sidebar.php'; ?>

        <!-- Main content -->
        <main class="flex-1 p-6">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
                <p class="text-gray-600 mt-2">Manage your account settings</p>
            </div>

            <!-- Alert Messages -->
            <?php if (!empty($errors)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <i class="bi bi-exclamation-circle text-red-400 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Error!</h3>
                        <ul class="text-sm text-red-700 mt-1">
                            <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <i class="bi bi-check-circle text-green-400 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-green-800">Success!</h3>
                        <p class="text-sm text-green-700 mt-1"><?= htmlspecialchars($success_message) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Settings Form -->
            <div class="max-w-2xl">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Change Password</h3>
                    </div>
                    
                    <form method="POST" class="p-6 space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="current_password" 
                                    name="current_password" 
                                    value="<?= htmlspecialchars($current_password) ?>"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 pr-10"
                                    placeholder="Enter your current password"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePasswordVisibility('current_password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <i class="bi bi-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password" 
                                    value="<?= htmlspecialchars($new_password) ?>"
                                    required
                                    minlength="8"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 pr-10"
                                    placeholder="Enter your new password"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePasswordVisibility('new_password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <i class="bi bi-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Must be at least 8 characters long
                            </p>
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    value="<?= htmlspecialchars($confirm_password) ?>"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 pr-10"
                                    placeholder="Confirm your new password"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePasswordVisibility('confirm_password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <i class="bi bi-eye" id="confirm_password_icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <a 
                                href="<?php echo url('app/controllers/dashboard.php'); ?>" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                            >
                                Cancel
                            </a>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <i class="bi bi-check-circle mr-2"></i>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.onclick || button.onclick.toString().indexOf('toggleDropdown') === -1) {
                dropdown.classList.add('hidden');
            }
        });

        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (password.length >= 16) strength++;
            
            // You can add visual feedback here if needed
        });
    </script>
</body>
</html>

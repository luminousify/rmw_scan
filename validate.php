<?php
require_once 'config.php';

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

// Database authentication (MySQL only)
$authSuccess = false;

// Try MySQL
if (in_array('mysql', PDO::getAvailableDrivers())) {
  
    include 'includes/conn_mysql.php';
    $query = "SELECT * FROM users WHERE username=:user AND password=:pass AND is_active=1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user', $user, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($results) {
      session_start();
      $_SESSION['loggedin'] = true;
      $_SESSION['user'] = $results['username'];
      $_SESSION['pass'] = $results['password'];
      $_SESSION['idlog'] = $results['id'];
      $_SESSION['department'] = $results['department'];
      $_SESSION['full_name'] = $results['full_name'];
      
      // Redirect based on department
      if ($results['department'] === 'production') {
        header("Location: " . url('app/controllers/material_request.php'));
      } else if ($results['department'] === 'rmw') {
        header("Location: " . url('app/controllers/rmw_dashboard.php'));
      } else {
        // Default fallback for other departments
        header("Location: " . url('app/controllers/dashboard.php'));
      }
      $authSuccess = true;
      exit();
    }
  } catch (Exception $e) {
    error_log("MySQL authentication failed: " . $e->getMessage());
  }
}

// Authentication failed
if (!$authSuccess) {
  header("Location: " . url() . "?error=invalid_credentials");
}
?>


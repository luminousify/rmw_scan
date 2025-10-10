<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Tailwind CSS -->
  <link rel="stylesheet" type="text/css" href="<?php echo url('includes/css/output.css'); ?>">
  <!-- Font-icon css-->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <title><?= "CKU SCAN-NO-BON" ?></title>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
  <!-- Login Container -->
  <div class="w-full max-w-md space-y-8">
    <!-- Logo Section -->
    <div class="text-center">
      <img src="<?php echo url('includes/img/logo-soode.png'); ?>" alt="Logo" class="mx-auto h-16 w-auto mb-6">
      <?php 
      $ket = "";
      if (isset($_GET['error'])) {
        switch($_GET['error']) {
          case 'invalid_credentials':
            $ket = "Invalid username or password. Please try again.";
            break;
          default:
            $ket = "Login error occurred. Please try again.";
        }
      }
      ?>
    </div>
    
    <!-- Connection Status Toggle -->
    <div class="flex flex-col items-center space-y-2">
      <div id="connectionToggle" class="connection-toggle">
        <div class="circle"></div>
      </div>
      <p id="statusText" class="text-sm text-gray-600">Checking connection...</p>
    </div>
    
    <!-- Login Form Card -->
    <div class="bg-white rounded-2xl shadow-xl border-4 border-blue-500 p-8">
      <form class="space-y-6" action="<?php echo url('validate.php'); ?>" method="post">
        <div class="text-center">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center justify-center gap-2">
            <i class="bi bi-person"></i>
            SCAN NO BON
          </h2>
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">USERNAME</label>
            <input 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
              type="text" 
              name="username" 
              placeholder="Username" 
              autofocus 
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">PASSWORD</label>
            <input 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
              type="password" 
              name="password" 
              placeholder="Password" 
              required
            >
          </div>
        </div>
        
        <?php if ($ket): ?>
        <div class="text-center">
          <p class="text-sm text-red-600"><i><?= $ket ?></i></p>
        </div>
        <?php endif; ?>
        
        <div class="pt-4">
          <button 
            type="submit" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl"
          >
            <i class="bi bi-box-arrow-in-right"></i>
            SIGN IN
          </button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Essential javascripts for application to work-->
  <script src="<?php echo url('includes/js/jquery-3.7.0.min.js'); ?>"></script>
  <script type="text/javascript">
    // Function to check connection by calling the endpoint.
    function checkConnection() {
      fetch('<?php echo url('check_connection.php'); ?>')
        .then(response => {
          // Check if response is ok
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          
          // Check if response is actually JSON
          const contentType = response.headers.get('content-type');
          if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON');
          }
          
          return response.json();
        })
        .then(data => {
          const toggle = document.getElementById('connectionToggle');
          const statusText = document.getElementById('statusText');
          
          if (data.status === 'ok') {
            // Add 'on' class to change toggle color to green
            toggle.classList.add('on');
            statusText.textContent = "Connected";
          } else {
            // Remove 'on' class to keep toggle red
            toggle.classList.remove('on');
            statusText.textContent = "Not Connected" + (data.error ? ": " + data.error : "");
          }
        })
        .catch(error => {
          const toggle = document.getElementById('connectionToggle');
          toggle.classList.remove('on');
          document.getElementById('statusText').textContent = "Connection Error";
          console.error('Connection check error:', error);
        });
    }

    // Check connection as soon as the page loads
    document.addEventListener('DOMContentLoaded', checkConnection);
    
    // Optional: allow user to click the toggle to re-check the connection
    document.getElementById('connectionToggle').addEventListener('click', checkConnection);
  </script>
</body>

</html>

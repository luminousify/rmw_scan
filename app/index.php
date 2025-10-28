<?php
require_once '../config.php';

// Generate CSRF token for security
if (!isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Tailwind CSS -->
  <link rel="stylesheet" type="text/css" href="<?php echo url('includes/css/output.css'); ?>">
  <!-- Font-icon css-->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <title><?= "CKU SCAN-NO-BON" ?></title>
  <style>
    
    
    /* Loading spinner */
    .spinner {
      border: 2px solid #f3f3f3;
      border-top: 2px solid #3b82f6;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
  <!-- Login Container -->
  <main class="w-full max-w-md space-y-8" role="main">
    <!-- Logo Section -->
    <header class="text-center">
      <img src="<?php echo url('includes/img/logo-soode.png'); ?>" alt="Company Logo" class="mx-auto h-16 w-auto mb-6">
      <?php 
      $ket = "";
      $error_type = "";
      if (isset($_GET['error'])) {
        $error_type = $_GET['error'];
        switch($error_type) {
          case 'invalid_credentials':
            $ket = "Username atau password salah. Silakan coba lagi.";
            break;
          case 'csrf_invalid':
            $ket = "Token keamanan kadaluarsa. Silakan refresh halaman dan coba lagi.";
            break;
          case 'rate_limit':
            $ket = "Terlalu banyak percobaan login. Silakan tunggu sebelum mencoba lagi.";
            break;
          default:
            $ket = "Terjadi kesalahan login. Silakan coba lagi.";
        }
      }
      ?>
    </header>
    
    
    
    <!-- Login Form Card -->
    <section class="bg-white rounded-2xl shadow-xl border-4 border-blue-500 p-8" role="form" aria-labelledby="login-heading">
      <form 
        id="loginForm"
        class="space-y-6" 
        action="<?php echo url('validate.php'); ?>" 
        method="post" 
        novalidate
        aria-describedby="error-message"
      >
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="text-center">
          <h2 id="login-heading" class="text-2xl font-bold text-gray-900 flex items-center justify-center gap-2">
            <i class="bi bi-person" aria-hidden="true"></i>
            SCAN NO BON
          </h2>
        </div>
        
        <?php if ($ket): ?>
        <div 
          id="error-message" 
          class="text-center p-3 bg-red-50 border border-red-200 rounded-lg"
          role="alert"
          aria-live="assertive"
        >
          <p class="text-sm text-red-600">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <?= htmlspecialchars($ket) ?>
          </p>
        </div>
        <?php endif; ?>
        
        <div class="space-y-4">
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
              NAMA PENGGUNA <span class="text-red-500" aria-label="required">*</span>
            </label>
            <div class="relative">
              <input 
                id="username"
                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                type="text" 
                name="username" 
                placeholder="Masukkan nama pengguna" 
                autocomplete="username"
                aria-required="true"
                aria-describedby="username-error"
                required
              >
              <i class="bi bi-person absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
            </div>
            <div id="username-error" class="mt-1 text-sm text-red-600 hidden" role="alert"></div>
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              KATA SANDI <span class="text-red-500" aria-label="required">*</span>
            </label>
            <div class="relative">
              <input 
                id="password"
                class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                type="password" 
                name="password" 
                placeholder="Masukkan kata sandi"
                autocomplete="current-password"
                aria-required="true"
                aria-describedby="password-error"
                required
              >
              <i class="bi bi-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
              <button 
                type="button"
                id="togglePassword"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                aria-label="Tampilkan kata sandi"
              >
                <i id="passwordIcon" class="bi bi-eye" aria-hidden="true"></i>
              </button>
            </div>
            <div id="password-error" class="mt-1 text-sm text-red-600 hidden" role="alert"></div>
          </div>
          
          
        </div>
        
        <div class="pt-4">
          <button 
            type="submit" 
            id="submitBtn"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
            aria-describedby="submit-help"
          >
            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
            <span id="submit-text">MASUK</span>
            <div id="submit-spinner" class="spinner hidden" aria-hidden="true"></div>
          </button>
          <p id="submit-help" class="mt-2 text-xs text-gray-500 text-center hidden"></p>
        </div>
      </form>
    </section>
  </main>
  
  <!-- Essential javascripts for application to work-->
  <script src="<?php echo url('includes/js/jquery-3.7.0.min.js'); ?>"></script>
  <script type="text/javascript">
    // Form validation and enhancement
    class LoginFormEnhancer {
      constructor() {
        this.form = document.getElementById('loginForm');
        this.usernameInput = document.getElementById('username');
        this.passwordInput = document.getElementById('password');
        this.submitBtn = document.getElementById('submitBtn');
        this.submitText = document.getElementById('submit-text');
        this.submitSpinner = document.getElementById('submit-spinner');
        this.submitHelp = document.getElementById('submit-help');
        this.passwordToggle = document.getElementById('togglePassword');
        this.passwordIcon = document.getElementById('passwordIcon');
        
        this.init();
      }
      
      init() {
        // Password toggle functionality
        this.passwordToggle.addEventListener('click', () => this.togglePassword());
        
        // Real-time validation
        this.usernameInput.addEventListener('input', () => this.validateUsername());
        this.usernameInput.addEventListener('blur', () => this.validateUsername());
        
        this.passwordInput.addEventListener('input', () => {
          this.validatePassword();
        });
        this.passwordInput.addEventListener('blur', () => this.validatePassword());
        
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Clear errors on input
        this.usernameInput.addEventListener('input', () => this.clearError('username'));
        this.passwordInput.addEventListener('input', () => this.clearError('password'));
        
        // Focus management
        this.usernameInput.focus();
      }
      
      togglePassword() {
        const type = this.passwordInput.type === 'password' ? 'text' : 'password';
        this.passwordInput.type = type;
        
        // Update icon and aria-label
        if (type === 'text') {
          this.passwordIcon.className = 'bi bi-eye-slash';
          this.passwordToggle.setAttribute('aria-label', 'Sembunyikan kata sandi');
        } else {
          this.passwordIcon.className = 'bi bi-eye';
          this.passwordToggle.setAttribute('aria-label', 'Tampilkan kata sandi');
        }
      }
      
      validateUsername() {
        const username = this.usernameInput.value.trim();
        const errorElement = document.getElementById('username-error');
        
        if (username.length < 3) {
          this.showError('username', 'Nama pengguna harus minimal 3 karakter');
          return false;
        }
        
        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
          this.showError('username', 'Nama pengguna hanya boleh mengandung huruf, angka, dan garis bawah');
          return false;
        }
        
        this.clearError('username');
        return true;
      }
      
      validatePassword() {
        const password = this.passwordInput.value;
        const errorElement = document.getElementById('password-error');
        
        if (password.length < 6) {
          this.showError('password', 'Kata sandi harus minimal 6 karakter');
          return false;
        }
        
        this.clearError('password');
        return true;
      }
      
      
      
      showError(field, message) {
        const errorElement = document.getElementById(`${field}-error`);
        const inputElement = document.getElementById(field);
        
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
        inputElement.classList.add('border-red-500', 'focus:ring-red-500');
        inputElement.classList.remove('border-gray-300', 'focus:ring-blue-500');
        inputElement.setAttribute('aria-invalid', 'true');
      }
      
      clearError(field) {
        const errorElement = document.getElementById(`${field}-error`);
        const inputElement = document.getElementById(field);
        
        errorElement.classList.add('hidden');
        inputElement.classList.remove('border-red-500', 'focus:ring-red-500');
        inputElement.classList.add('border-gray-300', 'focus:ring-blue-500');
        inputElement.setAttribute('aria-invalid', 'false');
      }
      
      async handleSubmit(e) {
        e.preventDefault();
        
        // Validate form
        const isUsernameValid = this.validateUsername();
        const isPasswordValid = this.validatePassword();
        
        if (!isUsernameValid || !isPasswordValid) {
          this.submitHelp.textContent = 'Perbaiki kesalahan di atas';
          this.submitHelp.classList.remove('hidden');
          this.submitHelp.classList.add('text-red-600');
          return;
        }
        
        // Show loading state
        this.setLoadingState(true);
        
        try {
          // Simulate network request (in real implementation, this would be the actual form submission)
          await this.submitForm();
        } catch (error) {
          this.handleSubmitError(error);
        } finally {
          this.setLoadingState(false);
        }
      }
      
      setLoadingState(isLoading) {
        if (isLoading) {
          this.submitBtn.disabled = true;
          this.submitText.textContent = 'Sedang masuk...';
          this.submitSpinner.classList.remove('hidden');
          this.submitHelp.textContent = 'Mohon tunggu sambil kami memverifikasi kredensial Anda';
          this.submitHelp.classList.remove('hidden', 'text-red-600');
          this.submitHelp.classList.add('text-gray-500');
        } else {
          this.submitBtn.disabled = false;
          this.submitText.textContent = 'MASUK';
          this.submitSpinner.classList.add('hidden');
        }
      }
      
      async submitForm() {
        // Create form data
        const formData = new FormData(this.form);
        
        // Submit the form normally (this will cause a page reload)
        this.form.submit();
      }
      
      handleSubmitError(error) {
        this.submitHelp.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
        this.submitHelp.classList.remove('text-gray-500');
        this.submitHelp.classList.add('text-red-600');
        console.error('Form submission error:', error);
      }
    }
    
    
    
    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
      const loginForm = new LoginFormEnhancer();
    });
  </script>
</body>

</html>

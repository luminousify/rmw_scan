<body class="min-h-screen bg-gray-50">
    <!-- Navbar-->
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
            <span class="text-sm font-medium text-gray-700"><?=strtoupper($name)?> (<?=ucfirst($department ?? 'production')?>)</span>
            
            <!-- User Menu-->
            <div class="relative">
              <button class="p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100" onclick="toggleDropdown()">
                <i class="bi bi-person text-xl"></i>
              </button>
              
              <!-- Dropdown Menu -->
              <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
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
      <!-- Sidebar -->
      <aside class="w-64 bg-white shadow-lg min-h-screen border-r border-gray-200 transition-all duration-300">
        <div class="p-6">
          <!-- Enhanced Logo Area -->
          <div class="flex flex-col items-center space-y-4 mb-8 pb-6 border-b border-gray-100">
            <div class="relative">
              <div class="w-16 h-16 <?= ($department === 'rmw' ? 'bg-gradient-to-br from-green-500 to-green-600' : 'bg-gradient-to-br from-blue-500 to-blue-600') ?> rounded-2xl flex items-center justify-center shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                <i class="<?= ($department === 'rmw' ? 'bi bi-box-seam' : 'bi bi-building') ?> text-white text-2xl"></i>
              </div>
              <div class="absolute -bottom-1 -right-1 w-6 h-6 <?= ($department === 'rmw' ? 'bg-green-500' : 'bg-blue-500') ?> rounded-full flex items-center justify-center">
                <i class="<?= ($department === 'rmw' ? 'bi bi-check2' : 'bi bi-person') ?> text-white text-xs"></i>
              </div>
            </div>
            <div class="text-center">
              <h3 class="text-lg font-bold text-gray-900 capitalize">
                <?= ($department === 'rmw' ? 'Raw Material Warehouse' : 'Production Department') ?>
              </h3>
              <p class="text-sm text-gray-500 mt-1">Management System</p>
            </div>
          </div>
          
          <!-- Enhanced Navigation -->
          <nav class="space-y-1" role="navigation" aria-label="Main navigation">
            <!-- Dashboard -->
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" 
               class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'dashboard' ? 'nav-active' : 'nav-inactive' ?>"
               role="menuitem" aria-current="<?= $module_name == 'dashboard' ? 'page' : 'false' ?>">
              <i class="bi bi-house-fill mr-3 text-lg"></i>
              <span>Dashboard</span>
              <?= $module_name == 'dashboard' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
            </a>
            
            <?php if ($department === 'production'): ?>
            <!-- Production Navigation -->
            <div class="pt-2">
              <h4 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Production</h4>
              
              <a href="<?php echo url('app/controllers/material_request.php'); ?>" 
                 class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'material_request' ? 'nav-active' : 'nav-inactive' ?>"
                 role="menuitem" aria-current="<?= $module_name == 'material_request' ? 'page' : 'false' ?>">
                <i class="bi bi-plus-circle-fill mr-3 text-lg"></i>
                <span>Create Request</span>
                <?= $module_name == 'material_request' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
              </a>
              
              <a href="<?php echo url('app/controllers/my_requests.php'); ?>" 
                 class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'my_requests' ? 'nav-active' : 'nav-inactive' ?>"
                 role="menuitem" aria-current="<?= $module_name == 'my_requests' ? 'page' : 'false' ?>">
                <i class="bi bi-list-task mr-3 text-lg"></i>
                <span>My Requests</span>
                <?= $module_name == 'my_requests' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
              </a>
            </div>
            
            <?php else: ?>
            <!-- RMW Navigation -->
            <div class="pt-2">
              <h4 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Warehouse</h4>
              
              <a href="<?php echo url('app/controllers/rmw_dashboard.php'); ?>" 
                 class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'rmw_dashboard' ? 'nav-active-rmw' : 'nav-inactive' ?>"
                 role="menuitem" aria-current="<?= $module_name == 'rmw_dashboard' ? 'page' : 'false' ?>">
                <i class="bi bi-box-seam-fill mr-3 text-lg"></i>
                <span>Warehouse Dashboard</span>
                <?= $module_name == 'rmw_dashboard' ? '<div class="absolute right-2 w-2 h-2 bg-green-600 rounded-full animate-pulse"></div>' : '' ?>
              </a>
            </div>
            <?php endif; ?>
            
  
          </nav>
        </div>
      </aside>

      <!-- Main content -->
      <main class="flex-1 p-6">
        <!-- Page Header -->
        <div class="mb-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <i class="bi bi-upc-scan text-white text-xl"></i>
                </div>
                <div>
                  <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Material Scanner</h1>
                  <p class="text-gray-600 mt-1">Scan and verify customer reference materials</p>
                </div>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <div class="text-sm text-gray-500 flex items-center">
                <i class="bi bi-clock mr-1" aria-hidden="true"></i>
                Last updated: <span id="lastUpdated"><?= date('H:i') ?></span>
              </div>
              <button onclick="refreshPage()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Refresh">
                <i class="bi bi-arrow-clockwise"></i>
              </button>
            </div>
          </div>
        </div>

    







      <!-- Loading Overlay -->
      <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex flex-col items-center space-y-4">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
          <p class="text-gray-600 font-medium">Processing...</p>
        </div>
      </div>

      <!-- Toast Container -->
      <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

      <!-- Scanner Content -->
      <div class="space-y-6">

      <!-- Request Number Alert (shown when request number is passed from URL) -->
      <?php if (!empty($_GET['request_number']) && $nobon != ''): ?>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 sm:p-6 animate-fade-in">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                <i class="bi bi-upc-scan text-white" aria-hidden="true"></i>
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-sm font-medium text-blue-900">Active Request</h3>
              <p class="text-lg font-semibold text-blue-900 mt-1">
                <?= htmlspecialchars($nobon) ?>
              </p>
              <p class="text-sm text-blue-700 mt-1">Scanning materials for this production request</p>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
  
            <a href="<?php echo url('app/controllers/scanner.php'); ?>" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
              <i class="bi bi-arrow-clockwise mr-2" aria-hidden="true"></i>
              New Scan
            </a>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Scanner Card -->
      <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="bi bi-upc-scan text-white text-lg"></i>
              </div>
              <div>
                <h2 class="text-xl font-semibold text-gray-900">Material Scanner</h2>
                <p class="text-sm text-gray-600">Scan customer reference QR codes</p>
              </div>
            </div>
            <?php if ($dat!=""): ?>
            <div class="flex items-center space-x-3">
              <div class="text-right">
                <div class="text-sm text-gray-500">Current Scan</div>
                <div class="text-lg font-semibold text-gray-900"><?=$nobon?></div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        </div>

        <!-- Warning Message for non-diproses status -->
          <?php if (isset($warning_message)): ?>
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4 animate-fade-in">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-yellow-600 rounded-full flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle text-white"></i>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-yellow-900">Status Warning</h3>
                  <p class="text-yellow-800 mt-1"><?= htmlspecialchars($warning_message) ?></p>
                </div>
              </div>
              <div class="flex-shrink-0">
                <a href="<?php echo url('app/controllers/my_requests.php'); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium transition-colors">
                  <i class="bi bi-list-task mr-2"></i>
                  View Requests
                </a>
              </div>
            </div>
          </div>
          <?php endif; ?>
          
          <!-- Info Message for requests with no materials -->
          <?php if (isset($info_message)): ?>
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 animate-fade-in">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="bi bi-info-circle text-white"></i>
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-blue-900">Request Information</h3>
                  <p class="text-blue-800 mt-1"><?= htmlspecialchars($info_message) ?></p>
                </div>
              </div>
              <div class="flex-shrink-0">
                <a href="<?php echo url('app/controllers/my_requests.php'); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                  <i class="bi bi-plus-circle mr-2"></i>
                  Add Materials
                </a>
              </div>
            </div>
          </div>
          <?php endif; ?>

        <!-- Enhanced Scanner Input -->
        <div class="p-6">
          <form name="form1" action="#" method="post" id="scannerForm">
            <?php if (!empty($currentRequestNumber)): ?>
            <input type="hidden" name="current_request_number" value="<?= htmlspecialchars($currentRequestNumber) ?>">
            <?php else: ?>
            <!-- Enhanced Request Number Selection -->
            <div class="mb-8">
              <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                <label class="block text-lg font-bold text-gray-900 mb-4 flex items-center">
                  <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="bi bi-clipboard-check text-white text-sm"></i>
                  </div>
                  Production Request Number
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="bi bi-clipboard-data text-gray-400 text-lg"></i>
                  </div>
                  <input 
                    id="request_number_input" 
                    type="text" 
                    class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-500 text-lg font-mono transition-all duration-200" 
                    name="request_number_input" 
                    placeholder="REQ-20241201-1234" 
                    required
                    oninput="validateRequestNumber(this)"
                  >
                  <div id="requestNumberValidation" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <div class="hidden" id="requestNumberStatus"></div>
                  </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                  <div class="text-sm text-gray-600 flex items-center">
                    <i class="bi bi-info-circle mr-2 text-blue-500"></i>
                    Enter the production request number to compare with RMW data
                  </div>
                  <div id="requestNumberFormat" class="text-xs text-gray-500">
                    Format: REQ-YYYYMMDD-XXXX
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
            
            <!-- Enhanced QR Code Scanner -->
            <div class="mb-8">
              <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                <label class="block text-lg font-bold text-gray-900 mb-4 flex items-center">
                  <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="bi bi-qr-code-scan text-white text-sm"></i>
                  </div>
                  Customer Reference QR Code
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="bi bi-box-seam text-gray-400 text-lg"></i>
                  </div>
                  <input 
                    id="lot_material_bc" 
                    type="text" 
                    class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900 placeholder-gray-500 text-lg font-mono transition-all duration-200" 
                    name="nobon" 
                    placeholder="Enter Customer Reference (e.g., INJ/FG/1887-1)" 
                    value="<?= htmlspecialchars($nobon) ?>"
                    required 
                    oninput="validateCustomerReference(this)"
                    <?php 
                    echo empty($nobon) ? 'autofocus' : '';
                    ?>
                  >
                  <div id="customerReferenceValidation" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <div class="hidden" id="customerReferenceStatus"></div>
                  </div>
                </div>
                <div class="mt-3 flex items-center justify-between">
                  <div class="text-sm text-gray-600 flex items-center">
                    <i class="bi bi-upc-scan mr-2 text-green-500"></i>
                    Scan or type RMW Customer Reference QR Code
                  </div>
                  <div id="scanStatus" class="text-xs text-gray-500">
                    Ready to scan
                  </div>
                </div>
                
                <!-- Scanner Status Indicator -->
                <div class="mt-4 flex items-center space-x-3">
                  <div class="flex items-center">
                    <div id="scannerIndicator" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="ml-2 text-sm text-gray-600">Scanner Active</span>
                  </div>
                  <button type="button" onclick="clearScannerInput()" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                    <i class="bi bi-x-circle mr-1"></i>
                    Clear
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
              <button 
                type="submit" 
                id="submitScan"
                class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg flex items-center justify-center space-x-3"
                onclick="showLoading()"
              >
                <i class="bi bi-search text-xl"></i>
                <span>Process Scanner Data</span>
              </button>
              <?php if (!empty($nobon)): ?>
              <?php endif; ?>
            </div>
            

          </form>
        </div>
        <!-- Material Request Details -->
        <?php if (!empty($dat)): ?>
        <!-- Request Header Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 sm:p-5 mb-4 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
              <i class="bi bi-clipboard-data mr-3 text-blue-600"></i>
              Material Request Details (Baseline)
            </h2>
            <div class="flex items-center space-x-2">
              <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                <?= count($dat) ?> Items
              </span>
              <a href="<?php echo url('app/controllers/my_requests.php'); ?>" class="px-4 py-1.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                <i class="bi bi-arrow-left mr-2"></i>Back to Requests
              </a>
            </div>
          </div>
          
          <?php if (isset($currentRequestNumber)): ?>
          <div class="bg-blue-100 border border-blue-200 rounded-lg p-3 mb-3">
            <div class="flex items-center text-sm text-blue-800">
              <i class="bi bi-info-circle mr-2"></i>
              This is the baseline material list for request <strong><?= htmlspecialchars($currentRequestNumber) ?></strong>. 
              Customer reference materials will be compared against these items.
            </div>
          </div>
          <?php endif; ?>
          
          <?php if (isset($requestDetails)): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
            <div class="bg-white rounded-lg p-3 border border-blue-100">
              <div class="flex items-center text-gray-500 mb-1">
                <i class="bi bi-upc-scan mr-2"></i>
                Request Number
              </div>
              <div class="font-mono font-semibold text-gray-900"><?= htmlspecialchars($requestDetails['request_number'] ?? 'N/A') ?></div>
            </div>
            
            <div class="bg-white rounded-lg p-3 border border-blue-100">
              <div class="flex items-center text-gray-500 mb-1">
                <i class="bi bi-flag mr-2"></i>
                Status
              </div>
              <div>
                <?php 
                $requestStatus = $requestDetails['status'] ?? 'pending';
                $requestStatusClass = ($requestStatus === 'pending') ? 'bg-yellow-100 text-yellow-800' : 
                                     (($requestStatus === 'diproses') ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800');
                ?>
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $requestStatusClass ?>">
                  <?= ucfirst(htmlspecialchars($requestStatus)) ?>
                </span>
              </div>
            </div>
            
            <div class="bg-white rounded-lg p-3 border border-blue-100">
              <div class="flex items-center text-gray-500 mb-1">
                <i class="bi bi-person mr-2"></i>
                Requested By
              </div>
              <div class="font-semibold text-gray-900"><?= htmlspecialchars($requestDetails['production_user'] ?? 'System') ?></div>
            </div>
            
            <?php if (!empty($requestDetails['customer_reference'] ?? '')): ?>
            <div class="bg-white rounded-lg p-3 border border-blue-100">
              <div class="flex items-center text-gray-500 mb-1">
                <i class="bi bi-tag mr-2"></i>
                Customer Reference
              </div>
              <div class="font-semibold text-gray-900"><?= htmlspecialchars($requestDetails['customer_reference'] ?? 'N/A') ?></div>
            </div>
            <?php endif; ?>
          </div>
          
          <?php if (!empty($requestDetails['notes'] ?? '')): ?>
          <div class="mt-3 bg-white rounded-lg p-3 border border-blue-100">
            <div class="flex items-center text-gray-500 mb-2 text-sm">
              <i class="bi bi-sticky-note mr-2"></i>
              Notes
            </div>
            <div class="text-gray-900"><?= htmlspecialchars($requestDetails['notes'] ?? '') ?></div>
          </div>
          <?php endif; ?>
          <?php endif; ?>
        </div>

        <!-- Enhanced Materials Table -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
          <!-- Table Header with Search -->
          <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="bi bi-box-seam text-blue-600"></i>
                  </div>
                  Requested Materials
                </h3>
                <p class="text-sm text-gray-600 mt-1">Baseline materials for comparison</p>
              </div>
              <div class="flex items-center space-x-3">
                <!-- Search Box -->
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                  </div>
                  <input 
                    type="text" 
                    id="materialSearch"
                    placeholder="Search materials..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    oninput="filterMaterials()"
                  >
                </div>
                <!-- Item Count -->
                <div class="bg-white px-3 py-2 rounded-lg border border-gray-200">
                  <span class="text-sm text-gray-600">Items: </span>
                  <span id="itemCount" class="text-sm font-semibold text-gray-900"><?= count($dat) ?></span>
                </div>
                <!-- Export Button -->
                <button 
                  onclick="exportMaterials()"
                  class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center space-x-2"
                >
                  <i class="bi bi-download"></i>
                  <span>Export</span>
                </button>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="materialsTable">
              <thead class="bg-gray-50 sticky top-0 z-10">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="flex items-center space-x-1">
                      <span>No.</span>
                      <button onclick="sortTable(0)" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-arrow-down-up text-xs"></i>
                      </button>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="flex items-center space-x-1">
                      <span>Product ID</span>
                      <button onclick="sortTable(1)" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-arrow-down-up text-xs"></i>
                      </button>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="flex items-center space-x-1">
                      <span>Product Name</span>
                      <button onclick="sortTable(2)" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-arrow-down-up text-xs"></i>
                      </button>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="flex items-center justify-center space-x-1">
                      <span>Quantity</span>
                      <button onclick="sortTable(4)" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-arrow-down-up text-xs"></i>
                      </button>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200" id="materialsTableBody">
                <?php $no = 1; foreach($dat as $d): ?>
                <tr class="hover:bg-gray-50 transition-colors material-row" data-searchable="<?php echo strtolower(htmlspecialchars($d['product_id'] ?? '') . ' ' . htmlspecialchars($d['product_name'] ?? '') . ' ' . htmlspecialchars($d['description'] ?? '')); ?>">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full text-xs font-semibold">
                      <?php echo $no++; ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <span class="text-sm font-mono font-semibold text-gray-900 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200">
                        <?php echo htmlspecialchars($d['product_id'] ?? 'N/A'); ?>
                      </span>
                      <button onclick="copyToClipboard('<?php echo htmlspecialchars($d['product_id'] ?? ''); ?>')" class="text-gray-400 hover:text-gray-600 transition-colors" title="Copy">
                        <i class="bi bi-clipboard text-xs"></i>
                      </button>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900 max-w-xs truncate" title="<?php echo htmlspecialchars($d['product_name'] ?? ''); ?>">
                      <?php echo htmlspecialchars($d['product_name'] ?? 'Unknown Product'); ?>
                    </div>
                    <?php if (!empty($d['description'])): ?>
                    <div class="text-xs text-gray-500 mt-1 max-w-xs truncate" title="<?php echo htmlspecialchars($d['description'] ?? ''); ?>">
                      <?php echo htmlspecialchars($d['description'] ?? ''); ?>
                    </div>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-500">
                    <div class="max-w-xs" title="<?php echo htmlspecialchars($d['description'] ?? ''); ?>">
                      <?php echo htmlspecialchars($d['description'] ?? 'No description'); ?>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <span class="text-lg font-bold text-gray-900">
                        <?php echo number_format($d['requested_quantity'] ?? 0, 0, '', ','); ?>
                      </span>
                      <div class="flex flex-col items-center">
                        <button onclick="adjustQuantity(<?php echo $d['item_id'] ?? 0; ?>, 1)" class="text-green-600 hover:text-green-700 transition-colors" title="Increase">
                          <i class="bi bi-chevron-up text-xs"></i>
                        </button>
                        <button onclick="adjustQuantity(<?php echo $d['item_id'] ?? 0; ?>, -1)" class="text-red-600 hover:text-red-700 transition-colors" title="Decrease">
                          <i class="bi bi-chevron-down text-xs"></i>
                        </button>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                        <?php echo htmlspecialchars($d['unit'] ?? 'pcs'); ?>
                      </span>
                      <select onchange="updateUnit(<?php echo $d['item_id'] ?? 0; ?>, this.value)" class="text-xs border border-gray-200 rounded px-2 py-1">
                        <option value="pcs" <?php echo ($d['unit'] ?? 'pcs') === 'pcs' ? 'selected' : ''; ?>>pcs</option>
                        <option value="kg" <?php echo ($d['unit'] ?? '') === 'kg' ? 'selected' : ''; ?>>kg</option>
                        <option value="m" <?php echo ($d['unit'] ?? '') === 'm' ? 'selected' : ''; ?>>m</option>
                        <option value="l" <?php echo ($d['unit'] ?? '') === 'l' ? 'selected' : ''; ?>>l</option>
                      </select>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <?php 
                    $itemStatus = $d['item_status'] ?? 'pending';
                    if ($itemStatus === 'pending') {
                        $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        $statusIcon = 'bi-clock';
                    } elseif ($itemStatus === 'approved') {
                        $statusClass = 'bg-green-100 text-green-800 border-green-200';
                        $statusIcon = 'bi-check-circle';
                    } else {
                        $statusClass = 'bg-red-100 text-red-800 border-red-200';
                        $statusIcon = 'bi-x-circle';
                    }
                    ?>
                    <div class="flex items-center justify-center space-x-2">
                      <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full border <?php echo $statusClass; ?>">
                        <i class="bi <?php echo $statusIcon; ?> mr-1"></i>
                        <?php echo ucfirst(htmlspecialchars($itemStatus)); ?>
                      </span>
                      <button onclick="toggleItemStatus(<?php echo $d['item_id'] ?? 0; ?>)" class="text-gray-400 hover:text-gray-600 transition-colors" title="Change Status">
                        <i class="bi bi-pencil-square text-xs"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Summary Footer -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-600">
                <i class="bi bi-box-seam mr-1"></i>
                <?= count($dat) ?> material<?= count($dat) > 1 ? 's' : '' ?>
              </div>
              <div class="text-sm text-gray-600">
                <i class="bi bi-calculator mr-1"></i>
                Total Quantity: 
                <span class="font-semibold text-gray-900">
                  <?php 
                  $totalQuantity = 0;
                  foreach ($dat as $item) {
                    $qty = isset($item['requested_quantity']) ? (int)$item['requested_quantity'] : 0;
                    $totalQuantity += $qty;
                  }
                  echo number_format($totalQuantity, 0, '', ',');
                  ?> items
                </span>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($customerReferenceData) && isset($comparisonResults)): ?>
        <!-- Customer Reference Comparison Results -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4 sm:p-5 mb-4 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
              <i class="bi bi-arrow-left-right mr-3 text-purple-600"></i>
              Comparison Results
            </h2>
            <div class="flex items-center space-x-2">
              <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-lg text-sm font-medium">
                <?= $comparisonResults['summary']['identical'] ? 'Perfect Match' : 'Differences Found' ?>
              </span>
            </div>
          </div>
          
          <!-- Customer Reference Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mb-5">
            <div class="bg-white rounded-lg p-3 border border-purple-100">
              <div class="flex items-center text-gray-500 mb-1">
                <i class="bi bi-upc-scan mr-2"></i>
                Customer Reference
              </div>
              <div class="font-mono font-semibold text-gray-900"><?= htmlspecialchars($customerReferenceData['customer_reference'] ?? 'N/A') ?></div>
            </div>
            
            <div class="bg-white rounded-lg p-3 border border-purple-100">
              <div class="flex items-center text-gray-500 mb-1">
                <i class="bi bi-building mr-2"></i>
                Customer Name
              </div>
              <div class="font-semibold text-gray-900"><?= htmlspecialchars($customerReferenceData['customer_name'] ?? 'N/A') ?></div>
            </div>
          </div>
          
  
          
          <!-- Customer Reference Materials Table -->
          <div class="mb-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-2.5 flex items-center">
              <i class="bi bi-list-ul mr-2 text-purple-600"></i>
              Customer Reference Materials
            </h3>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product ID</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php $no = 1; foreach($customerReferenceData['items'] as $item): ?>
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium"><?php echo $no++; ?></td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">
                        <?php echo htmlspecialchars($item['product_id'] ?? 'N/A'); ?>
                      </span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?></div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900"><?php echo number_format($item['quantity'] ?? 0); ?> <?php echo htmlspecialchars($item['unit'] ?? ''); ?></div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                      <?php 
                      $itemStatus = 'ok';
                      $statusClass = 'bg-green-100 text-green-800';
                      $statusText = 'Match';
                      
                      // Check for mismatches - with defensive array checks
                      if (!empty($comparisonResults['mismatched_names'])) {
                        foreach($comparisonResults['mismatched_names'] as $mismatch) {
                          if ($mismatch['product_id'] === ($item['product_id'] ?? null)) {
                            $itemStatus = 'name_mismatch';
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $statusText = 'Name Diff';
                            break;
                          }
                        }
                      }
                      
                      if (!empty($comparisonResults['mismatched_quantities'])) {
                        foreach($comparisonResults['mismatched_quantities'] as $mismatch) {
                          if ($mismatch['product_id'] === ($item['product_id'] ?? null)) {
                            $itemStatus = 'quantity_mismatch';
                            $statusClass = 'bg-orange-100 text-orange-800';
                            $statusText = 'Qty Diff';
                            break;
                          }
                        }
                      }
                      
                      if (!empty($comparisonResults['extra_in_customer'])) {
                        foreach($comparisonResults['extra_in_customer'] as $extra) {
                          if (($extra['product_id'] ?? null) === ($item['product_id'] ?? null)) {
                            $itemStatus = 'extra';
                            $statusClass = 'bg-blue-100 text-blue-800';
                            $statusText = 'Extra';
                            break;
                          }
                        }
                      }
                      ?>
                      <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                        <?= $statusText ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          
          <!-- Mismatch Details -->
          <?php if (!$comparisonResults['summary']['identical']): ?>
          <div class="bg-red-50 border border-red-200 rounded-lg p-3">
            <h4 class="text-sm font-medium text-red-800 mb-2">
              <i class="bi bi-exclamation-triangle mr-1"></i>
              Difference Details
            </h4>
            <ul class="text-sm text-red-700 space-y-1">
              <?php if (!empty($comparisonResults['mismatched_names'])): ?>
              <li><strong>Name Mismatches:</strong></li>
              <?php foreach($comparisonResults['mismatched_names'] as $mismatch): ?>
              <li class="ml-4">• <?= htmlspecialchars($mismatch['product_id'] ?? 'N/A') ?>: Request has "<?= htmlspecialchars($mismatch['request_name'] ?? 'Unknown') ?>" but customer has "<?= htmlspecialchars($mismatch['customer_name'] ?? 'Unknown') ?>"</li>
              <?php endforeach; ?>
              <?php endif; ?>
              
              <?php if (!empty($comparisonResults['mismatched_quantities'])): ?>
              <li><strong>Quantity Differences:</strong></li>
              <?php foreach($comparisonResults['mismatched_quantities'] as $mismatch): ?>
              <li class="ml-4">• <?= htmlspecialchars($mismatch['product_name'] ?? 'Unknown Product') ?>: Request has <?= $mismatch['request_quantity'] ?? 0 ?> but customer has <?= $mismatch['customer_quantity'] ?? 0 ?></li>
              <?php endforeach; ?>
              <?php endif; ?>
              
              <?php if (!empty($comparisonResults['missing_in_customer'])): ?>
              <li><strong>Missing in Customer Reference:</strong></li>
              <?php foreach($comparisonResults['missing_in_customer'] as $missing): ?>
              <li class="ml-4">• <?= htmlspecialchars($missing['product_name'] ?? 'Unknown Product') ?> (<?= $missing['requested_quantity'] ?? 0 ?> <?= $missing['unit'] ?? 'pcs' ?>)</li>
              <?php endforeach; ?>
              <?php endif; ?>
              
              <?php if (!empty($comparisonResults['extra_in_customer'])): ?>
              <li><strong>Extra in Customer Reference:</strong></li>
              <?php foreach($comparisonResults['extra_in_customer'] as $extra): ?>
              <li class="ml-4">• <?= htmlspecialchars($extra['product_name'] ?? 'Unknown Product') ?> (<?= $extra['quantity'] ?? 0 ?> <?= $extra['unit'] ?? 'pcs' ?>)</li>
              <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
          <?php endif; ?>
          
          <div class="mt-3 text-sm text-gray-500">
            <i class="bi bi-info-circle mr-1"></i>
            Customer reference data is sourced from StockDetailVer table.
          </div>
        </div>
        <?php endif; ?>

        <!-- Request Information -->
  
        
        <?php elseif (isset($error_message)): ?>
        <div class="text-center py-10">
          <div class="text-red-400 mb-4">
            <i class="bi bi-exclamation-triangle text-6xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Error</h3>
          <p class="text-red-600"><?= htmlspecialchars($error_message) ?></p>
        </div>
        <?php else: ?>
        <div class="text-center py-10">
          <div class="text-gray-400 mb-4">
            <i class="bi bi-table text-6xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Data Available</h3>
          <p class="text-gray-500">Scan a QR code to load material request data</p>
          <p class="text-sm text-gray-400 mt-2">QR codes should start with "REQ-" for material requests</p>
        </div>
        <?php endif; ?>
      </div>
    </div>


 


        </div>

    </div>

</div>

<!-- Success Alert Component -->
<div id="successAlert" class="fixed top-4 right-4 z-50 hidden">
  <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center space-x-3 min-w-[320px] animate-slide-in">
    <div class="flex-shrink-0">
      <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
        <i class="bi bi-check-lg text-white text-xl"></i>
      </div>
    </div>
    <div class="flex-1">
      <h4 class="font-semibold text-white">Perfect Match!</h4>
      <p class="text-green-100 text-sm">All materials match perfectly between RMW and Production</p>
    </div>
    <button onclick="closeSuccessAlert()" class="flex-shrink-0 text-green-200 hover:text-white transition-colors">
      <i class="bi bi-x-lg text-xl"></i>
    </button>
  </div>
  <div class="mt-2 h-1 bg-white bg-opacity-20 rounded-full overflow-hidden">
    <div id="alertProgress" class="h-full bg-white bg-opacity-40 transition-all duration-5000"></div>
  </div>
</div>

<!-- Difference Modal Component -->
<div id="differenceModal" class="fixed inset-0 z-50 hidden">
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm" onclick="closeDifferenceModal()"></div>
  
  <!-- Modal Content -->
  <div class="absolute inset-4 md:inset-8 lg:inset-16 bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
            <i class="bi bi-exclamation-triangle text-white text-xl"></i>
          </div>
          <div>
            <h3 class="text-xl font-bold">Material Differences Detected</h3>
            <p class="text-red-100 text-sm">Review the comparison results below</p>
          </div>
        </div>
        <button onclick="closeDifferenceModal()" class="text-red-200 hover:text-white transition-colors">
          <i class="bi bi-x-lg text-2xl"></i>
        </button>
      </div>
    </div>
    
    <!-- Modal Body -->
    <div class="flex-1 overflow-auto p-6">
      <!-- Summary Cards -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
          <div class="text-2xl font-bold text-gray-900" id="matchedCount">0</div>
          <div class="text-sm text-gray-500">Matched</div>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4 text-center">
          <div class="text-2xl font-bold text-yellow-600" id="mismatchedCount">0</div>
          <div class="text-sm text-gray-500">Mismatches</div>
        </div>
        <div class="bg-orange-50 rounded-lg p-4 text-center">
          <div class="text-2xl font-bold text-orange-600" id="missingCount">0</div>
          <div class="text-sm text-gray-500">Missing</div>
        </div>
        <div class="bg-blue-50 rounded-lg p-4 text-center">
          <div class="text-2xl font-bold text-blue-600" id="extraCount">0</div>
          <div class="text-sm text-gray-500">Extra</div>
        </div>
      </div>
      
      <!-- Difference Details -->
      <div id="differenceDetails" class="space-y-4">
        <!-- Content will be dynamically inserted here -->
      </div>
    </div>
    
    <!-- Modal Footer -->
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
      <div class="flex flex-col sm:flex-row gap-3 justify-end">
        <button onclick="closeDifferenceModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors font-medium">
          Close
        </button>
        <button onclick="acknowledgeDifferences()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
          Acknowledge & Continue
        </button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo url('includes/js/main.js'); ?>"></script>
    <style>
      @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      .animate-fade-in {
        animation: fade-in 0.3s ease-out;
      }
      
      @keyframes slide-in {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
      }
      
      .animate-slide-in {
        animation: slide-in 0.4s ease-out;
      }
      
      @keyframes modal-backdrop {
        from { opacity: 0; }
        to { opacity: 1; }
      }
      
      @keyframes modal-content {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
      }
      
      #differenceModal {
        animation: modal-backdrop 0.3s ease-out;
      }
      
      #differenceModal .absolute:last-child {
        animation: modal-content 0.3s ease-out;
      }
      
      /* Progress bar animation */
      .transition-all.duration-5000 {
        transition-duration: 5s;
      }
    </style>
    
    <script>
    // Success Alert Functions
    function showSuccessAlert() {
        const alert = document.getElementById('successAlert');
        const progressBar = document.getElementById('alertProgress');
        
        alert.classList.remove('hidden');
        alert.classList.add('animate-slide-in');
        
        // Start progress bar animation
        progressBar.style.width = '100%';
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 100);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            closeSuccessAlert();
        }, 5000);
    }
    
    function closeSuccessAlert() {
        const alert = document.getElementById('successAlert');
        alert.classList.add('hidden');
        alert.classList.remove('animate-slide-in');
    }
    
  
    
    // Difference Modal Functions
    function showDifferenceModal(comparisonData) {
        console.log('showDifferenceModal called with data:', comparisonData);
        
        const modal = document.getElementById('differenceModal');
        const detailsContainer = document.getElementById('differenceDetails');
        
        if (!modal) {
            console.error('Modal element not found');
            return;
        }
        
        if (!detailsContainer) {
            console.error('Details container not found');
            return;
        }
        
        // Update summary counts
        document.getElementById('matchedCount').textContent = comparisonData.summary.matched_items;
        document.getElementById('mismatchedCount').textContent = 
            comparisonData.mismatched_names.length + 
            comparisonData.mismatched_quantities.length;
        document.getElementById('missingCount').textContent = comparisonData.missing_in_customer.length;
        document.getElementById('extraCount').textContent = comparisonData.extra_in_customer.length;
        
        console.log('Updated summary counts');
        
        // Build difference details HTML
        let detailsHTML = '';
        
        // Name mismatches
        if (comparisonData.mismatched_names.length > 0) {
            detailsHTML += `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-800 mb-3 flex items-center">
                        <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                        Name Mismatches (${comparisonData.mismatched_names.length})
                    </h4>
                    <div class="space-y-2">
            `;
            
            comparisonData.mismatched_names.forEach(item => {
                detailsHTML += `
                    <div class="bg-white rounded p-3 border border-yellow-100">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm font-medium">${item.product_id}</span>
                            <span class="text-xs text-yellow-600">Different Names</span>
                        </div>
                        <div class="mt-2 text-sm">
                            <div class="text-gray-600">Request: <span class="font-medium">${item.request_name}</span></div>
                            <div class="text-gray-600">Customer: <span class="font-medium">${item.customer_name}</span></div>
                        </div>
                    </div>
                `;
            });
            
            detailsHTML += `
                    </div>
                </div>
            `;
        }
        
        // Quantity differences
        if (comparisonData.mismatched_quantities.length > 0) {
            detailsHTML += `
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <h4 class="font-semibold text-orange-800 mb-3 flex items-center">
                        <i class="bi bi-arrow-left-right mr-2"></i>
                        Quantity Differences (${comparisonData.mismatched_quantities.length})
                    </h4>
                    <div class="space-y-2">
            `;
            
            comparisonData.mismatched_quantities.forEach(item => {
                detailsHTML += `
                    <div class="bg-white rounded p-3 border border-orange-100">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm font-medium">${item.product_id}</span>
                            <span class="text-xs text-orange-600">Different Quantities</span>
                        </div>
                        <div class="mt-2 text-sm">
                            <div class="text-gray-900 font-medium">${item.product_name}</div>
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-600">Request: <span class="font-medium text-orange-600">${item.request_quantity}</span></span>
                                <span class="text-gray-600">Customer: <span class="font-medium text-orange-600">${item.customer_quantity}</span></span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            detailsHTML += `
                    </div>
                </div>
            `;
        }
        
        // Missing items
        if (comparisonData.missing_in_customer.length > 0) {
            detailsHTML += `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-semibold text-red-800 mb-3 flex items-center">
                        <i class="bi bi-dash-circle-fill mr-2"></i>
                        Missing in Customer Reference (${comparisonData.missing_in_customer.length})
                    </h4>
                    <div class="space-y-2">
            `;
            
            comparisonData.missing_in_customer.forEach(item => {
                detailsHTML += `
                    <div class="bg-white rounded p-3 border border-red-100">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm font-medium">${item.product_id}</span>
                            <span class="text-xs text-red-600">Missing</span>
                        </div>
                        <div class="mt-2 text-sm">
                            <div class="text-gray-900 font-medium">${item.product_name}</div>
                            <div class="text-gray-600">Quantity: ${item.requested_quantity} ${item.unit}</div>
                        </div>
                    </div>
                `;
            });
            
            detailsHTML += `
                    </div>
                </div>
            `;
        }
        
        // Extra items
        if (comparisonData.extra_in_customer.length > 0) {
            detailsHTML += `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                        <i class="bi bi-plus-circle-fill mr-2"></i>
                        Extra in Customer Reference (${comparisonData.extra_in_customer.length})
                    </h4>
                    <div class="space-y-2">
            `;
            
            comparisonData.extra_in_customer.forEach(item => {
                detailsHTML += `
                    <div class="bg-white rounded p-3 border border-blue-100">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm font-medium">${item.product_id}</span>
                            <span class="text-xs text-blue-600">Extra</span>
                        </div>
                        <div class="mt-2 text-sm">
                            <div class="text-gray-900 font-medium">${item.product_name}</div>
                            <div class="text-gray-600">Quantity: ${item.quantity} ${item.unit}</div>
                        </div>
                    </div>
                `;
            });
            
            detailsHTML += `
                    </div>
                </div>
            `;
        }
        
        detailsContainer.innerHTML = detailsHTML;
        
        console.log('Setting modal visible');
        modal.classList.remove('hidden');
        
        console.log('Modal classes after showing:', modal.className);
        
        // Focus management for accessibility
        document.body.style.overflow = 'hidden';
        const firstFocusable = modal.querySelector('button');
        if (firstFocusable) firstFocusable.focus();
        
        console.log('Modal should now be visible');
    }
    
    function closeDifferenceModal() {
        const modal = document.getElementById('differenceModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function acknowledgeDifferences() {
        closeDifferenceModal();
        // Additional logic if needed when user acknowledges differences
        console.log('Differences acknowledged');
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('differenceModal');
            if (!modal.classList.contains('hidden')) {
                closeDifferenceModal();
            }
        }
    });
    
    // Auto-trigger based on comparison results
    function checkComparisonResults() {
        <?php if (isset($comparisonResults)): ?>
        const comparisonData = <?php echo json_encode($comparisonResults); ?>;
        
        // Debug logging
        console.log('Comparison data found:', comparisonData);
        console.log('Is identical:', comparisonData.summary.identical);
        
        if (comparisonData.summary.identical) {
            // Show success alert for perfect match
            console.log('Showing success alert');
            setTimeout(() => {
                showSuccessAlert();
            }, 500);
        } else {
            // Show difference modal for mismatches
            console.log('Showing difference modal');
            setTimeout(() => {
                showDifferenceModal(comparisonData);
            }, 500);
        }
        <?php else: ?>
        console.log('No comparison results found');
        <?php endif; ?>
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkComparisonResults();
    });

    const inputField = document.getElementById('lot_material_bc');  // Get the input field
    let typingTimeout;

    // Function to simulate an action after 1 second of input
    function simulateEnter() {
        clearTimeout(typingTimeout);  // Clear any existing timeout to avoid multiple triggers
        typingTimeout = setTimeout(() => {
            // Simulate the action you want after 1 second of input
            // For example, submitting the form or triggering an event
            // Here, I'm using a log statement, but you can replace this with form submission or other actions
            console.log("Input entered: ", inputField.value);
            document.forms['form1'].submit();
            // For example, you can submit the form (if applicable)
            // document.forms['form1'].submit();

            // Alternatively, you could simulate the button press by clicking the "Input Data" button
            // document.getElementById('inputButton').click();
        }, 1000); // 1000ms = 1 second delay
    }

    // Add event listener to monitor the input field
    inputField.addEventListener('input', simulateEnter);
    
    // Auto-focus behavior when request number is pre-filled
    document.addEventListener('DOMContentLoaded', function() {
        // If request number is pre-filled, focus on the input for scanning additional items
        <?php if (!empty($nobon)): ?>
        inputField.focus();
        inputField.select();
        <?php endif; ?>
    });


        // Only initialize checkbox functionality if elements exist
        const checkboxes = document.querySelectorAll('.checkbox');
        const inputButton = document.getElementById('inputButton');

        if (checkboxes.length > 0 && inputButton) {
            // Fungsi untuk mengecek apakah semua checkbox sudah dicentang
            function updateButtonVisibility() {
                const allChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                if (allChecked) {
                    inputButton.style.display = 'inline-block'; // Tampilkan tombol inputan
                } else {
                    inputButton.style.display = 'none'; // Sembunyikan tombol inputan
                }
            }

            // Event listener untuk setiap checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateButtonVisibility);
            });

            // Panggil fungsi untuk memeriksa status awal checkbox
            updateButtonVisibility();

            // Function to disable the button and add a visual indicator
            function disableButton() {
                // Disable the button to prevent multiple submissions
                inputButton.disabled = true;
                
                // Change the text to indicate processing
                inputButton.innerText = 'Processing...';
                
                // Allow the form to submit normally
                return true;
            }

            // Add event listener to the form submission
            const form = document.querySelector('form[action="../controllers/proses.php"]');
            if (form) {
                form.addEventListener('submit', disableButton);
            }

            // Additional protection: Disable the button on click as well
            inputButton.addEventListener('click', function() {
                // Disable immediately on click
                inputButton.disabled = true;
                inputButton.innerText = 'Processing...';

                const submitForm = document.querySelector('form[action="../controllers/proses.php"]');
                if (submitForm) {
                    submitForm.submit();
                }
            });
        }

    </script>

<!-- <div class="row"> -->
  
  <!-- <div class="row">
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Dashboard</h3>
            <div class="ratio ratio-16x9">
              <canvas id="salesChart"></canvas>
            </div>
          </div>
        </div>
       
  </div> -->
  
    <!-- <script type="text/javascript" src="<?php echo url('includes/js/plugins/chart.js'); ?>"></script> -->

   <!-- Page specific javascripts
    
    <script type="text/javascript">
      const salesData = {
        type: "line",
        data: {
          labels: [
            "Jan",
            "Feb",
            "March",
            "April",
            "May",
            "June",
          ],
          datasets: [{
            label: 'Monthly Sales',
            data: [45, 50, 48, 48, 52, 55, 40],
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
          }]
        }
      }
      
      const supportRequests = {
        type: "doughnut",
        data: {
          labels: [
            'In-Progress',
            'Complete',
            'Delayed'
          ],
          datasets: [{
            label: 'Support Requests',
            data: [300, 50, 100],
            backgroundColor: [
              '#EFCC00',
              '#5AD3D1',
              '#FF5A5E'
            ],
            hoverOffset: 4
          }]
        }
      };
      
      const salesChartCtx = document.getElementById('salesChart');
      new Chart(salesChartCtx, salesData);
      
      const supportChartCtx = document.getElementById('supportRequestChart');
      new Chart(supportChartCtx, supportRequests);
    </script>-->

      </main>
    </div>

    <!-- JavaScript for dropdown functionality -->
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
    </script>
    
    <!-- Enhanced Sidebar Styles -->
    <style>
      /* Navigation Item Styles */
      .nav-item {
        position: relative;
        overflow: hidden;
      }
      
      .nav-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s;
      }
      
      .nav-item:hover::before {
        left: 100%;
      }
      
      .nav-active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        transform: translateX(4px);
      }
      
      .nav-active-rmw {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transform: translateX(4px);
      }
      
      .nav-inactive {
        color: #6b7280;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
      }
      
      .nav-inactive:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        transform: translateX(2px);
        color: #374151;
      }
      
      .nav-item i {
        transition: transform 0.2s ease;
      }
      
      .nav-item:hover i {
        transform: scale(1.1);
      }
      
      /* Logo Animation */
      .hover-scale {
        transition: transform 0.3s ease;
      }
      
      .hover-scale:hover {
        transform: scale(1.05);
      }
      
      /* Active Page Indicator */
      @keyframes pulse {
        0%, 100% {
          opacity: 1;
        }
        50% {
          opacity: 0.5;
        }
      }
      
      .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
      }
    </style>

    <!-- Enhanced JavaScript Functionality -->
    <script>
      // Toast Notification System
      class ToastManager {
        constructor() {
          this.container = document.getElementById('toastContainer');
        }

        show(message, type = 'info', duration = 5000) {
          const toast = document.createElement('div');
          const id = 'toast-' + Date.now();
          toast.id = id;
          
          const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
          };

          const icons = {
            success: 'bi-check-circle',
            error: 'bi-x-circle',
            warning: 'bi-exclamation-triangle',
            info: 'bi-info-circle'
          };

          toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] transform transition-all duration-300 translate-x-full`;
          toast.innerHTML = `
            <i class="bi ${icons[type]} text-xl"></i>
            <div class="flex-1">
              <p class="font-medium">${message}</p>
            </div>
            <button onclick="ToastManager.hide('${id}')" class="text-white hover:text-gray-200">
              <i class="bi bi-x-lg"></i>
            </button>
          `;

          this.container.appendChild(toast);
          
          // Animate in
          setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
          }, 100);

          // Auto hide
          if (duration > 0) {
            setTimeout(() => this.hide(id), duration);
          }
        }

        static hide(id) {
          const toast = document.getElementById(id);
          if (toast) {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
          }
        }
      }

      const toast = new ToastManager();

      // Loading and Validation Functions
      function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
      }

      function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
      }

      function validateRequestNumber(input) {
        const value = input.value.trim();
        const statusEl = document.getElementById('requestNumberStatus');
        const formatEl = document.getElementById('requestNumberFormat');
        
        if (value.length === 0) {
          statusEl.classList.add('hidden');
          formatEl.textContent = 'Format: REQ-YYYYMMDD-XXXX';
          return;
        }

        const regex = /^REQ-\d{8}-\d{1,4}$/;
        const isValid = regex.test(value);
        
        statusEl.classList.remove('hidden');
        if (isValid) {
          statusEl.innerHTML = '<i class="bi bi-check-circle text-green-500 text-lg"></i>';
          input.classList.remove('border-red-300');
          input.classList.add('border-green-300');
          formatEl.textContent = 'Valid format';
          formatEl.classList.add('text-green-600');
          formatEl.classList.remove('text-red-600');
        } else {
          statusEl.innerHTML = '<i class="bi bi-x-circle text-red-500 text-lg"></i>';
          input.classList.remove('border-green-300');
          input.classList.add('border-red-300');
          formatEl.textContent = 'Format: REQ-YYYYMMDD-XXXX';
          formatEl.classList.add('text-red-600');
          formatEl.classList.remove('text-green-600');
        }
      }

      function validateCustomerReference(input) {
        const value = input.value.trim();
        const statusEl = document.getElementById('customerReferenceStatus');
        const statusTextEl = document.getElementById('scanStatus');
        
        if (value.length === 0) {
          statusEl.classList.add('hidden');
          statusTextEl.textContent = 'Ready to scan';
          return;
        }

        // Basic validation for customer reference format
        const isValid = value.length > 3 && /^[A-Z0-9\/\-]+$/.test(value);
        
        statusEl.classList.remove('hidden');
        if (isValid) {
          statusEl.innerHTML = '<i class="bi bi-check-circle text-green-500 text-lg"></i>';
          input.classList.remove('border-red-300');
          input.classList.add('border-green-300');
          statusTextEl.textContent = 'Valid format';
          statusTextEl.classList.add('text-green-600');
          document.getElementById('scannerIndicator').classList.add('bg-green-500');
          document.getElementById('scannerIndicator').classList.remove('bg-yellow-500');
        } else {
          statusEl.innerHTML = '<i class="bi bi-exclamation-triangle text-yellow-500 text-lg"></i>';
          input.classList.remove('border-green-300', 'border-red-300');
          statusTextEl.textContent = 'Checking format...';
          statusTextEl.classList.remove('text-green-600');
          document.getElementById('scannerIndicator').classList.add('bg-yellow-500');
          document.getElementById('scannerIndicator').classList.remove('bg-green-500');
        }
      }

      function clearScannerInput() {
        const input = document.getElementById('lot_material_bc');
        input.value = '';
        input.classList.remove('border-green-300', 'border-red-300');
        document.getElementById('customerReferenceStatus').classList.add('hidden');
        document.getElementById('scanStatus').textContent = 'Ready to scan';
        input.focus();
        toast.show('Scanner input cleared', 'info', 2000);
      }

      function resetForm() {
        if (confirm('Are you sure you want to start a new scan?')) {
          window.location.href = window.location.pathname;
        }
      }

      function refreshPage() {
        showLoading();
        setTimeout(() => {
          window.location.reload();
        }, 500);
      }

      // Table Functions
      function filterMaterials() {
        const searchInput = document.getElementById('materialSearch');
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('.material-row');
        let visibleCount = 0;

        rows.forEach(row => {
          const searchable = row.getAttribute('data-searchable');
          if (searchable.includes(filter)) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });

        document.getElementById('itemCount').textContent = visibleCount;
        
        if (visibleCount === 0) {
          toast.show('No materials found matching your search', 'warning', 3000);
        }
      }

      function sortTable(columnIndex) {
        const table = document.getElementById('materialsTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
          const aValue = a.cells[columnIndex].textContent.trim();
          const bValue = b.cells[columnIndex].textContent.trim();
          
          if (columnIndex === 0 || columnIndex === 4) { // Number or Quantity column
            return parseFloat(aValue) - parseFloat(bValue);
          }
          return aValue.localeCompare(bValue);
        });

        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
        
        toast.show(`Table sorted by column ${columnIndex + 1}`, 'info', 2000);
      }

      function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
          toast.show(`Copied "${text}" to clipboard`, 'success', 2000);
        }).catch(() => {
          toast.show('Failed to copy to clipboard', 'error', 3000);
        });
      }

      function exportMaterials() {
        const table = document.getElementById('materialsTable');
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        // Headers
        const headers = Array.from(rows[0].querySelectorAll('th')).map(th => th.textContent.trim());
        csv.push(headers.join(','));
        
        // Data rows
        rows.forEach((row, index) => {
          if (index > 0) { // Skip header row
            const rowData = Array.from(row.querySelectorAll('td')).map(td => td.textContent.trim());
            csv.push(rowData.join(','));
          }
        });
        
        const csvContent = csv.join('\\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'materials-export-' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
        window.URL.revokeObjectURL(url);
        
        toast.show('Materials exported successfully', 'success', 3000);
      }

      // Placeholder functions for enhanced functionality
      function adjustQuantity(itemId, change) {
        toast.show('Quantity adjustment feature coming soon', 'info', 3000);
      }

      function updateUnit(itemId, newUnit) {
        toast.show('Unit updated to ' + newUnit, 'success', 2000);
      }

      function toggleItemStatus(itemId) {
        toast.show('Status change feature coming soon', 'info', 3000);
      }

      // Form submission with loading
      document.getElementById('scannerForm').addEventListener('submit', function(e) {
        showLoading();
        toast.show('Processing scanner data...', 'info', 0);
      });

      // Initialize on page load
      document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips and other UI elements
        toast.show('Scanner ready', 'success', 2000);
        
        // Auto-focus logic
        const qrInput = document.getElementById('lot_material_bc');
        const requestInput = document.getElementById('request_number_input');
        
        if (qrInput && qrInput.value === '') {
          qrInput.focus();
        } else if (requestInput && requestInput.value === '') {
          requestInput.focus();
        }
      });

      // Keyboard shortcuts
      document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
          e.preventDefault();
          document.getElementById('materialSearch')?.focus();
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
          hideLoading();
        }
      });
    </script>


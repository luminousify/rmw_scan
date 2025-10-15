<body class="min-h-screen bg-gray-50" style="position: relative; overflow-x: hidden;">
    <!-- Mobile menu backdrop -->
    <div id="mobileMenuBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="closeMobileMenu()"></div>
    
    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
      <div class="px-2 sm:px-4 lg:px-6">
        <div class="flex justify-between items-center h-16">
          <!-- Mobile menu button -->
          <button 
            type="button" 
            class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            onclick="toggleMobileMenu()"
            aria-controls="mobile-menu"
            aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <i class="bi bi-list text-xl"></i>
          </button>
          
          <!-- Logo (hidden on mobile, shown on larger screens) -->
          <div class="hidden lg:flex items-center flex-1">
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" class="text-xl font-bold text-gray-900">
              RMW System
            </a>
          </div>
          
          <!-- Mobile logo (centered on mobile) -->
          <div class="lg:hidden absolute inset-x-0 top-0 flex justify-center items-center h-16">
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" class="text-lg font-bold text-gray-900">
              RMW
            </a>
          </div>
          
          <!-- Right side -->
          <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- User info (hidden on mobile, shown on larger screens) -->
            <span class="hidden sm:block text-sm font-medium text-gray-700 truncate max-w-[120px] md:max-w-none">
              <?=strtoupper($name)?> (<?=ucfirst($department ?? 'production')?>)
            </span>
            
            <!-- User Menu-->
            <div class="relative">
              <button 
                class="p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                onclick="toggleDropdown()"
                aria-label="User menu">
                <i class="bi bi-person text-lg sm:text-xl"></i>
              </button>
              
              <!-- Dropdown Menu -->
              <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200 lg:origin-top-right lg:scale-100 transition-transform">
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
      <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:z-auto">
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
      <main id="main-content" class="flex-1 min-h-screen lg:ml-0" role="main">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
          <!-- Page Header -->
          <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">My Material Requests</h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-lg">View and track your material requests</p>
          </div>

        <!-- Alert Messages -->
        <?php if (isset($error_message)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm animate-fade-in" role="alert">
          <div class="flex">
            <i class="bi bi-exclamation-circle text-red-400 text-xl mr-3 flex-shrink-0" aria-hidden="true"></i>
            <div>
              <h3 class="text-sm font-medium text-red-800">Error!</h3>
              <p class="text-sm text-red-700 mt-1"><?= $error_message ?></p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600 transition-colors" aria-label="Dismiss error">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <?php endif; ?>

        <!-- Success Messages (placeholder for future use) -->
        <div id="successAlert" class="hidden mb-6 bg-green-50 border border-green-200 rounded-lg p-4 shadow-sm animate-fade-in" role="alert">
          <div class="flex">
            <i class="bi bi-check-circle text-green-400 text-xl mr-3 flex-shrink-0" aria-hidden="true"></i>
            <div>
              <h3 class="text-sm font-medium text-green-800">Success!</h3>
              <p class="text-sm text-green-700 mt-1" id="successMessage"></p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600 transition-colors" aria-label="Dismiss success">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

          <!-- Filters and Search -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
            <form method="GET" id="filterForm" class="space-y-4">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2 lg:col-span-2">
                  <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                  <div class="relative">
                    <input 
                      type="text" 
                      id="searchInput"
                      name="search" 
                      value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                      placeholder="Search by request number or product..." 
                      class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm sm:text-base"
                      autocomplete="off">
                    <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
                    <div id="searchSpinner" class="hidden absolute right-3 top-1/2 transform -translate-y-1/2">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                    </div>
                  </div>
                </div>
                
                <div>
                  <label for="statusSelect" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                  <select 
                    id="statusSelect"
                    name="status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm sm:text-base">
                    <option value="all" <?= ($_GET['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="diproses" <?= ($_GET['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Processing</option>
                    <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                  </select>
                </div>
                
                <div class="flex items-end space-x-2 sm:space-x-3">
                  <button 
                    type="submit" 
                    class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm hover:shadow-md whitespace-nowrap">
                    <i class="bi bi-funnel mr-1 sm:mr-2" aria-hidden="true"></i><span class="hidden sm:inline">Filter</span>
                  </button>
                  
                  <a 
                    href="<?php echo url('app/controllers/my_requests.php'); ?>" 
                    class="flex-1 sm:flex-none bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-4 sm:px-6 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm hover:shadow-md whitespace-nowrap">
                    <i class="bi bi-arrow-clockwise mr-1 sm:mr-2" aria-hidden="true"></i><span class="hidden sm:inline">Reset</span>
                  </a>
                </div>
              </div>
            </form>
          </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">My Material Requests</h3>
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">
                  <?php if (!empty($userRequests)): ?>
                    Showing <?= min((($page - 1) * $limit) + 1, $totalRequests) ?>-<?= min($page * $limit, $totalRequests) ?> of <?= $totalRequests ?> request<?= $totalRequests > 1 ? 's' : '' ?>
                  <?php endif; ?>
                </span>
              </div>
            </div>
          </div>
          
        <!-- Loading State -->
        <div id="tableLoading" class="hidden">
          <div class="px-8 py-6 space-y-4">
            <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="animate-pulse">
              <div class="flex space-x-8 items-center">
                <div class="h-5 bg-gray-200 rounded w-24"></div>
                <div class="h-5 bg-gray-200 rounded w-28"></div>
                <div class="h-5 bg-gray-200 rounded w-20"></div>
                <div class="h-5 bg-gray-200 rounded w-16"></div>
                <div class="h-5 bg-gray-200 rounded w-24"></div>
                <div class="h-5 bg-gray-200 rounded w-40"></div>
                <div class="h-5 bg-gray-200 rounded w-32"></div>
              </div>
            </div>
            <?php endfor; ?>
          </div>
        </div>
          
          <!-- Desktop Table View -->
          <div class="hidden lg:block overflow-x-auto">
            <table class="w-full divide-y divide-gray-200" role="table" aria-label="Material requests">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Request #</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Date & Created By</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Items</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Notes</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200" id="requestsTableBody">
                <?php if (!empty($userRequests)): ?>
                  <?php foreach ($userRequests as $request): ?>
                  <tr class="hover:bg-gray-50 transition-colors duration-150" data-request-id="<?= $request['id'] ?>">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-semibold text-gray-900 font-mono"><?= htmlspecialchars($request['request_number']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-600">
                        <span class="inline-flex items-center">
                          <i class="bi bi-person-circle mr-2 text-gray-400" aria-hidden="true"></i>
                          <span class="font-medium"><?= htmlspecialchars($request['created_by'] ?? 'System') ?></span>
                        </span>
                      </div>
                      <div class="text-xs text-gray-500 mt-1">
                        <time datetime="<?= $request['created_at'] ?>"><?= date('M d, Y H:i', strtotime($request['created_at'])) ?></time>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-600">
                        <span class="inline-flex items-center">
                          <i class="bi bi-box-seam mr-2 text-gray-400" aria-hidden="true"></i>
                          <span class="font-medium"><?= $request['item_count'] ?></span>
                          <span class="ml-1">items</span>
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 py-1 inline-flex text-xs font-medium rounded-full transition-all duration-200
                        <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($request['status'] === 'diproses' ? 'bg-blue-100 text-blue-800' : 
                           ($request['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) ?>">
                        <?= ucfirst($request['status']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-600 max-w-xs">
                        <div class="truncate" title="<?= htmlspecialchars($request['notes'] ?? '') ?>">
                          <?= htmlspecialchars($request['notes'] ?? 'No notes') ?>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <button 
                          onclick="viewRequest(<?= $request['id'] ?>)" 
                          class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                          aria-label="View details for request <?= htmlspecialchars($request['request_number']) ?>"
                          title="View request details">
                          <i class="bi bi-eye mr-1" aria-hidden="true"></i> View
                        </button>
                        
                        <?php if ($request['status'] === 'diproses'): ?>
                        <button 
                          onclick="scanQRForRequest('<?= htmlspecialchars($request['request_number']) ?>')" 
                          class="inline-flex items-center px-2.5 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-md text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                          aria-label="Scan QR code for request <?= htmlspecialchars($request['request_number']) ?>"
                          title="Open QR scanner for this request">
                          <i class="bi bi-upc-scan mr-1" aria-hidden="true"></i> Scan
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($request['status'] === 'pending'): ?>
                        <button 
                          class="cancel-request-btn inline-flex items-center px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-md text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                          data-request-id="<?= $request['id'] ?>"
                          data-request-number="<?= htmlspecialchars($request['request_number']) ?>"
                          aria-label="Cancel request <?= htmlspecialchars($request['request_number']) ?>"
                          title="Cancel this request (only available for pending requests)">
                          <i class="bi bi-x-circle mr-1" aria-hidden="true"></i> Cancel
                        </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                      <div class="space-y-4">
                        <i class="bi bi-inbox text-6xl text-gray-300 block mx-auto" aria-hidden="true"></i>
                        <div>
                          <h4 class="text-lg font-semibold text-gray-900 mb-2">No requests found</h4>
                          <p class="text-gray-600 mb-4 text-sm">Get started by creating your first material request</p>
                          <a 
                            href="<?php echo url('app/controllers/material_request.php'); ?>" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="bi bi-plus-circle mr-2" aria-hidden="true"></i>
                            <span>Create Request</span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Mobile Card View -->
          <div class="lg:hidden space-y-4" id="mobileRequestsView">
            <?php if (!empty($userRequests)): ?>
              <?php foreach ($userRequests as $request): ?>
              <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200" data-request-id="<?= $request['id'] ?>">
                <div class="flex items-start justify-between mb-3">
                  <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-gray-900 font-mono truncate"><?= htmlspecialchars($request['request_number']) ?></h3>
                    <div class="flex items-center mt-1 text-xs text-gray-500">
                      <i class="bi bi-person-circle mr-1" aria-hidden="true"></i>
                      <span><?= htmlspecialchars($request['created_by'] ?? 'System') ?></span>
                      <span class="mx-1">•</span>
                      <time datetime="<?= $request['created_at'] ?>"><?= date('M d, Y', strtotime($request['created_at'])) ?></time>
                    </div>
                  </div>
                  <span class="px-2 py-1 inline-flex text-xs font-medium rounded-full flex-shrink-0
                    <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($request['status'] === 'diproses' ? 'bg-blue-100 text-blue-800' : 
                       ($request['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) ?>">
                    <?= ucfirst($request['status']) ?>
                  </span>
                </div>
                
                <div class="space-y-2 mb-3">
                  <div class="flex items-center text-sm text-gray-600">
                    <i class="bi bi-box-seam mr-2 text-gray-400" aria-hidden="true"></i>
                    <span><?= $request['item_count'] ?> items</span>
                  </div>
                  
                  <?php if (!empty($request['notes'])): ?>
                  <div class="text-sm text-gray-600">
                    <div class="line-clamp-2" title="<?= htmlspecialchars($request['notes']) ?>">
                      <?= htmlspecialchars($request['notes']) ?>
                    </div>
                  </div>
                  <?php endif; ?>
                </div>
                
                <div class="flex items-center space-x-2 pt-3 border-t border-gray-100">
                  <button 
                    onclick="viewRequest(<?= $request['id'] ?>)" 
                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="bi bi-eye mr-1" aria-hidden="true"></i> View
                  </button>
                  
                  <?php if ($request['status'] === 'diproses'): ?>
                  <button 
                    onclick="scanQRForRequest('<?= htmlspecialchars($request['request_number']) ?>')" 
                    class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-md text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="bi bi-upc-scan mr-1" aria-hidden="true"></i> Scan
                  </button>
                  <?php endif; ?>
                  
                  <?php if ($request['status'] === 'pending'): ?>
                  <button 
                    class="cancel-request-btn flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-md text-xs font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    data-request-id="<?= $request['id'] ?>"
                    data-request-number="<?= htmlspecialchars($request['request_number']) ?>">
                    <i class="bi bi-x-circle mr-1" aria-hidden="true"></i> Cancel
                  </button>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="bg-white border border-gray-200 rounded-lg p-6 text-center text-gray-500">
                <div class="space-y-4">
                  <i class="bi bi-inbox text-6xl text-gray-300 block mx-auto" aria-hidden="true"></i>
                  <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">No requests found</h4>
                    <p class="text-gray-600 mb-4 text-sm">Get started by creating your first material request</p>
                    <a 
                      href="<?php echo url('app/controllers/material_request.php'); ?>" 
                      class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                      <i class="bi bi-plus-circle mr-2" aria-hidden="true"></i>
                      <span>Create Request</span>
                    </a>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
          
          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
          <div class="px-4 sm:px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
              <div class="text-sm text-gray-700 text-center sm:text-left">
                Showing 
                <span class="font-medium"><?= min((($page - 1) * $limit) + 1, $totalRequests) ?></span> 
                to 
                <span class="font-medium"><?= min($page * $limit, $totalRequests) ?></span> 
                of 
                <span class="font-medium"><?= $totalRequests ?></span> 
                results
              </div>
              
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <!-- Previous page -->
                <?php if ($page > 1): ?>
                  <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                     class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <i class="bi bi-chevron-left"></i>
                  </a>
                <?php else: ?>
                  <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                    <span class="sr-only">Previous</span>
                    <i class="bi bi-chevron-left"></i>
                  </span>
                <?php endif; ?>
                
                <!-- Page numbers (mobile simplified) -->
                <div class="hidden sm:flex">
                  <?php
                  $startPage = max(1, $page - 2);
                  $endPage = min($totalPages, $page + 2);
                  
                  if ($startPage > 1) {
                      echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>';
                      if ($startPage > 2) {
                          echo '<span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                      }
                  }
                  
                  for ($i = $startPage; $i <= $endPage; $i++) {
                      $isActive = $i == $page;
                      $classes = $isActive 
                          ? 'relative inline-flex items-center px-3 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600 z-10'
                          : 'relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50';
                      echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="' . $classes . '">' . $i . '</a>';
                  }
                  
                  if ($endPage < $totalPages) {
                      if ($endPage < $totalPages - 1) {
                          echo '<span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                      }
                      echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $totalPages . '</a>';
                  }
                  ?>
                </div>
                
                <!-- Mobile page indicator -->
                <div class="sm:hidden px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  <?= $page ?> / <?= $totalPages ?>
                </div>
                
                <!-- Next page -->
                <?php if ($page < $totalPages): ?>
                  <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                     class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <i class="bi bi-chevron-right"></i>
                  </a>
                <?php else: ?>
                  <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                    <span class="sr-only">Next</span>
                    <i class="bi bi-chevron-right"></i>
                  </span>
                <?php endif; ?>
              </nav>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </main>
        </div>
      </main>
    </div>

    <!-- Request Details Modal -->
    <div id="requestModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 animate-fade-in" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden animate-slide-up">
          <!-- Modal Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Request Details</h3>
            <button 
              onclick="closeModal()" 
              class="text-gray-400 hover:text-gray-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-lg p-2"
              aria-label="Close modal">
              <i class="bi bi-x-lg text-xl" aria-hidden="true"></i>
            </button>
          </div>
          
          <!-- Modal Body -->
          <div class="p-6 overflow-y-auto max-h-[calc(90vh-8rem)]">
            <!-- Loading State -->
            <div id="modalLoading" class="hidden">
              <div class="flex flex-col items-center justify-center py-12 space-y-4">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="text-gray-600">Loading request details...</p>
              </div>
            </div>
            
            <!-- Error State -->
            <div id="modalError" class="hidden">
              <div class="flex flex-col items-center justify-center py-12 space-y-4">
                <i class="bi bi-exclamation-triangle text-red-500 text-5xl" aria-hidden="true"></i>
                <div class="text-center">
                  <h4 class="text-lg font-medium text-gray-900 mb-2">Error Loading Details</h4>
                  <p class="text-gray-600" id="modalErrorMessage">Failed to load request details</p>
                  <button 
                    onclick="closeModal()" 
                    class="mt-4 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
                    Close
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Request Details Content -->
            <div id="requestDetails" class="space-y-6">
              <!-- Request details will be loaded here -->
            </div>
          </div>
          
          <!-- Modal Footer -->
          <div id="modalFooter" class="hidden p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-end space-x-3">
              <button 
                onclick="closeModal()" 
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Simple Confirmation Modal -->
    <div id="confirmModal" class="modal-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
      <div class="modal-content">
        <div class="modal-header">
          <div class="modal-icon">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
          <h3 id="confirmTitle">Confirm Cancellation</h3>
        </div>
        <p id="confirmMessage">Are you sure you want to cancel this request?</p>
        <div class="modal-actions">
          <button 
            id="confirmCancel"
            type="button"
            onclick="closeConfirmModal()" 
            class="btn btn-secondary">
            No, Keep Request
          </button>
          <button 
            id="confirmAction"
            type="button"
            class="btn btn-danger">
            Yes, Cancel Request
          </button>
        </div>
      </div>
    </div>

    <!-- Custom CSS for animations -->
    <style>
      @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
      }
      
      @keyframes slide-up {
        from { 
          opacity: 0;
          transform: translateY(20px);
        }
        to { 
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      .animate-fade-in {
        animation: fade-in 0.2s ease-out;
      }
      
      .animate-slide-up {
        animation: slide-up 0.3s ease-out;
      }
      
      /* Mobile responsive adjustments */
      @media (max-width: 768px) {
        .sidebar-mobile-hidden {
          transform: translateX(-100%);
        }
        
        .sidebar-mobile-visible {
          transform: translateX(0);
        }
      }
      
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
      
      
      /* Simple modal styles */
      .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      
      .modal-overlay.hidden {
        display: none;
      }
      
      .modal-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        max-width: 500px;
        width: 90%;
        margin: 20px;
      }
      
      .modal-header {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
      }
      
      .modal-icon {
        flex-shrink: 0;
        width: 48px;
        height: 48px;
        background: #fef2f2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
      }
      
      .modal-icon i {
        color: #dc2626;
        font-size: 24px;
      }
      
      .modal-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #111827;
        margin: 0;
      }
      
      #confirmMessage {
        color: #6b7280;
        margin-bottom: 24px;
        line-height: 1.5;
      }
      
      .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
      }
      
      .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
      }
      
      .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }
      
      .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
      }
      
      .btn-secondary:hover:not(:disabled) {
        background: #e5e7eb;
        color: #111827;
      }
      
      .btn-danger {
        background: #dc2626;
        color: white;
      }
      
      .btn-danger:hover:not(:disabled) {
        background: #b91c1c;
      }
      
      .btn-danger:focus {
        outline: 2px solid #fca5a5;
        outline-offset: 2px;
      }
      
      /* Prevent body scroll when modal is open */
      body.modal-open {
        overflow: hidden;
      }
      
      /* Line clamp utility for mobile view */
      .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }
      
      /* Responsive adjustments */
      @media (max-width: 640px) {
        .text-responsive-xs {
          font-size: 0.75rem;
          line-height: 1rem;
        }
        
        .button-responsive-xs {
          padding: 0.5rem 0.75rem;
          font-size: 0.75rem;
        }
      }
    </style>

    <!-- JavaScript -->
    <script>
      // Utility functions
      const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
          const later = () => {
            clearTimeout(timeout);
            func(...args);
          };
          clearTimeout(timeout);
          timeout = setTimeout(later, wait);
        };
      };

      const showSuccess = (message) => {
        const alert = document.getElementById('successAlert');
        const messageEl = document.getElementById('successMessage');
        messageEl.textContent = message;
        alert.classList.remove('hidden');
        setTimeout(() => alert.classList.add('hidden'), 5000);
      };

      const showError = (message) => {
        // Create error alert dynamically if needed
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mb-6 bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm animate-fade-in';
        errorDiv.setAttribute('role', 'alert');
        errorDiv.innerHTML = `
          <div class="flex">
            <i class="bi bi-exclamation-circle text-red-400 text-xl mr-3 flex-shrink-0" aria-hidden="true"></i>
            <div>
              <h3 class="text-sm font-medium text-red-800">Error!</h3>
              <p class="text-sm text-red-700 mt-1">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600 transition-colors" aria-label="Dismiss error">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        `;
        
        const main = document.querySelector('main');
        main.insertBefore(errorDiv, main.firstChild);
        setTimeout(() => errorDiv.remove(), 5000);
      };

      // Mobile menu functionality
      function toggleMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        
        if (isOpen) {
          closeMobileMenu();
        } else {
          openMobileMenu();
        }
      }
      
      function openMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      
      function closeMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
      }
      
      // Close mobile menu when window is resized to desktop size
      window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) { // lg breakpoint
          closeMobileMenu();
        }
      });

      // Dropdown functionality
      function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        const button = event.currentTarget;
        const isHidden = dropdown.classList.contains('hidden');
        
        dropdown.classList.toggle('hidden');
        button.setAttribute('aria-expanded', isHidden);
      }

      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const button = event.target.closest('button[onclick*="toggleDropdown"]');
        
        if (!button) {
          dropdown.classList.add('hidden');
          const menuButton = document.querySelector('button[onclick*="toggleDropdown"]');
          if (menuButton) {
            menuButton.setAttribute('aria-expanded', 'false');
          }
        }
      });

      // Search functionality with debouncing
      const searchInput = document.getElementById('searchInput');
      const searchSpinner = document.getElementById('searchSpinner');
      
      if (searchInput) {
        const performSearch = debounce(() => {
          searchSpinner.classList.remove('hidden');
          // Simulate search delay
          setTimeout(() => {
            document.getElementById('filterForm').submit();
          }, 300);
        }, 500);

        searchInput.addEventListener('input', performSearch);
      }

      // Modal functionality
      function viewRequest(requestId) {
        console.log('viewRequest function called with ID:', requestId);
        
        // Remove any existing test modal
        const existingTest = document.getElementById('testModal');
        if (existingTest) existingTest.remove();
        
        // Create a working modal with proper structure
        const modal = document.createElement('div');
        modal.id = 'requestModal';
        modal.style.cssText = `
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            z-index: 999999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow-y: auto !important;
        `;
        
        modal.innerHTML = `
            <div style="
                background: white !important;
                border-radius: 12px !important;
                max-width: 1200px !important;
                width: 95% !important;
                max-height: 90vh !important;
                overflow: hidden !important;
                box-shadow: 0 25px 50px rgba(0,0,0,0.25) !important;
                display: flex !important;
                flex-direction: column !important;
            ">
                <!-- Header -->
                <div style="
                    display: flex !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                    padding: 24px !important;
                    border-bottom: 1px solid #e5e7eb !important;
                    background: #f9fafb !important;
                ">
                    <h3 style="font-size: 20px !important; font-weight: 600 !important; color: #111827 !important; margin: 0 !important;">
                        Request Details
                    </h3>
                    <button onclick="this.closest('#requestModal').remove()" style="
                        background: none !important;
                        border: none !important;
                        color: #6b7280 !important;
                        cursor: pointer !important;
                        padding: 8px !important;
                        border-radius: 6px !important;
                        font-size: 20px !important;
                    ">&times;</button>
                </div>
                
                <!-- Loading State -->
                <div id="modalLoading" style="
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 60px !important;
                ">
                    <div style="
                        width: 48px !important;
                        height: 48px !important;
                        border: 4px solid #e5e7eb !important;
                        border-top: 4px solid #3b82f6 !important;
                        border-radius: 50% !important;
                        animation: spin 1s linear infinite !important;
                        margin-bottom: 16px !important;
                    "></div>
                    <p style="color: #6b7280 !important; margin: 0 !important;">Loading request details...</p>
                </div>
                
                <!-- Error State -->
                <div id="modalError" style="display: none !important; padding: 60px !important; text-align: center !important;">
                    <div style="font-size: 48px !important; color: #ef4444 !important; margin-bottom: 16px !important;">⚠️</div>
                    <h4 style="font-size: 18px !important; font-weight: 600 !important; color: #111827 !important; margin-bottom: 8px !important;">Error Loading Details</h4>
                    <p id="modalErrorMessage" style="color: #6b7280 !important; margin-bottom: 16px !important;">Failed to load request details</p>
                    <button onclick="this.closest('#requestModal').remove()" style="
                        background: #6b7280 !important;
                        color: white !important;
                        padding: 8px 16px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                    ">Close</button>
                </div>
                
                <!-- Request Details Content -->
                <div id="requestDetails" style="display: none !important; padding: 24px !important; overflow-y: auto !important; flex: 1 !important;">
                    <!-- Content will be loaded here -->
                </div>
                
                <!-- Footer -->
                <div id="modalFooter" style="display: none !important; padding: 24px !important; border-top: 1px solid #e5e7eb !important; background: #f9fafb !important; text-align: right !important;">
                    <button onclick="this.closest('#requestModal').remove()" style="
                        background: #6b7280 !important;
                        color: white !important;
                        padding: 8px 16px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                    ">Close</button>
                </div>
            </div>
        `;
        
        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
        
        // Remove any existing modal and add new one
        const existingModal = document.getElementById('requestModal');
        if (existingModal) existingModal.remove();
        
        document.body.appendChild(modal);
        
        // Fetch request details via AJAX
        const url = `get_request_details.php?id=${requestId}`;
        console.log('Fetching from URL:', url);
        
        fetch(url)
          .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Response data:', data);
            document.getElementById('modalLoading').style.display = 'none';
            
            if (data.success) {
              console.log('Data success, calling displayRequestDetails');
              displayRequestDetails(data.request);
            } else {
              console.log('Data error:', data.error);
              document.getElementById('modalErrorMessage').textContent = data.error || 'Failed to load request details';
              document.getElementById('modalError').style.display = 'block';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalLoading').style.display = 'none';
            document.getElementById('modalErrorMessage').textContent = 'Network error: ' + error.message;
            document.getElementById('modalError').style.display = 'block';
          });
      }
      
      function displayRequestDetails(request) {
        console.log('displayRequestDetails called with:', request);
        
        const itemsHtml = request.items.map(item => `
          <tr style="border-bottom: 1px solid #e5e7eb !important;">
            <td style="padding: 12px 16px !important; font-size: 14px !important; font-family: monospace !important; color: #111827 !important;">${item.product_id}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.product_name}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.requested_quantity}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.unit}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.description || '-'}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important;">
              <span style="
                padding: 4px 8px !important;
                display: inline-flex !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                border-radius: 9999px !important;
                ${item.status === 'pending' ? 'background: #fef3c7 !important; color: #92400e !important;' :
                  item.status === 'approved' ? 'background: #d1fae5 !important; color: #065f46 !important;' :
                  'background: #fee2e2 !important; color: #991b1b !important;'}
              ">
                ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
              </span>
            </td>
          </tr>
        `).join('');
        
        const statusColor = request.status === 'pending' ? 'background: #fef3c7 !important; color: #92400e !important;' :
                           request.status === 'diproses' ? 'background: #dbeafe !important; color: #1e40af !important;' :
                           request.status === 'completed' ? 'background: #d1fae5 !important; color: #065f46 !important;' :
                           'background: #fee2e2 !important; color: #991b1b !important;';
        
        const detailsContent = `
          <div style="display: flex !important; flex-direction: column !important; gap: 24px !important;">
            <!-- Request Header -->
            <div style="
              background: #f9fafb !important;
              border-radius: 8px !important;
              padding: 24px !important;
              border: 1px solid #e5e7eb !important;
            ">
              <div style="display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 24px !important;">
                <div>
                  <label style="font-size: 14px !important; font-weight: 500 !important; color: #6b7280 !important; display: block !important; margin-bottom: 8px !important;">Request Number</label>
                  <p style="font-size: 18px !important; font-weight: 600 !important; color: #111827 !important; font-family: monospace !important; margin: 0 !important;">${request.request_number}</p>
                </div>
                <div>
                  <label style="font-size: 14px !important; font-weight: 500 !important; color: #6b7280 !important; display: block !important; margin-bottom: 8px !important;">Status</label>
                  <p style="margin: 0 !important;">
                    <span style="
                      padding: 6px 12px !important;
                      display: inline-flex !important;
                      font-size: 14px !important;
                      font-weight: 600 !important;
                      border-radius: 9999px !important;
                      ${statusColor}
                    ">
                      ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                    </span>
                  </p>
                </div>
  
                <div>
                  <label style="font-size: 14px !important; font-weight: 500 !important; color: #6b7280 !important; display: block !important; margin-bottom: 8px !important;">Created Date</label>
                  <p style="font-size: 18px !important; color: #111827 !important; margin: 0 !important;">
                    <time datetime="${request.created_at}">${new Date(request.created_at).toLocaleDateString('en-US', { 
                      year: 'numeric', 
                      month: 'long', 
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    })}</time>
                  </p>
                </div>
                <div>
                  <label style="font-size: 14px !important; font-weight: 500 !important; color: #6b7280 !important; display: block !important; margin-bottom: 8px !important;">Created By</label>
                  <p style="font-size: 18px !important; color: #111827 !important; margin: 0 !important;">${request.created_by || 'System'}</p>
                </div>
              </div>
              ${request.notes ? `
                <div style="margin-top: 24px !important;">
                  <label style="font-size: 14px !important; font-weight: 500 !important; color: #6b7280 !important; display: block !important; margin-bottom: 8px !important;">Notes</label>
                  <p style="color: #111827 !important; background: white !important; padding: 12px !important; border-radius: 6px !important; border: 1px solid #e5e7eb !important; margin: 0 !important;">${request.notes}</p>
                </div>
              ` : ''}
            </div>
            
            <!-- Request Items -->
            <div>
              <h4 style="
                font-size: 18px !important;
                font-weight: 600 !important;
                color: #111827 !important;
                margin-bottom: 16px !important;
                display: flex !important;
                align-items: center !important;
              ">
                📦 Requested Items (${request.items.length})
              </h4>
              <div style="overflow-x: auto !important;">
                <table style="
                  min-width: 100% !important;
                  border: 1px solid #e5e7eb !important;
                  border-radius: 8px !important;
                  background: white !important;
                ">
                  <thead style="background: #f9fafb !important;">
                    <tr>
                      <th style="
                        padding: 12px 16px !important;
                        text-align: left !important;
                        font-size: 12px !important;
                        font-weight: 600 !important;
                        color: #6b7280 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        border-bottom: 1px solid #e5e7eb !important;
                      ">Product ID</th>
                      <th style="
                        padding: 12px 16px !important;
                        text-align: left !important;
                        font-size: 12px !important;
                        font-weight: 600 !important;
                        color: #6b7280 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        border-bottom: 1px solid #e5e7eb !important;
                      ">Product Name</th>
                      <th style="
                        padding: 12px 16px !important;
                        text-align: left !important;
                        font-size: 12px !important;
                        font-weight: 600 !important;
                        color: #6b7280 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        border-bottom: 1px solid #e5e7eb !important;
                      ">Quantity</th>
                      <th style="
                        padding: 12px 16px !important;
                        text-align: left !important;
                        font-size: 12px !important;
                        font-weight: 600 !important;
                        color: #6b7280 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        border-bottom: 1px solid #e5e7eb !important;
                      ">Unit</th>
                      <th style="
                        padding: 12px 16px !important;
                        text-align: left !important;
                        font-size: 12px !important;
                        font-weight: 600 !important;
                        color: #6b7280 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        border-bottom: 1px solid #e5e7eb !important;
                      ">Description</th>
                      <th style="
                        padding: 12px 16px !important;
                        text-align: left !important;
                        font-size: 12px !important;
                        font-weight: 600 !important;
                        color: #6b7280 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        border-bottom: 1px solid #e5e7eb !important;
                      ">Status</th>
                    </tr>
                  </thead>
                  <tbody style="background: white !important;">
                    ${itemsHtml}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        `;
        
        console.log('Setting innerHTML, content length:', detailsContent.length);
        document.getElementById('requestDetails').innerHTML = detailsContent;
        document.getElementById('requestDetails').style.display = 'block';
        document.getElementById('modalFooter').style.display = 'block';
        console.log('InnerHTML set successfully');
      }

      // Modal state management
      let modalState = {
        isOpen: false,
        currentRequestId: null,
        currentRequestNumber: null
      };

      /**
       * Handle cancel request with event delegation
       */
      function handleCancelRequest(event) {
        const button = event.target.closest('.cancel-request-btn');
        if (!button) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        const requestId = button.dataset.requestId;
        const requestNumber = button.dataset.requestNumber;
        
        if (!requestId || !requestNumber) {
          showError('Invalid request data');
          return;
        }
        
        // Store state
        modalState.currentRequestId = requestId;
        modalState.currentRequestNumber = requestNumber;
        
        // Update modal content
        document.getElementById('confirmMessage').textContent = 
          `Are you sure you want to cancel request ${requestNumber}? This action cannot be undone.`;
        
        // Show modal
        openConfirmModal();
      }
      
      // Event delegation for cancel buttons
      document.addEventListener('click', handleCancelRequest);
      

      
      function performCancel(requestId) {
        const confirmBtn = document.getElementById('confirmAction');
        
        try {
          // Validate request ID
          if (!requestId || !Number.isInteger(Number(requestId))) {
            showError('Invalid request ID');
            return;
          }
          
          // Show loading state
          if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block mr-2"></div>Cancelling...';
          } else {
            showLoading('Cancelling request...');
          }
          
          // Make API call
          const formData = new FormData();
          formData.append('request_id', requestId);
          
          fetch('../../cancel_request.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(`Request failed with status ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            closeConfirmModal();
            hideLoading();
            
            if (data.success) {
              showSuccess(data.message || 'Request cancelled successfully');
              updateRequestRowStatus(requestId, 'cancelled');
            } else {
              showError(data.error || 'Failed to cancel request');
            }
          })
          .catch(error => {
            closeConfirmModal();
            hideLoading();
            showError(error.message || 'Network error occurred');
          })
          .finally(() => {
            // Reset button
            if (confirmBtn) {
              confirmBtn.disabled = false;
              confirmBtn.innerHTML = 'Yes, Cancel Request';
            }
          });
        } catch (error) {
          console.error('Error in performCancel function:', error);
          hideLoading();
          showError('Internal error: ' + error.message);
          
          // Reset button
          if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Yes, Cancel Request';
          }
        }
      }
      
      function updateRequestRowStatus(requestId, newStatus) {
        const row = document.querySelector(`tr[data-request-id="${requestId}"]`);
        if (!row) {
          console.warn('Request row not found for ID:', requestId);
          return;
        }
        
        // Update status badge
        const statusCell = row.querySelector('td:nth-child(4) span');
        if (statusCell) {
          statusCell.className = 'px-3 py-1.5 inline-flex text-sm font-medium rounded-lg transition-all duration-200 bg-red-100 text-red-800 ring-1 ring-red-200';
          statusCell.textContent = 'Cancelled';
        }
        
        // Remove the cancel button
        const cancelBtn = row.querySelector('button[onclick*="cancelRequest"]');
        if (cancelBtn) {
          cancelBtn.remove();
        }
        
        // Add visual feedback
        row.style.transition = 'background-color 0.3s';
        row.style.backgroundColor = '#fef2f2';
        setTimeout(() => {
          row.style.backgroundColor = '';
        }, 2000);
        
        // Update table count if needed
        const countElement = document.querySelector('.text-sm.text-gray-500');
        if (countElement) {
          const currentText = countElement.textContent;
          const match = currentText.match(/Showing (\d+) request/);
          if (match) {
            const currentCount = parseInt(match[1]);
            countElement.textContent = `Showing ${currentCount} request${currentCount !== 1 ? 's' : ''}`;
          }
        }
      }
      
      function showLoading(message) {
        // Simple loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'globalLoading';
        loadingDiv.style.cssText = `
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0,0,0,0.5);
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 9999;
        `;
        loadingDiv.innerHTML = `
          <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
            <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
            <p>${message}</p>
          </div>
        `;
        document.body.appendChild(loadingDiv);
      }
      
      function hideLoading() {
        const loadingDiv = document.getElementById('globalLoading');
        if (loadingDiv) {
          loadingDiv.remove();
        }
      }

      function closeModal() {
        const modal = document.getElementById('requestModal');
        modal.classList.add('hidden');
        // Reset modal content
        document.getElementById('requestDetails').innerHTML = '';
      }

      /**
       * Open confirmation modal
       */
      function openConfirmModal() {
        const modal = document.getElementById('confirmModal');
        if (!modal) {
          console.error('Modal element not found!');
          return;
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        modalState.isOpen = true;
        
        // Set up the Yes button handler
        const yesBtn = document.getElementById('confirmAction');
        if (yesBtn) {
          yesBtn.onclick = function(event) {
            event.preventDefault();
            if (modalState.currentRequestId) {
              performCancel(modalState.currentRequestId);
            }
          };
          
          // Focus the button
          setTimeout(() => yesBtn.focus(), 100);
        }
      }

      /**
       * Close confirmation modal
       */
      function closeConfirmModal() {
        const modal = document.getElementById('confirmModal');
        if (!modal) return;
        
        // Clean up event listener
        const yesBtn = document.getElementById('confirmAction');
        if (yesBtn) {
          yesBtn.onclick = null;
        }
        
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Reset state
        modalState.isOpen = false;
        modalState.currentRequestId = null;
        modalState.currentRequestNumber = null;
      }

      function scanQRForRequest(requestNumber) {
        // Open scan QR page with request number as parameter
        const scanUrl = `<?php echo url('app/controllers/scanner.php'); ?>?request_number=${encodeURIComponent(requestNumber)}`;
        window.open(scanUrl, '_blank');
      }



      // Close modals when clicking outside
      document.getElementById('requestModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeModal();
        }
      });

      document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeConfirmModal();
        }
      });

      // Enhanced keyboard navigation with modal state
      document.addEventListener('keydown', function(e) {
        // Handle Escape key
        if (e.key === 'Escape') {
          if (modalState.isOpen) {
            closeConfirmModal();
          } else {
            closeModal();
          }
          return;
        }
        
        // Tab navigation within modals
        if (!modalState.isOpen) return;
        const activeModal = document.getElementById('confirmModal');
        if (activeModal && e.key === 'Tab') {
          const focusableElements = activeModal.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
          );
          const firstElement = focusableElements[0];
          const lastElement = focusableElements[focusableElements.length - 1];
          
          if (e.shiftKey) {
            if (document.activeElement === firstElement) {
              e.preventDefault();
              lastElement.focus();
            }
          } else {
            if (document.activeElement === lastElement) {
              e.preventDefault();
              firstElement.focus();
            }
          }
        }
      });
      
      // Focus management for modals
      function trapFocus(modal) {
        const focusableElements = modal.querySelectorAll(
          'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length > 0) {
          focusableElements[0].focus();
        }
      }

      // Mobile menu toggle (if needed in future)
      function toggleMobileMenu() {
        const sidebar = document.querySelector('aside');
        sidebar.classList.toggle('sidebar-mobile-hidden');
        sidebar.classList.toggle('sidebar-mobile-visible');
      }

      // Initialize page
      document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Focus management for modals
        const modals = document.querySelectorAll('[role="dialog"]');
        modals.forEach(modal => {
          modal.addEventListener('shown', () => {
            const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusableElements.length > 0) {
              focusableElements[0].focus();
            }
          });
        });
      });

      </script>
</body>

<body class="min-h-screen bg-gray-50" style="position: relative;">
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
                  Pengaturan
                </a>
                <a href="<?php echo url('app/logout.php'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                  <i class="bi bi-box-arrow-right mr-2"></i>
                  Keluar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="flex">
      <!-- Sidebar -->
      <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:fixed lg:inset-y-0 lg:left-0 lg:z-30">
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
                <?= ($department === 'rmw' ? 'Gudang Bahan Baku' : 'Departemen Produksi') ?>
              </h3>
              <div class="mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                  <?= $department === 'rmw' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                  <i class="bi bi-diagram-3 mr-1"></i>
                  <?= htmlspecialchars($userDivision ?? 'Unassigned') ?>
                </span>
              </div>
              <p class="text-sm text-gray-500 mt-2">Sistem Manajemen</p>
            </div>
          </div>
          
          <!-- Enhanced Navigation -->
          <nav class="space-y-1" role="navigation" aria-label="Main navigation">
            <!-- Dashboard -->
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" 
               class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'dashboard' ? 'nav-active' : 'nav-inactive' ?>"
               role="menuitem" aria-current="<?= $module_name == 'dashboard' ? 'page' : 'false' ?>">
              <i class="bi bi-house-fill mr-3 text-lg"></i>
              <span>Dasbor</span>
              <?= $module_name == 'dashboard' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
            </a>
            
            <?php if ($department === 'production'): ?>
            <!-- Production Navigation -->
            <div class="pt-2">
              <h4 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Produksi</h4>
              
              <a href="<?php echo url('app/controllers/material_request.php'); ?>" 
                 class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'material_request' ? 'nav-active' : 'nav-inactive' ?>"
                 role="menuitem" aria-current="<?= $module_name == 'material_request' ? 'page' : 'false' ?>">
                <i class="bi bi-plus-circle-fill mr-3 text-lg"></i>
                <span>Buat Permintaan</span>
                <?= $module_name == 'material_request' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
              </a>
              
              <a href="<?php echo url('app/controllers/my_requests.php'); ?>" 
                 class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'my_requests' ? 'nav-active' : 'nav-inactive' ?>"
                 role="menuitem" aria-current="<?= $module_name == 'my_requests' ? 'page' : 'false' ?>">
                <i class="bi bi-list-task mr-3 text-lg"></i>
                <span>Permintaan Saya</span>
                <?= $module_name == 'my_requests' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
              </a>
            </div>
            
            <?php else: ?>
            <!-- RMW Navigation -->
            <div class="pt-2">
              <h4 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Gudang</h4>
              
              <a href="<?php echo url('app/controllers/rmw_dashboard.php'); ?>" 
                 class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'rmw_dashboard' ? 'nav-active-rmw' : 'nav-inactive' ?>"
                 role="menuitem" aria-current="<?= $module_name == 'rmw_dashboard' ? 'page' : 'false' ?>">
                <i class="bi bi-box-seam-fill mr-3 text-lg"></i>
                <span>Dasbor Gudang</span>
                <?= $module_name == 'rmw_dashboard' ? '<div class="absolute right-2 w-2 h-2 bg-green-600 rounded-full animate-pulse"></div>' : '' ?>
              </a>
            </div>
            <?php endif; ?>
            
  
          </nav>
        </div>
      </aside>

      <!-- Main content -->
      <main id="main-content" class="flex-1 min-h-screen" role="main">
        <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-full">
          <!-- Page Header -->
          <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Permintaan Material Saya</h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-lg">Lihat dan lacak permintaan material Anda</p>
          </div>

        <!-- Alert Messages -->
        <?php if (isset($error_message)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm animate-fade-in" role="alert">
          <div class="flex">
            <i class="bi bi-exclamation-circle text-red-400 text-xl mr-3 flex-shrink-0" aria-hidden="true"></i>
            <div>
              <h3 class="text-sm font-medium text-red-800">Kesalahan!</h3>
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
              <h3 class="text-sm font-medium text-green-800">Berhasil!</h3>
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
                  <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                  <div class="relative">
                    <input 
                      type="text" 
                      id="searchInput"
                      name="search" 
                      value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                      placeholder="Cari berdasarkan nomor permintaan atau produk..." 
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
                    <option value="all" <?= ($_GET['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="diproses" <?= ($_GET['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
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

        <!-- Optimized Requests Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h3 class="text-base font-medium text-gray-900">Permintaan Material</h3>
              <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-500">
                  <?php if (!empty($userRequests)): ?>
                    <?= $totalRequests ?> permintaan
                  <?php endif; ?>
                </span>
              </div>
            </div>
          </div>
          
          <!-- Loading State -->
          <div id="tableLoading" class="hidden">
            <div class="px-4 py-3 space-y-3">
              <?php for ($i = 0; $i < 3; $i++): ?>
              <div class="animate-pulse">
                <div class="flex space-x-4 items-center p-3 border border-gray-100 rounded-lg">
                  <div class="h-4 bg-gray-200 rounded w-20"></div>
                  <div class="h-4 bg-gray-200 rounded w-24"></div>
                  <div class="h-4 bg-gray-200 rounded w-16"></div>
                  <div class="h-4 bg-gray-200 rounded w-12"></div>
                  <div class="h-4 bg-gray-200 rounded w-20 flex-1"></div>
                  <div class="h-8 bg-gray-200 rounded w-16"></div>
                </div>
              </div>
              <?php endfor; ?>
            </div>
          </div>
          
          <!-- Responsive Table -->
          <div class="overflow-x-auto">
            <table class="w-full" role="table" aria-label="Material requests">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider" scope="col">Permintaan</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider" scope="col">Detail</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden sm:table-cell" scope="col">Item</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider" scope="col">Status</th>
                  <th class="px-4 py-2 text-right text-xs font-medium text-gray-600 uppercase tracking-wider" scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100" id="requestsTableBody">
                <?php if (!empty($userRequests)): ?>
                  <?php foreach ($userRequests as $request): ?>
                  <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-100" data-request-id="<?= $request['id'] ?>">
                    <!-- Request Number & Date -->
                    <td class="px-4 py-3">
                      <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900 font-mono"><?= htmlspecialchars($request['request_number']) ?></div>
                        <div class="text-xs text-gray-500 mt-1">
                          <time datetime="<?= $request['created_at'] ?>"><?= date('M j, Y', strtotime($request['created_at'])) ?></time>
                        </div>
                      </div>
                    </td>
                    
                    <!-- Created By & Notes -->
                    <td class="px-4 py-3">
                      <div class="min-w-0">
                        <div class="text-sm text-gray-700 flex items-center">
                          <i class="bi bi-person-circle text-gray-400 mr-1.5" aria-hidden="true"></i>
                          <span class="font-medium truncate"><?= htmlspecialchars($request['created_by'] ?? 'System') ?></span>
                        </div>
                        <?php if (!empty($request['notes'])): ?>
                        <div class="text-xs text-gray-500 mt-1 truncate max-w-xs" title="<?= htmlspecialchars($request['notes']) ?>">
                          <?= htmlspecialchars($request['notes']) ?>
                        </div>
                        <?php endif; ?>
                      </div>
                    </td>
                    
                    <!-- Item Count (hidden on mobile) -->
                    <td class="px-4 py-3 hidden sm:table-cell">
                      <div class="text-sm text-gray-700 flex items-center">
                        <i class="bi bi-box-seam text-gray-400 mr-1.5" aria-hidden="true"></i>
                        <span class="font-medium"><?= $request['item_count'] ?></span>
                        <span class="text-xs text-gray-500 ml-1">item</span>
                      </div>
                    </td>
                    
                    <!-- Status Badge -->
                    <td class="px-4 py-3">
                      <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full transition-all duration-200
                        <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($request['status'] === 'diproses' ? 'bg-blue-100 text-blue-800' : 
                           ($request['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) ?>">
                        <?= ucfirst($request['status']) ?>
                      </span>
                    </td>
                    
                    <!-- Actions -->
                    <td class="px-4 py-3">
                      <div class="flex items-center justify-end space-x-1">
                        <!-- View Button (always visible) -->
                        <button 
                          onclick="viewRequest(<?= $request['id'] ?>)" 
                          class="inline-flex items-center justify-center w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                          aria-label="View request <?= htmlspecialchars($request['request_number']) ?>"
                          title="View details">
                          <i class="bi bi-eye text-sm" aria-hidden="true"></i>
                        </button>
                        
                        <!-- Scan Button (for processing requests) -->
                        <?php if ($request['status'] === 'diproses'): ?>
                        <button 
                          onclick="scanQRForRequest('<?= htmlspecialchars($request['request_number']) ?>')" 
                          class="inline-flex items-center justify-center w-8 h-8 bg-green-50 hover:bg-green-100 text-green-700 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1"
                          aria-label="Scan request <?= htmlspecialchars($request['request_number']) ?>"
                          title="Scan QR code">
                          <i class="bi bi-upc-scan text-sm" aria-hidden="true"></i>
                        </button>
                        <?php endif; ?>
                        
                        <!-- Cancel Button (for pending requests) -->
                        <?php if ($request['status'] === 'pending'): ?>
                        <button 
                          class="cancel-request-btn inline-flex items-center justify-center w-8 h-8 bg-red-50 hover:bg-red-100 text-red-700 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                          data-request-id="<?= $request['id'] ?>"
                          data-request-number="<?= htmlspecialchars($request['request_number']) ?>"
                          aria-label="Cancel request <?= htmlspecialchars($request['request_number']) ?>"
                          title="Cancel request">
                          <i class="bi bi-x-lg text-sm" aria-hidden="true"></i>
                        </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                      <div class="space-y-3">
                        <i class="bi bi-inbox text-4xl text-gray-300 block mx-auto" aria-hidden="true"></i>
                        <div>
                          <h4 class="text-base font-semibold text-gray-900 mb-1">Tidak ada permintaan ditemukan</h4>
                          <p class="text-gray-600 text-sm mb-3">Buat permintaan material pertama Anda untuk memulai</p>
                          <a 
                            href="<?php echo url('app/controllers/material_request.php'); ?>" 
                            class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="bi bi-plus-circle mr-1.5" aria-hidden="true"></i>
                            Buat Permintaan
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
          <div class="px-4 sm:px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
              <div class="text-sm text-gray-700 text-center sm:text-left">
                Menampilkan 
                <span class="font-medium"><?= min((($page - 1) * $limit) + 1, $totalRequests) ?></span> 
                hingga 
                <span class="font-medium"><?= min($page * $limit, $totalRequests) ?></span> 
                dari 
                <span class="font-medium"><?= $totalRequests ?></span> 
                hasil
              </div>
              
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <!-- Previous page -->
                <?php if ($page > 1): ?>
                  <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                     class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Sebelumnya</span>
                    <i class="bi bi-chevron-left"></i>
                  </a>
                <?php else: ?>
                  <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                    <span class="sr-only">Sebelumnya</span>
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
                    <span class="sr-only">Selanjutnya</span>
                    <i class="bi bi-chevron-right"></i>
                  </a>
                <?php else: ?>
                  <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                    <span class="sr-only">Selanjutnya</span>
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
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Detail Permintaan</h3>
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
                <p class="text-gray-600">Memuat detail permintaan...</p>
              </div>
            </div>
            
            <!-- Error State -->
            <div id="modalError" class="hidden">
              <div class="flex flex-col items-center justify-center py-12 space-y-4">
                <i class="bi bi-exclamation-triangle text-red-500 text-5xl" aria-hidden="true"></i>
                <div class="text-center">
                  <h4 class="text-lg font-medium text-gray-900 mb-2">Kesalahan Memuat Detail</h4>
                  <p class="text-gray-600" id="modalErrorMessage">Gagal memuat detail permintaan</p>
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
                Tutup
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
          <h3 id="confirmTitle">Konfirmasi Pembatalan</h3>
        </div>
        <p id="confirmMessage">Apakah Anda yakin ingin membatalkan permintaan ini?</p>
        <div class="modal-actions">
          <button 
            id="confirmCancel"
            type="button"
            onclick="closeConfirmModal()" 
            class="btn btn-secondary">
            Tidak, Simpan Permintaan
          </button>
          <button 
            id="confirmAction"
            type="button"
            class="btn btn-danger">
            Ya, Batalkan Permintaan
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
      
      /* Main content transition for mobile sidebar */
      #main-content {
        transition: transform 0.3s ease-in-out;
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
      
      /* Ensure proper responsive layout */
      @media (min-width: 1024px) {
        /* Desktop: sidebar is visible, content has proper margin */
        #main-content {
          margin-left: 16rem; /* 64 * 0.25rem = 16rem */
        }
        
        header {
          margin-left: 16rem; /* Match main content margin */
        }
        
        body {
          overflow-x: auto;
        }
      }
      
      @media (max-width: 1023px) {
        /* Mobile/tablet: sidebar is hidden by default */
        #main-content {
          margin-left: 0;
        }
        
        header {
          margin-left: 0;
        }
        
        body {
          overflow-x: hidden;
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
        const mainContent = document.getElementById('main-content');
        
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // On mobile, slide content to the right when sidebar opens
        if (window.innerWidth < 1024) {
          mainContent.style.transform = 'translateX(16rem)';
        }
      }
      
      function closeMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        const mainContent = document.getElementById('main-content');
        
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Reset content position
        mainContent.style.transform = '';
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

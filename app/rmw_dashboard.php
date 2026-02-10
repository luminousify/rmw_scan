<?php
// Helper function to build pagination URLs with current filters
function buildPaginationUrl($page, $perPage = null) {
    $params = $_GET;
    $params['page'] = $page;
    if ($perPage !== null) {
        $params['per_page'] = $perPage;
    }
    $query = http_build_query($params);
    return 'rmw_dashboard.php' . ($query ? '?' . $query : '');
}
?>
<body class="min-h-screen bg-gray-50">
    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="<?php echo url('app/controllers/rmw_dashboard.php'); ?>" class="text-xl font-bold text-gray-900">
              <?= ($department === 'rmw' ? 'RMW System' : 'Scan No Bon') ?>
            </a>
          </div>
          
          <!-- Right side -->
          <div class="flex items-center space-x-4">
            <!-- User info -->
            <span class="text-sm font-medium text-gray-700"><?=strtoupper($name)?> (<?=ucfirst($department ?? 'rmw')?>)</span>
            
            <!-- User Menu-->
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
      <?php include 'common/sidebar.php'; ?>

      <!-- Main content -->
      <main class="flex-1 p-6">
        <!-- Dashboard Content -->
        <div class="dashboard-container space-y-6">
          <!-- Dashboard Title -->
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-2">Selamat datang kembali, <?= $name ?>!</p>
          </div>

        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
          <div class="flex">
            <i class="bi bi-check-circle text-green-400 text-xl mr-3"></i>
            <div>
              <h3 class="text-sm font-medium text-green-800">Berhasil!</h3>
              <p class="text-sm text-green-700 mt-1"><?= $success_message ?></p>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <i class="bi bi-exclamation-circle text-red-400 text-xl mr-3"></i>
            <div>
              <h3 class="text-sm font-medium text-red-800">Kesalahan!</h3>
              <p class="text-sm text-red-700 mt-1"><?= $error_message ?></p>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-container grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="bi bi-clock text-yellow-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Menunggu</p>
                <p class="stats-pending text-2xl font-bold text-gray-900"><?= $stats['pending'] ?? 0 ?></p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-blue-100 rounded-lg">
                <i class="bi bi-gear text-blue-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                <p class="stats-approved text-2xl font-bold text-gray-900"><?= $stats['approved'] ?? 0 ?></p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-purple-100 rounded-lg">
                <i class="bi bi-box text-purple-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Sudah Siap</p>
                <p class="stats-ready text-2xl font-bold text-gray-900"><?= $stats['ready'] ?? 0 ?></p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-green-100 rounded-lg">
                <i class="bi bi-check-circle text-green-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Selesai</p>
                <p class="stats-completed text-2xl font-bold text-gray-900"><?= $stats['completed'] ?? 0 ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
          <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
              <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
              <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                     placeholder="Cari berdasarkan nomor permintaan, pengguna, atau produk..." 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            
            <div class="min-w-48">
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="all" <?= ($_GET['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                <option value="diproses" <?= ($_GET['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
              </select>
            </div>
            
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium">
              <i class="bi bi-search mr-2"></i>Filter
            </button>
            
            <a href="<?php echo url('app/controllers/rmw_dashboard.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium">
              <i class="bi bi-arrow-clockwise mr-2"></i>Reset
            </a>
          </form>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Permintaan Material</h3>
          </div>
          
          <div class="overflow-x-auto">
            <table class="requests-table min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Permintaan</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna Produksi</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($requests)): ?>
                  <?php foreach ($requests as $request): ?>
                  <tr data-request-id="<?= $request['id'] ?>" 
                      data-created-at="<?= htmlspecialchars($request['created_at']) ?>"
                      data-updated-at="<?= htmlspecialchars($request['updated_at']) ?>"
                      class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      <?= htmlspecialchars($request['request_number']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div>
                        <div class="font-medium text-gray-900"><?= htmlspecialchars($request['production_user_name'] ?? 'Unknown') ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($request['production_division'] ?? 'Unassigned') ?></div>
                      </div>
                    </td>
                    <td class="request-date px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <?= date('M d, Y H:i', strtotime($request['created_at'])) ?>
                    </td>
    
                    <td class="px-6 py-4 text-sm text-gray-500">
                      <?= $request['item_count'] ?> item
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="request-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           ($request['status'] === 'approved' ? 'bg-blue-100 text-blue-800' :
                           ($request['status'] === 'ready' ? 'bg-purple-100 text-purple-800' :
                           ($request['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))) ?>">
                        <?php
                        $statusIndo = [
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'ready' => 'Sudah Siap',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan'
                        ];
                        echo $statusIndo[$request['status']] ?? ucfirst($request['status']);
                        ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <button onclick="viewRequest(<?= $request['id'] ?>)" 
                                class="text-blue-600 hover:text-blue-900">
                          <i class="bi bi-eye"></i> Lihat
                        </button>
                        
                        <?php if ($request['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline;">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                          <input type="hidden" name="status" value="approved">
                          <button type="submit" 
                                  onclick="return confirm('Setujui permintaan ini?')"
                                  class="text-green-600 hover:text-green-900">
                            <i class="bi bi-check-circle"></i> Setujui
                          </button>
                        </form>
                        <?php elseif ($request['status'] === 'approved'): ?>
                        <form method="POST" style="display: inline;">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                          <input type="hidden" name="status" value="ready">
                          <button type="submit" 
                                  onclick="return confirm('Tandai sebagai Sudah Siap?')"
                                  class="text-purple-600 hover:text-purple-900">
                            <i class="bi bi-check2-circle"></i> Sudah Siap
                          </button>
                        </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                      <i class="bi bi-inbox text-4xl mb-4 block"></i>
                      Tidak ada permintaan ditemukan
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination Controls -->
          <?php if (isset($pagination)): ?>
          <div class="pagination-container px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <div class="flex items-center text-sm text-gray-700">
                <span class="pagination-info">Menampilkan 
                  <span class="font-medium"><?= number_format($pagination['start_record']) ?></span> 
                  hingga 
                  <span class="font-medium"><?= number_format($pagination['end_record']) ?></span> 
                  dari 
                  <span class="font-medium"><?= number_format($pagination['total_records']) ?></span> 
                  hasil
                </span>
              </div>
              
              <div class="flex items-center space-x-4">
                <!-- Per Page Selector -->
                <div class="flex items-center space-x-2">
                  <label class="text-sm text-gray-700">Tampilkan:</label>
                  <select id="perPageSelect" class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="5" <?= $pagination['per_page'] === 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $pagination['per_page'] === 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $pagination['per_page'] === 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $pagination['per_page'] === 50 ? 'selected' : '' ?>>50</option>
                  </select>
                </div>

                <!-- Page Navigation - Only show if more than 1 page -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <div class="flex items-center space-x-1">
                  <!-- Previous Button -->
                  <?php if ($pagination['has_prev']): ?>
                    <a href="<?= buildPaginationUrl($pagination['prev_page']) ?>" 
                       class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                      <i class="bi bi-chevron-left"></i> Sebelumnya
                    </a>
                  <?php else: ?>
                    <span class="px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                      <i class="bi bi-chevron-left"></i> Sebelumnya
                    </span>
                  <?php endif; ?>

                  <!-- Page Numbers -->
                  <?php
                  $currentPage = $pagination['current_page'];
                  $totalPages = $pagination['total_pages'];
                  $startPage = max(1, $currentPage - 2);
                  $endPage = min($totalPages, $currentPage + 2);
                  
                  if ($startPage > 1) {
                      echo '<a href="' . buildPaginationUrl(1) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">1</a>';
                      if ($startPage > 2) {
                          echo '<span class="px-2 text-gray-500">...</span>';
                      }
                  }
                  
                  for ($i = $startPage; $i <= $endPage; $i++) {
                      if ($i === $currentPage) {
                          echo '<span class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-md">' . $i . '</span>';
                      } else {
                          echo '<a href="' . buildPaginationUrl($i) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">' . $i . '</a>';
                      }
                  }
                  
                  if ($endPage < $totalPages) {
                      if ($endPage < $totalPages - 1) {
                          echo '<span class="px-2 text-gray-500">...</span>';
                      }
                      echo '<a href="' . buildPaginationUrl($totalPages) . '" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">' . $totalPages . '</a>';
                  }
                  ?>

                  <!-- Next Button -->
                  <?php if ($pagination['has_next']): ?>
                    <a href="<?= buildPaginationUrl($pagination['next_page']) ?>" 
                       class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                      Selanjutnya <i class="bi bi-chevron-right"></i>
                    </a>
                  <?php else: ?>
                    <span class="px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                      Selanjutnya <i class="bi bi-chevron-right"></i>
                    </span>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        </div>
      </main>
    </div>

  

    <!-- Dashboard Updater Script -->
    <script src="<?php echo url('includes/js/dashboard-updater.js'); ?>"></script>

    <!-- Initialize Dashboard Updater with correct endpoint -->
    <script>
      // Override default endpoint with absolute URL
      document.addEventListener('DOMContentLoaded', function() {
        const dashboardContainer = document.querySelector('.dashboard-container');
        
        if (dashboardContainer && typeof DashboardUpdater !== 'undefined') {
          // Remove any existing auto-initialized updater
          if (window.dashboardUpdater) {
            window.dashboardUpdater.destroy();
            window.dashboardUpdater = null;
          }
          
          // Initialize with correct absolute endpoint
          window.dashboardUpdater = new DashboardUpdater({
            endpoint: '<?php echo url('app/controllers/get_dashboard_updates.php'); ?>',
            debugMode: true,
            enableNotifications: false,
            onConnectionChange: function(status) {
              console.log('Dashboard connection status:', status);
            }
          });
          
          console.log('Dashboard updater initialized with endpoint:', '<?php echo url('app/controllers/get_dashboard_updates.php'); ?>');
        }
      });
    </script>

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

      function viewRequest(requestId) {
        // Cache DOM elements for performance
        const modalCache = viewRequest.modalCache || {};
        viewRequest.modalCache = modalCache;
        
        // Remove any existing modal with cleanup
        if (modalCache.existingModal) {
            modalCache.existingModal.remove();
            modalCache.existingModal = null;
        }
        
        const existingTest = document.getElementById('testModal');
        if (existingTest) {
            existingTest.remove();
        }
        
        // Create a working modal with proper structure
        const modal = document.createElement('div');
        modal.id = 'requestModal';
        modalCache.existingModal = modal;
        
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
                        Detail Permintaan
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
                        border-top: 4px solid #10b981 !important;
                        border-radius: 50% !important;
                        animation: spin 1s linear infinite !important;
                        margin-bottom: 16px !important;
                    "></div>
                    <p style="color: #6b7280 !important; margin: 0 !important;">Memuat detail permintaan...</p>
                </div>
                
                <!-- Error State -->
                <div id="modalError" style="display: none !important; padding: 60px !important; text-align: center !important;">
                    <div style="font-size: 48px !important; color: #ef4444 !important; margin-bottom: 16px !important;">⚠️</div>
                    <h4 style="font-size: 18px !important; font-weight: 600 !important; color: #111827 !important; margin-bottom: 8px !important;">Kesalahan Memuat Detail</h4>
                    <p id="modalErrorMessage" style="color: #6b7280 !important; margin-bottom: 16px !important;">Gagal memuat detail permintaan</p>
                    <button onclick="this.closest('#requestModal').remove()" style="
                        background: #6b7280 !important;
                        color: white !important;
                        padding: 8px 16px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                    ">Tutup</button>
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
                    ">Tutup</button>
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
        const url = `rmw_dashboard.php?action=get_request_details&id=${requestId}`;
        
        fetch(url, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          cache: 'no-cache'
        })
          .then(response => {
            
            // Check content type to ensure we're getting JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
              throw new Error(`Expected JSON response, got: ${contentType || 'unknown'}`);
            }
            
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Get response as text first to validate JSON
            return response.text();
          })
          .then(text => {
            
            // Validate that response starts with JSON object/array
            const trimmed = text.trim();
            if (!trimmed.startsWith('{') && !trimmed.startsWith('[')) {
              throw new Error('Invalid JSON response format');
            }
            
            try {
              return JSON.parse(text);
            } catch (parseError) {
              throw new Error(`JSON parse error: ${parseError.message}`);
            }
          })
          .then(data => {
            document.getElementById('modalLoading').style.display = 'none';
            
            // Validate response structure
            if (!data || typeof data !== 'object') {
              throw new Error('Invalid response structure');
            }
            
            if (data.success) {
              if (!data.request) {
                throw new Error('Missing request data in response');
              }
              displayRequestDetails(data.request);
            } else {
              const errorMessage = data.error || 'Failed to load request details';
              const errorType = data.error_type || 'Unknown';
              
              document.getElementById('modalErrorMessage').textContent = `${errorMessage} (${errorType})`;
              document.getElementById('modalError').style.display = 'block';
            }
          })
          .catch(error => {
            document.getElementById('modalLoading').style.display = 'none';
            
            let errorMessage = error.message;
            let detailedInfo = '';
            
            // Provide more user-friendly error messages
            if (error.message.includes('Expected JSON response')) {
              errorMessage = 'Server returned invalid response format';
              detailedInfo = 'The server may be experiencing technical difficulties. Please try again.';
            } else if (error.message.includes('JSON parse error')) {
              errorMessage = 'Failed to process server response';
              detailedInfo = 'There may be an issue with the data format. Contact support if this persists.';
            } else if (error.message.includes('HTTP error')) {
              errorMessage = 'Server communication error';
              detailedInfo = 'Please check your connection and try again.';
            } else if (error.message.includes('Failed to fetch')) {
              errorMessage = 'Network connection failed';
              detailedInfo = 'Please check your internet connection and try again.';
            }
            
            const fullErrorMessage = detailedInfo ? `${errorMessage}: ${detailedInfo}` : errorMessage;
            document.getElementById('modalErrorMessage').textContent = fullErrorMessage;
            document.getElementById('modalError').style.display = 'block';
          });
      }
      
      function displayRequestDetails(request) {
        
        const itemsHtml = request.items.map(item => `
          <tr style="border-bottom: 1px solid #e5e7eb !important;">
            <td style="padding: 12px 16px !important; font-size: 14px !important; font-family: monospace !important; color: #111827 !important;">${item.product_id}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.product_name}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.requested_quantity}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.unit}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.description || '-'}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.machine || '-'}</td>
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
              display: grid !important;
              grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
              gap: 20px !important;
              padding: 20px !important;
              background: #f9fafb !important;
              border-radius: 8px !important;
              border: 1px solid #e5e7eb !important;
            ">
              <div>
                <h4 style="font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; margin-bottom: 4px !important; letter-spacing: 0.5px !important;">Nomor Permintaan</h4>
                <p style="font-size: 16px !important; font-weight: 600 !important; color: #111827 !important; margin: 0 !important;">${request.request_number}</p>
              </div>
              <div>
                <h4 style="font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; margin-bottom: 4px !important; letter-spacing: 0.5px !important;">Status</h4>
                <span style="
                  padding: 6px 12px !important;
                  display: inline-flex !important;
                  font-size: 14px !important;
                  font-weight: 600 !important;
                  border-radius: 6px !important;
                  ${statusColor}
                ">
                  ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                </span>
              </div>
  
              <div>
                <h4 style="font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; margin-bottom: 4px !important; letter-spacing: 0.5px !important;">Nomor Bon</h4>
                <p style="font-size: 16px !important; font-weight: 500 !important; color: #111827 !important; margin: 0 !important;">${request.customer_reference || '-'}</p>
              </div>
            </div>
            
            <!-- Request Information -->
            <div style="display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 20px !important;">
              <div style="padding: 20px !important; background: white !important; border-radius: 8px !important; border: 1px solid #e5e7eb !important;">
                <h4 style="font-size: 14px !important; font-weight: 600 !important; color: #111827 !important; margin-bottom: 16px !important;">Informasi Produksi</h4>
                <div style="display: flex !important; flex-direction: column !important; gap: 12px !important;">
                  <div style="display: flex !important; justify-content: space-between !important;">
                    <span style="font-size: 14px !important; color: #6b7280 !important;">Pengguna:</span>
                    <span style="font-size: 14px !important; font-weight: 500 !important; color: #111827 !important;">${request.production_user_name || 'Tidak diketahui'}</span>
                  </div>
                  <div style="display: flex !important; justify-content: space-between !important;">
                    <span style="font-size: 14px !important; color: #6b7280 !important;">Departemen:</span>
                    <span style="font-size: 14px !important; font-weight: 500 !important; color: #111827 !important;">${request.production_department || 'Tidak diketahui'}</span>
                  </div>
                  <div style="display: flex !important; justify-content: space-between !important;">
                    <span style="font-size: 14px !important; color: #6b7280 !important;">Dibuat:</span>
                    <span style="font-size: 14px !important; font-weight: 500 !important; color: #111827 !important;">${new Date(request.created_at).toLocaleString()}</span>
                  </div>
                </div>
              </div>
              
              <div style="padding: 20px !important; background: white !important; border-radius: 8px !important; border: 1px solid #e5e7eb !important;">
                <h4 style="font-size: 14px !important; font-weight: 600 !important; color: #111827 !important; margin-bottom: 16px !important;">Informasi Pemrosesan</h4>
                <div style="display: flex !important; flex-direction: column !important; gap: 12px !important;">
                  <div style="display: flex !important; justify-content: space-between !important;">
                    <span style="font-size: 14px !important; color: #6b7280 !important;">Diproses Oleh:</span>
                    <span style="font-size: 14px !important; font-weight: 500 !important; color: #111827 !important;">${request.processed_by || 'Belum diproses'}</span>
                  </div>
                  <div style="display: flex !important; justify-content: space-between !important;">
                    <span style="font-size: 14px !important; color: #6b7280 !important;">Tanggal Diproses:</span>
                    <span style="font-size: 14px !important; font-weight: 500 !important; color: #111827 !important;">${request.processed_date ? new Date(request.processed_date).toLocaleString() : 'Belum diproses'}</span>
                  </div>
                  <div style="display: flex !important; justify-content: space-between !important;">
                    <span style="font-size: 14px !important; color: #6b7280 !important;">Tanggal Selesai:</span>
                    <span style="font-size: 14px !important; font-weight: 500 !important; color: #111827 !important;">${request.completed_date ? new Date(request.completed_date).toLocaleString() : 'Belum selesai'}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Notes -->
            ${request.notes ? `
              <div style="padding: 20px !important; background: #f0f9ff !important; border-radius: 8px !important; border: 1px solid #bae6fd !important;">
                <h4 style="font-size: 14px !important; font-weight: 600 !important; color: #111827 !important; margin-bottom: 12px !important;">Catatan</h4>
                <p style="font-size: 14px !important; color: #374151 !important; margin: 0 !important; line-height: 1.5 !important;">${request.notes}</p>
              </div>
            ` : ''}
            
            <!-- Items Table -->
            <div>
              <h4 style="font-size: 16px !important; font-weight: 600 !important; color: #111827 !important; margin-bottom: 16px !important;">Item yang Diminta</h4>
              <div style="border: 1px solid #e5e7eb !important; border-radius: 8px !important; overflow: hidden !important;">
                <table style="width: 100% !important; border-collapse: collapse !important;">
                  <thead style="background: #f9fafb !important;">
                    <tr>
                      <th style="padding: 12px 16px !important; text-align: left !important; font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important;">ID Produk</th>
                      <th style="padding: 12px 16px !important; text-align: left !important; font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important;">Nama Produk</th>
                      <th style="padding: 12px 16px !important; text-align: left !important; font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important;">Jumlah</th>
                      <th style="padding: 12px 16px !important; text-align: left !important; font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important;">Satuan</th>
                      <th style="padding: 12px 16px !important; text-align: left !important; font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important;">Deskripsi</th>
                      <th style="padding: 12px 16px !important; text-align: left !important; font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important;">Mesin</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${itemsHtml}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        `;
        
        document.getElementById('requestDetails').innerHTML = detailsContent;
        document.getElementById('requestDetails').style.display = 'block';
        document.getElementById('modalFooter').style.display = 'block';
      }

      // Helper function to build pagination URLs with current filters
      function buildPaginationUrl(page, perPage = null) {
        const params = new URLSearchParams(window.location.search);
        params.set('page', page);
        if (perPage !== null) {
          params.set('per_page', perPage);
        }
        return 'rmw_dashboard.php' + (params.toString() ? '?' + params.toString() : '');
      }

      // Handle per-page selector change
      document.addEventListener('DOMContentLoaded', function() {
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
          perPageSelect.addEventListener('change', function() {
            const perPage = this.value;
            window.location.href = buildPaginationUrl(1, perPage);
          });
        }

        // Smooth scroll to table when pagination links are clicked
        document.querySelectorAll('a[href*="rmw_dashboard.php"]').forEach(link => {
          link.addEventListener('click', function(e) {
            // Check if this is a pagination link (has page parameter)
            const url = new URL(this.href);
            if (url.searchParams.has('page')) {
              setTimeout(() => {
                const table = document.querySelector('.bg-white.rounded-lg.shadow-sm');
                if (table) {
                  table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
              }, 100);
            }
          });
        });
      });

        // Initialize Dashboard Updater with RMW-specific configuration
        document.addEventListener('DOMContentLoaded', function() {
          // Wait for the auto-initialization to complete, then reconfigure
          setTimeout(() => {
            if (window.dashboardUpdater) {
              // Reconfigure updater with dashboard-specific settings
              window.dashboardUpdater.options.debugMode = false; // Disable debug mode in production
              window.dashboardUpdater.options.enableNotifications = false; // Disable notifications initially to prevent issues
              window.dashboardUpdater.options.fastInterval = 5000; // 5 seconds for active RMW users (reduced frequency)
              window.dashboardUpdater.options.updateInterval = 10000; // 10 seconds default (increased for performance)
              window.dashboardUpdater.options.businessHours = { start: 7, end: 18 }; // RMW extended hours
              window.dashboardUpdater.options.maxRetries = 5; // Increase retry attempts
              
              // Custom callbacks for RMW dashboard
              window.dashboardUpdater.options.onConnectionChange = function(status) {
                // Show visual feedback for connection issues
                if (status === 'error') {
                  // Could show a user-friendly notification here
                }
              };
              
              window.dashboardUpdater.options.onStatsUpdate = function(stats) {
                // Optional: Add visual emphasis for critical status changes
                
                // Flash important status changes
                const pendingCount = stats.pending || 0;
                if (pendingCount > 10) {
                  // High pending requests - could add visual warning
                }
              };
              
              window.dashboardUpdater.options.onRequestUpdate = function(requests) {
                // Check for time-sensitive requests that might need attention
                requests.forEach(request => {
                  if (request.status === 'approved' && !request.updated_fields.includes('processed_by')) {
                    // New approved request ready for processing
                    // Could highlight or notify user
                  }
                });
              };
              
              // Request notification permission for better UX
              if (window.dashboardUpdater.options.enableNotifications && 'Notification' in window) {
                if (Notification.permission === 'default') {
                  Notification.requestPermission().then(permission => {
                    // Permission handled
                  });
                }
              }
            } else {
              // Dashboard updater not found
            }
          }, 200);
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

      /* Real-time Update Animations */
      @keyframes statUpdate {
        0% {
          transform: scale(1);
          background-color: rgba(16, 185, 129, 0);
        }
        50% {
          transform: scale(1.1);
          background-color: rgba(16, 185, 129, 0.1);
        }
        100% {
          transform: scale(1);
          background-color: rgba(16, 185, 129, 0);
        }
      }

      .stat-updated {
        animation: statUpdate 1s ease-out;
      }

      @keyframes newRow {
        0% {
          opacity: 0;
          transform: translateY(-20px);
          background-color: rgba(16, 185, 129, 0.2);
        }
        100% {
          opacity: 1;
          transform: translateY(0);
          background-color: rgba(16, 185, 129, 0);
        }
      }

      .row-new {
        animation: newRow 0.5s ease-out;
        background-color: rgba(16, 185, 129, 0.1);
        border-left: 4px solid #10b981;
      }

      @keyframes rowUpdate {
        0% {
          background-color: rgba(59, 130, 246, 0);
          border-left-color: transparent;
        }
        50% {
          background-color: rgba(59, 130, 246, 0.1);
          border-left-color: #3b82f6;
        }
        100% {
          background-color: rgba(59, 130, 246, 0);
          border-left-color: transparent;
        }
      }

      .row-updated {
        animation: rowUpdate 2s ease-out;
      }

      @keyframes rowRemove {
        0% {
          opacity: 1;
          transform: translateX(0);
        }
        100% {
          opacity: 0;
          transform: translateX(20px);
        }
      }

      .row-removed {
        animation: rowRemove 0.5s ease-out;
      }

      /* Connection Status Indicator */
      .connection-indicator {
        transition: all 0.3s ease;
      }

      .connection-indicator.connected {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
      }

      .connection-indicator.connecting {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
      }

      .connection-indicator.error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
      }

      /* Loading states */
      .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
      }

      .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #10b981;
        border-radius: 50%;
        animation: spin 1s linear infinite;
      }

      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
    </style>
  </body>
</html>

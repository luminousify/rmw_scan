<body class="min-h-screen bg-gray-50">
    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" class="text-xl font-bold text-gray-900">
              Scan No Bon
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
            
            <!-- Tools section removed - QR Scanner access only through action buttons -->
          </nav>
        </div>
      </aside>

      <!-- Main content -->
      <main class="flex-1 p-6">



<!-- Dashboard Content -->
<div class="space-y-6">
  <!-- Dashboard Title -->
  <div>
    <h1 class="text-3xl font-bold text-gray-900">
      <?= ($department === 'rmw' ? 'Warehouse Dashboard' : 'Production Dashboard') ?>
    </h1>
    <p class="text-gray-600 mt-2">Welcome back, <?= $name ?>! Here's your activity overview.</p>
  </div>

  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total Requests Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
          <i class="bi bi-file-text text-blue-600 text-xl"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Total Requests</p>
          <p class="text-2xl font-bold text-gray-900" id="totalRequests">0</p>
        </div>
      </div>
    </div>

    <!-- Pending Requests Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
          <i class="bi bi-clock text-yellow-600 text-xl"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Pending</p>
          <p class="text-2xl font-bold text-gray-900" id="pendingRequests">0</p>
        </div>
      </div>
    </div>

    <!-- Approved Requests Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
          <i class="bi bi-check-circle text-green-600 text-xl"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Approved</p>
          <p class="text-2xl font-bold text-gray-900" id="approvedRequests">0</p>
        </div>
      </div>
    </div>

    <!-- Scanned Items Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
          <i class="bi bi-qr-code text-purple-600 text-xl"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Items Scanned</p>
          <p class="text-2xl font-bold text-gray-900" id="scannedItems">0</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Request Trends Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Trends (Last 7 Days)</h3>
      <div class="aspect-video">
        <canvas id="requestTrendsChart" class="w-full h-full"></canvas>
      </div>
    </div>

    <!-- Request Status Distribution -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Status Distribution</h3>
      <div class="aspect-video">
        <canvas id="statusChart" class="w-full h-full"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Activity and Top Products -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
      <div class="space-y-3 max-h-96 overflow-y-auto" id="recentActivity">
        <!-- Activity items will be populated by JavaScript -->
      </div>
    </div>

    <!-- Top Requested Products -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Requested Products</h3>
      <div class="space-y-3 max-h-96 overflow-y-auto" id="topProducts">
        <!-- Product items will be populated by JavaScript -->
      </div>
    </div>
  </div>
</div>
  
    <script type="text/javascript" src="<?php echo url('includes/js/plugins/chart.js'); ?>"></script>

   <!-- Page specific javascripts-->
    
    <script type="text/javascript">
      // Dashboard data will be loaded from PHP
      const dashboardData = {
        stats: {
          totalRequests: <?= getTotalRequests() ?>,
          pendingRequests: <?= getPendingRequests() ?>,
          approvedRequests: <?= getApprovedRequests() ?>,
          scannedItems: <?= getScannedItems() ?>
        },
        requestTrends: {
          labels: <?= json_encode(getLast7Days()) ?>,
          data: <?= json_encode(getRequestTrends()) ?>
        },
        statusDistribution: {
          labels: ['Pending', 'Approved', 'Rejected', 'Completed'],
          data: [
            <?= getRequestsByStatus('pending') ?>,
            <?= getRequestsByStatus('approved') ?>,
            <?= getRequestsByStatus('rejected') ?>,
            <?= getRequestsByStatus('completed') ?>
          ]
        },
        recentActivity: <?= json_encode(getRecentActivity()) ?>,
        topProducts: <?= json_encode(getTopProducts()) ?>
      };

      // Update stats cards
      document.getElementById('totalRequests').textContent = dashboardData.stats.totalRequests;
      document.getElementById('pendingRequests').textContent = dashboardData.stats.pendingRequests;
      document.getElementById('approvedRequests').textContent = dashboardData.stats.approvedRequests;
      document.getElementById('scannedItems').textContent = dashboardData.stats.scannedItems;

      // Request Trends Chart
      const requestTrendsCtx = document.getElementById('requestTrendsChart');
      if (requestTrendsCtx) {
        new Chart(requestTrendsCtx, {
          type: 'line',
          data: {
            labels: dashboardData.requestTrends.labels,
            datasets: [{
              label: 'Daily Requests',
              data: dashboardData.requestTrends.data,
              fill: true,
              borderColor: 'rgb(59, 130, 246)',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              tension: 0.3,
              pointBackgroundColor: 'rgb(59, 130, 246)',
              pointBorderColor: '#fff',
              pointHoverBackgroundColor: '#fff',
              pointHoverBorderColor: 'rgb(59, 130, 246)'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              }
            }
          }
        });
      }

      // Status Distribution Chart
      const statusCtx = document.getElementById('statusChart');
      if (statusCtx) {
        new Chart(statusCtx, {
          type: 'doughnut',
          data: {
            labels: dashboardData.statusDistribution.labels,
            datasets: [{
              data: dashboardData.statusDistribution.data,
              backgroundColor: [
                '#FCD34D', // Yellow for pending
                '#34D399', // Green for approved
                '#F87171', // Red for rejected
                '#60A5FA'  // Blue for completed
              ],
              borderWidth: 2,
              borderColor: '#ffffff'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom'
              }
            }
          }
        });
      }

      // Populate Recent Activity
      function populateRecentActivity() {
        const container = document.getElementById('recentActivity');
        if (!container) return;

        if (dashboardData.recentActivity.length === 0) {
          container.innerHTML = '<p class="text-gray-500 text-sm">No recent activity</p>';
          return;
        }

        container.innerHTML = dashboardData.recentActivity.map(activity => `
          <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="bi bi-${getActivityIcon(activity.action)} text-blue-600 text-sm"></i>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm text-gray-900">${activity.action}</p>
              <p class="text-xs text-gray-500">${formatTime(activity.created_at)}</p>
            </div>
          </div>
        `).join('');
      }

      // Populate Top Products
      function populateTopProducts() {
        const container = document.getElementById('topProducts');
        if (!container) return;

        if (dashboardData.topProducts.length === 0) {
          container.innerHTML = '<p class="text-gray-500 text-sm">No product data available</p>';
          return;
        }

        container.innerHTML = dashboardData.topProducts.map((product, index) => `
          <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
            <div class="flex items-center space-x-3">
              <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                <span class="text-purple-600 text-sm font-semibold">${index + 1}</span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">${product.product_name}</p>
                <p class="text-xs text-gray-500">${product.category || 'Uncategorized'}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-900">${product.request_count}</p>
              <p class="text-xs text-gray-500">requests</p>
            </div>
          </div>
        `).join('');
      }

      // Helper functions
      function getActivityIcon(action) {
        const iconMap = {
          'CREATE': 'plus-circle',
          'APPROVE': 'check-circle',
          'SCAN': 'qr-code',
          'COMPLETE': 'check-square',
          'UPDATE': 'arrow-repeat'
        };
        return iconMap[action] || 'activity';
      }

      function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} min ago`;
        if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        return date.toLocaleDateString();
      }

      // Initialize dashboard
      document.addEventListener('DOMContentLoaded', function() {
        populateRecentActivity();
        populateTopProducts();
      });
    </script>

    <?php
    // Dashboard data methods (these should be moved to a proper controller)
    function getTotalRequests() {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM material_requests");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    function getPendingRequests() {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM material_requests WHERE status = 'pending'");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    function getApprovedRequests() {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM material_requests WHERE status = 'approved'");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    function getScannedItems() {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM qr_tracking WHERE status = 'scanned'");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    function getLast7Days() {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = date('M j', strtotime("-$i days"));
        }
        return $days;
    }

    function getRequestTrends() {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $trends = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM material_requests WHERE DATE(created_at) = ?");
                $stmt->execute([$date]);
                $trends[] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
            }
            return $trends;
        } catch (Exception $e) {
            return array_fill(0, 7, 0);
        }
    }

    function getRequestsByStatus($status) {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM material_requests WHERE status = ?");
            $stmt->execute([$status]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    function getRecentActivity() {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("
                SELECT action, created_at 
                FROM activity_log 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    function getTopProducts() {
        try {
            if (!class_exists('DatabaseManager')) {
                require_once path('includes/DatabaseManager.php');
            }
            $db = DatabaseManager::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    p.product_name,
                    p.category,
                    COUNT(mri.product_id) as request_count
                FROM material_request_items mri
                JOIN products p ON mri.product_id = p.product_id
                GROUP BY p.product_id, p.product_name, p.category
                ORDER BY request_count DESC
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    ?>

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
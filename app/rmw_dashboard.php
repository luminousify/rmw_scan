<body class="min-h-screen bg-gray-50">
    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="<?php echo url('app/controllers/rmw_dashboard.php'); ?>" class="text-xl font-bold text-gray-900">
              RMW System
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
      <aside class="w-64 bg-white shadow-sm min-h-screen border-r border-gray-200">
        <div class="p-6">
          <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 <?= ($department === 'rmw' ? 'bg-green-600' : 'bg-blue-600') ?> rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg"><?= strtoupper($department ?? 'production') ?></span>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">Raw Material Warehouse</p>
            </div>
          </div>
          
          <nav class="space-y-2">
            <a href="<?php echo url('app/controllers/rmw_dashboard.php'); ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $module_name == 'rmw_dashboard' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' ?>">
              <i class="bi bi-house mr-3"></i>
              Dashboard
            </a>
            <a href="<?php echo url('app/controllers/pending_requests.php'); ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $module_name == 'pending_requests' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' ?>">
              <i class="bi bi-clock mr-3"></i>
              Pending Requests
            </a>
            <a href="<?php echo url('app/controllers/processing_requests.php'); ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $module_name == 'processing_requests' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' ?>">
              <i class="bi bi-gear mr-3"></i>
              Processing
            </a>
            <a href="<?php echo url('app/controllers/generate_qr.php'); ?>" 
               class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $module_name == 'generate_qr' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' ?>">
              <i class="bi bi-qr-code mr-3"></i>
              Generate QR
            </a>
          </nav>
        </div>
      </aside>

      <!-- Main content -->
      <main class="flex-1 p-6">
        <!-- Page Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-900">RMW Dashboard</h1>
          <p class="text-gray-600 mt-2">Manage material requests from production</p>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
          <div class="flex">
            <i class="bi bi-check-circle text-green-400 text-xl mr-3"></i>
            <div>
              <h3 class="text-sm font-medium text-green-800">Success!</h3>
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
              <h3 class="text-sm font-medium text-red-800">Error!</h3>
              <p class="text-sm text-red-700 mt-1"><?= $error_message ?></p>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="bi bi-clock text-yellow-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-gray-900"><?= $stats['pending'] ?? 0 ?></p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-blue-100 rounded-lg">
                <i class="bi bi-gear text-blue-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Processing</p>
                <p class="text-2xl font-bold text-gray-900"><?= $stats['diproses'] ?? 0 ?></p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-green-100 rounded-lg">
                <i class="bi bi-check-circle text-green-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Completed</p>
                <p class="text-2xl font-bold text-gray-900"><?= $stats['completed'] ?? 0 ?></p>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-gray-100 rounded-lg">
                <i class="bi bi-x-circle text-gray-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Cancelled</p>
                <p class="text-2xl font-bold text-gray-900"><?= $stats['cancelled'] ?? 0 ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
          <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
              <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                     placeholder="Search by request number, user, or product..." 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            
            <div class="min-w-48">
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="all" <?= ($_GET['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="diproses" <?= ($_GET['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Processing</option>
                <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
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
            <h3 class="text-lg font-medium text-gray-900">Material Requests</h3>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request #</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Production User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($requests)): ?>
                  <?php foreach ($requests as $request): ?>
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      <?= htmlspecialchars($request['request_number']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <?= htmlspecialchars($request['production_user_name'] ?? 'Unknown') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <?= date('M d, Y H:i', strtotime($request['created_at'])) ?>
                    </td>
    
                    <td class="px-6 py-4 text-sm text-gray-500">
                      <?= $request['item_count'] ?> items
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                           ($request['status'] === 'diproses' ? 'bg-blue-100 text-blue-800' : 
                           ($request['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) ?>">
                        <?= ucfirst($request['status']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <button onclick="viewRequest(<?= $request['id'] ?>)" 
                                class="text-blue-600 hover:text-blue-900">
                          <i class="bi bi-eye"></i> View
                        </button>
                        
                        <?php if ($request['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline;">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                          <input type="hidden" name="status" value="diproses">
                          <button type="submit" 
                                  onclick="return confirm('Change status to Processing?')"
                                  class="text-green-600 hover:text-green-900">
                            <i class="bi bi-play-circle"></i> Process
                          </button>
                        </form>
                        <?php elseif ($request['status'] === 'diproses'): ?>
                        <form method="POST" style="display: inline;">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                          <input type="hidden" name="status" value="completed">
                          <button type="submit" 
                                  onclick="return confirm('Mark as Completed?')"
                                  class="text-green-600 hover:text-green-900">
                            <i class="bi bi-check-circle"></i> Complete
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
                      No requests found
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <!-- Request Details Modal -->
    <div id="requestModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Request Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
              <i class="bi bi-x-lg text-xl"></i>
            </button>
          </div>
          <div id="requestDetails" class="text-gray-600">
            <!-- Request details will be loaded here -->
          </div>
        </div>
      </div>
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

      function viewRequest(requestId) {
        // TODO: Implement AJAX request to get request details
        document.getElementById('requestModal').classList.remove('hidden');
        document.getElementById('requestDetails').innerHTML = `
          <p>Loading request details...</p>
          <p>Request ID: ${requestId}</p>
        `;
      }

      function closeModal() {
        document.getElementById('requestModal').classList.add('hidden');
      }

      // Close modal when clicking outside
      document.getElementById('requestModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeModal();
        }
      });
    </script>
  </body>
</html>

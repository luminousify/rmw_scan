<body class="min-h-screen bg-gray-50">


    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md px-2 py-1">
              RMW System
            </a>
          </div>
          
          <!-- Right side -->
          <div class="flex items-center space-x-4">
            <!-- User info -->
            <span class="text-sm font-medium text-gray-700" aria-label="Current user"><?=strtoupper($name)?> (<?=ucfirst($department ?? 'production')?>)</span>
            
            <!-- User Menu-->
            <div class="relative">
              <button 
                class="p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" 
                onclick="toggleDropdown()"
                aria-label="User menu"
                aria-expanded="false"
                aria-haspopup="true">
                <i class="bi bi-person text-xl" aria-hidden="true"></i>
              </button>
              
              <!-- Dropdown Menu -->
              <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200" role="menu">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center transition-colors focus:outline-none focus:bg-gray-100" role="menuitem">
                  <i class="bi bi-gear mr-2" aria-hidden="true"></i>
                  Settings
                </a>
                <a href="<?php echo url('app/logout.php'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center transition-colors focus:outline-none focus:bg-gray-100" role="menuitem">
                  <i class="bi bi-box-arrow-right mr-2" aria-hidden="true"></i>
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
      <aside class="w-64 bg-white shadow-lg min-h-screen border-r border-gray-200 sticky top-16 transition-all duration-300" role="navigation" aria-label="Main navigation">
        <div class="p-6">
          <!-- Enhanced Logo Area -->
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
            <div>
              <p class="text-sm font-medium text-gray-900"><?= ucfirst($department ?? 'production') ?></p>
            </div>
          </div>
          
          <!-- Enhanced Navigation -->
          <nav class="space-y-1" role="navigation" aria-label="Main navigation">
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" 
               class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'dashboard' ? 'nav-active' : 'nav-inactive' ?>"
               aria-current="<?= $module_name == 'dashboard' ? 'page' : 'false' ?>">
              <i class="bi bi-house-fill mr-3 text-lg" aria-hidden="true"></i>
              <span>Dashboard</span>
              <?= $module_name == 'dashboard' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
            </a>
            <a href="<?php echo url('app/controllers/material_request.php'); ?>" 
               class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'material_request' ? 'nav-active' : 'nav-inactive' ?>"
               aria-current="<?= $module_name == 'material_request' ? 'page' : 'false' ?>">
              <i class="bi bi-plus-circle-fill mr-3 text-lg" aria-hidden="true"></i>
              <span>Create Request</span>
              <?= $module_name == 'material_request' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
            </a>
            <a href="<?php echo url('app/controllers/my_requests.php'); ?>" 
               class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'my_requests' ? 'nav-active' : 'nav-inactive' ?>"
               aria-current="<?= $module_name == 'my_requests' ? 'page' : 'false' ?>">
              <i class="bi bi-list-task mr-3 text-lg" aria-hidden="true"></i>
              <span>My Requests</span>
              <?= $module_name == 'my_requests' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
            </a>
            <a href="<?php echo url('app/controllers/scanner.php'); ?>" 
               class="nav-item group relative flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?= $module_name == 'scan' ? 'nav-active' : 'nav-inactive' ?>"
               aria-current="<?= $module_name == 'scan' ? 'page' : 'false' ?>">
              <i class="bi bi-qr-code-scan mr-3 text-lg" aria-hidden="true"></i>
              <span>Scan QR Code</span>
              <?= $module_name == 'scan' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
            </a>
          </nav>
        </div>
      </aside>

      <!-- Main content -->
      <main id="main-content" class="flex-1 p-6" role="main">
        <!-- Page Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Material Requests</h1>
          <p class="text-gray-600 mt-2 text-lg">View and track your material requests</p>
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
          <form method="GET" id="filterForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="lg:col-span-2">
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="searchInput"
                    name="search" 
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                    placeholder="Search by request number or product..." 
                    class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
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
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                  <option value="all" <?= ($_GET['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                  <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="diproses" <?= ($_GET['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Processing</option>
                  <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                  <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
              </div>
              
              <div class="flex items-end space-x-3">
                <button 
                  type="submit" 
                  class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm hover:shadow-md">
                  <i class="bi bi-funnel mr-2" aria-hidden="true"></i>Filter
                </button>
                
                <a 
                  href="<?php echo url('app/controllers/my_requests.php'); ?>" 
                  class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-6 py-2.5 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm hover:shadow-md">
                  <i class="bi bi-arrow-clockwise mr-2" aria-hidden="true"></i>Reset
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
                    Showing <?= count($userRequests) ?> request<?= count($userRequests) > 1 ? 's' : '' ?>
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
          
          <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200" role="table" aria-label="Material requests">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900" scope="col">Request #</th>
                  <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900" scope="col">Date</th>
                  <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900" scope="col">Priority</th>
                  <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900" scope="col">Items</th>
                  <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900" scope="col">Status</th>
                  <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900" scope="col">Notes</th>
                  <th class="px-8 py-4 text-left text-sm font-semibold text-gray-900" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200" id="requestsTableBody">
                <?php if (!empty($userRequests)): ?>
                  <?php foreach ($userRequests as $request): ?>
                  <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-100" data-request-id="<?= $request['id'] ?>">
                    <td class="px-8 py-5 whitespace-nowrap">
                      <div class="text-base font-semibold text-gray-900 font-mono"><?= htmlspecialchars($request['request_number']) ?></div>
                    </td>
                    <td class="px-8 py-5 whitespace-nowrap">
                      <div class="text-base text-gray-600">
                        <time datetime="<?= $request['created_at'] ?>"><?= date('M d, Y H:i', strtotime($request['created_at'])) ?></time>
                      </div>
                    </td>
                    <td class="px-8 py-5 whitespace-nowrap">
                      <span class="px-3 py-1.5 inline-flex text-sm font-medium rounded-lg transition-all duration-200
                        <?= $request['priority'] === 'high' ? 'bg-red-100 text-red-800 ring-1 ring-red-200' : 
                           ($request['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200' : 'bg-green-100 text-green-800 ring-1 ring-green-200') ?>">
                        <?= ucfirst($request['priority']) ?>
                      </span>
                    </td>
                    <td class="px-8 py-5">
                      <div class="text-base text-gray-600">
                        <span class="inline-flex items-center">
                          <i class="bi bi-box-seam mr-2 text-gray-400" aria-hidden="true"></i>
                          <span class="font-medium"><?= $request['item_count'] ?></span>
                          <span class="ml-1">items</span>
                        </span>
                      </div>
                    </td>
                    <td class="px-8 py-5 whitespace-nowrap">
                      <span class="px-3 py-1.5 inline-flex text-sm font-medium rounded-lg transition-all duration-200
                        <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200' : 
                           ($request['status'] === 'diproses' ? 'bg-blue-100 text-blue-800 ring-1 ring-blue-200' : 
                           ($request['status'] === 'completed' ? 'bg-green-100 text-green-800 ring-1 ring-green-200' : 'bg-red-100 text-red-800 ring-1 ring-red-200')) ?>">
                        <?= ucfirst($request['status']) ?>
                      </span>
                    </td>
                    <td class="px-8 py-5">
                      <div class="text-base text-gray-600 max-w-sm">
                        <div class="truncate" title="<?= htmlspecialchars($request['notes'] ?? '') ?>">
                          <?= htmlspecialchars($request['notes'] ?? 'No notes') ?>
                        </div>
                      </div>
                    </td>
                    <td class="px-8 py-5 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <button 
                          onclick="viewRequest(<?= $request['id'] ?>)" 
                          class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                          aria-label="View request <?= htmlspecialchars($request['request_number']) ?>">
                          <i class="bi bi-eye mr-1.5" aria-hidden="true"></i> View
                        </button>
                        
                        <button 
                          onclick="scanQRForRequest('<?= htmlspecialchars($request['request_number']) ?>')" 
                          class="inline-flex items-center px-3 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                          aria-label="Scan QR for request <?= htmlspecialchars($request['request_number']) ?>">
                          <i class="bi bi-upc-scan mr-1.5" aria-hidden="true"></i> Scan QR
                        </button>
                        
                        <?php if ($request['status'] === 'pending'): ?>
                        <button 
                          onclick="cancelRequest(<?= $request['id'] ?>)" 
                          class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                          aria-label="Cancel request <?= htmlspecialchars($request['request_number']) ?>">
                          <i class="bi bi-x-circle mr-1.5" aria-hidden="true"></i> Cancel
                        </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="px-8 py-16 text-center text-gray-500">
                      <div class="space-y-6">
                        <i class="bi bi-inbox text-8xl text-gray-300 block mx-auto" aria-hidden="true"></i>
                        <div>
                          <h4 class="text-xl font-semibold text-gray-900 mb-3">No requests found</h4>
                          <p class="text-gray-600 mb-6 text-lg">Get started by creating your first material request</p>
                          <a 
                            href="<?php echo url('app/controllers/material_request.php'); ?>" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                            <i class="bi bi-plus-circle mr-2" aria-hidden="true"></i>
                            <span>Create Request</span>
              <?= $module_name == 'material_request' ? '<div class="absolute right-2 w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>' : '' ?>
                          </a>
                        </div>
                      </div>
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

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 animate-fade-in" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
      <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full animate-slide-up">
          <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
              <i class="bi bi-exclamation-triangle text-red-600 text-xl" aria-hidden="true"></i>
            </div>
            <h3 id="confirmTitle" class="text-lg font-medium text-gray-900 text-center mb-2">Confirm Action</h3>
            <p id="confirmMessage" class="text-sm text-gray-500 text-center mb-6">Are you sure you want to proceed?</p>
            <div class="flex space-x-3">
              <button 
                id="confirmCancel"
                onclick="closeConfirmModal()" 
                class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Cancel
              </button>
              <button 
                id="confirmAction"
                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Confirm
              </button>
            </div>
          </div>
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
          document.querySelector('button[onclick*="toggleDropdown"]').setAttribute('aria-expanded', 'false');
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
        const modal = document.getElementById('requestModal');
        const loading = document.getElementById('modalLoading');
        const error = document.getElementById('modalError');
        const details = document.getElementById('requestDetails');
        const footer = document.getElementById('modalFooter');
        
        // Reset modal state
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        details.classList.add('hidden');
        footer.classList.add('hidden');
        
        // Fetch request details via AJAX
        fetch(`get_request_details.php?id=${requestId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            loading.classList.add('hidden');
            
            if (data.success) {
              displayRequestDetails(data.request);
              details.classList.remove('hidden');
              footer.classList.remove('hidden');
            } else {
              document.getElementById('modalErrorMessage').textContent = data.error || 'Failed to load request details';
              error.classList.remove('hidden');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            loading.classList.add('hidden');
            document.getElementById('modalErrorMessage').textContent = 'Network error. Please try again.';
            error.classList.remove('hidden');
          });
      }
      
      function displayRequestDetails(request) {
        const itemsHtml = request.items.map(item => `
          <tr class="border-b border-gray-200 hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-mono text-gray-900">${item.product_id}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${item.product_name}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${item.requested_quantity}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${item.unit}</td>
            <td class="px-4 py-3 text-sm text-gray-900">${item.description || '-'}</td>
            <td class="px-4 py-3 text-sm">
              <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                item.status === 'pending' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200' :
                item.status === 'approved' ? 'bg-green-100 text-green-800 ring-1 ring-green-200' :
                'bg-red-100 text-red-800 ring-1 ring-red-200'
              }">
                ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
              </span>
            </td>
          </tr>
        `).join('');
        
        const statusColor = request.status === 'pending' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200' :
                           request.status === 'diproses' ? 'bg-blue-100 text-blue-800 ring-1 ring-blue-200' :
                           request.status === 'completed' ? 'bg-green-100 text-green-800 ring-1 ring-green-200' :
                           'bg-red-100 text-red-800 ring-1 ring-red-200';
        
        const priorityColor = request.priority === 'high' ? 'bg-red-100 text-red-800 ring-1 ring-red-200' :
                             request.priority === 'medium' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200' :
                             'bg-green-100 text-green-800 ring-1 ring-green-200';
        
        document.getElementById('requestDetails').innerHTML = `
          <div class="space-y-6">
            <!-- Request Header -->
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-2">Request Number</label>
                  <p class="text-lg font-semibold text-gray-900 font-mono">${request.request_number}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-2">Status</label>
                  <p class="text-lg">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full ${statusColor}">
                      ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                    </span>
                  </p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-2">Priority</label>
                  <p class="text-lg">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full ${priorityColor}">
                      ${request.priority.charAt(0).toUpperCase() + request.priority.slice(1)}
                    </span>
                  </p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-2">Created Date</label>
                  <p class="text-lg text-gray-900">
                    <time datetime="${request.created_at}">${new Date(request.created_at).toLocaleDateString('en-US', { 
                      year: 'numeric', 
                      month: 'long', 
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    })}</time>
                  </p>
                </div>
              </div>
              ${request.notes ? `
                <div class="mt-6">
                  <label class="text-sm font-medium text-gray-500 block mb-2">Notes</label>
                  <p class="text-gray-900 bg-white p-3 rounded border border-gray-200">${request.notes}</p>
                </div>
              ` : ''}
            </div>
            
            <!-- Request Items -->
            <div>
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="bi bi-box-seam mr-2 text-gray-400" aria-hidden="true"></i>
                Requested Items (${request.items.length})
              </h4>
              <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Product ID</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Product Name</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Quantity</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Unit</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Description</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Status</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    ${itemsHtml}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        `;
      }

      function cancelRequest(requestId) {
        const modal = document.getElementById('confirmModal');
        const confirmBtn = document.getElementById('confirmAction');
        
        modal.classList.remove('hidden');
        document.getElementById('confirmMessage').textContent = 
          'Are you sure you want to cancel this request? This action cannot be undone.';
        
        confirmBtn.onclick = function() {
          // Show loading state
          confirmBtn.disabled = true;
          confirmBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block mr-2"></div>Cancelling...';
          
          // Simulate API call
          setTimeout(() => {
            closeConfirmModal();
            showSuccess('Request cancelled successfully');
            
            // Remove the row from table
            const row = document.querySelector(`tr[data-request-id="${requestId}"]`);
            if (row) {
              row.style.transition = 'opacity 0.3s, transform 0.3s';
              row.style.opacity = '0';
              row.style.transform = 'translateX(-20px)';
              setTimeout(() => row.remove(), 300);
            }
            
            // Reset button
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Confirm';
          }, 1500);
        };
      }

      function closeModal() {
        const modal = document.getElementById('requestModal');
        modal.classList.add('hidden');
        // Reset modal content
        document.getElementById('requestDetails').innerHTML = '';
      }

      function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
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

      // Keyboard navigation
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeModal();
          closeConfirmModal();
        }
      });

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
  </body>
</html>

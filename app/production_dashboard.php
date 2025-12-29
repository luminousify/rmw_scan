<body class="min-h-screen bg-gray-50">
    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="<?php echo url('app/controllers/production_dashboard.php'); ?>" class="text-xl font-bold text-gray-900">
              <?= ($department === 'production' ? 'Production System' : 'Scan No Bon') ?>
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
        <div class="space-y-6">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="bi bi-clock text-yellow-600 text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Menunggu</p>
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
                <p class="text-sm font-medium text-gray-600">Diproses</p>
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
                <p class="text-sm font-medium text-gray-600">Selesai</p>
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
                <p class="text-sm font-medium text-gray-600">Dibatalkan</p>
                <p class="text-2xl font-bold text-gray-900"><?= $stats['cancelled'] ?? 0 ?></p>
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
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="min-w-48">
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all" <?= ($_GET['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                <option value="diproses" <?= ($_GET['status'] ?? '') === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
              </select>
            </div>
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
              <i class="bi bi-search mr-2"></i>Filter
            </button>
            
            <a href="<?php echo url('app/controllers/production_dashboard.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium">
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
            <table class="min-w-full divide-y divide-gray-200">
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
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      <?= htmlspecialchars($request['request_number']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div>
                        <div class="font-medium text-gray-900"><?= htmlspecialchars($request['production_user_name'] ?? 'Unknown') ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($request['production_division'] ?? 'Unassigned') ?></div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <?= date('M d, Y H:i', strtotime($request['created_at'])) ?>
                    </td>
    
                    <td class="px-6 py-4 text-sm text-gray-500">
                      <?= $request['item_count'] ?> item
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                          <input type="hidden" name="status" value="diproses">
                          <button type="submit" 
                                  onclick="return confirm('Ubah status ke Diproses?')"
                                  class="text-blue-600 hover:text-blue-900">
                            <i class="bi bi-play-circle"></i> Proses
                          </button>
                        </form>
                        <?php elseif ($request['status'] === 'diproses'): ?>
                        <form method="POST" style="display: inline;">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                          <input type="hidden" name="status" value="completed">
                          <button type="submit" 
                                  onclick="return confirm('Tandai sebagai Selesai?')"
                                  class="text-blue-600 hover:text-blue-900">
                            <i class="bi bi-check-circle"></i> Selesaikan
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
        </div>
        </div>
      </main>
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
                        border-top: 4px solid #3b82f6 !important;
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
        const url = `app/controllers/production_dashboard.php?action=get_request_details&id=${requestId}`;
        console.log('Fetching from URL:', url);
        
        fetch(url, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          cache: 'no-cache'
        })
          .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
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
            console.log('Response text length:', text.length);
            
            // Validate that response starts with JSON object/array
            const trimmed = text.trim();
            if (!trimmed.startsWith('{') && !trimmed.startsWith('[')) {
              console.error('Invalid JSON response - starts with:', trimmed.substring(0, 50));
              throw new Error('Invalid JSON response format');
            }
            
            try {
              return JSON.parse(text);
            } catch (parseError) {
              console.error('JSON parse error:', parseError);
              console.error('Response content preview:', text.substring(0, 200));
              throw new Error(`JSON parse error: ${parseError.message}`);
            }
          })
          .then(data => {
            console.log('Parsed data:', data);
            document.getElementById('modalLoading').style.display = 'none';
            
            // Validate response structure
            if (!data || typeof data !== 'object') {
              throw new Error('Invalid response structure');
            }
            
            if (data.success) {
              console.log('Data success, calling displayRequestDetails');
              if (!data.request) {
                throw new Error('Missing request data in response');
              }
              displayRequestDetails(data.request);
            } else {
              console.log('Data error:', data.error);
              const errorMessage = data.error || 'Failed to load request details';
              const errorType = data.error_type || 'Unknown';
              console.error('Server error details:', { error: errorMessage, type: errorType });
              
              document.getElementById('modalErrorMessage').textContent = `${errorMessage} (${errorType})`;
              document.getElementById('modalError').style.display = 'block';
            }
          })
          .catch(error => {
            console.error('Request failed:', error);
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
        console.log('displayRequestDetails called with:', request);
        
        const itemsHtml = request.items.map(item => `
          <tr style="border-bottom: 1px solid #e5e7eb !important;">
            <td style="padding: 12px 16px !important; font-size: 14px !important; font-family: monospace !important; color: #111827 !important;">${item.product_id}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.product_name}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.requested_quantity}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.unit}</td>
            <td style="padding: 12px 16px !important; font-size: 14px !important; color: #111827 !important;">${item.description || '-'}</td>
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
                <h4 style="font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; margin-bottom: 4px !important; letter-spacing: 0.5px !important;">Priority</h4>
                <span style="
                  padding: 6px 12px !important;
                  display: inline-flex !important;
                  font-size: 14px !important;
                  font-weight: 600 !important;
                  border-radius: 6px !important;
                  ${request.priority === 'high' ? 'background: #fee2e2 !important; color: #991b1b !important;' :
                    request.priority === 'medium' ? 'background: #fef3c7 !important; color: #92400e !important;' :
                    'background: #f3f4f6 !important; color: #374151 !important;'}
                ">
                  ${request.priority ? request.priority.charAt(0).toUpperCase() + request.priority.slice(1) : 'Normal'}
                </span>
              </div>
              <div>
                <h4 style="font-size: 12px !important; font-weight: 600 !important; color: #6b7280 !important; text-transform: uppercase !important; margin-bottom: 4px !important; letter-spacing: 0.5px !important;">Referensi Pelanggan</h4>
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
      
      .nav-active-production {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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

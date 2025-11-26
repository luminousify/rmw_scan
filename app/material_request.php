<body class="min-h-screen bg-gray-50">
    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="<?php echo url('app/controllers/dashboard.php'); ?>" class="text-xl font-bold text-gray-900">
              RMW System
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
      <?php include '../common/sidebar.php'; ?>

      <!-- Main content -->
      <main class="flex-1 p-6">
        <!-- Page Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-900">Buat Permintaan Material</h1>
          <p class="text-gray-600 mt-2">Minta bahan baku untuk produksi</p>
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

        <!-- Material Request Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <form method="POST" action="" id="materialRequestForm">
            <input type="hidden" name="action" value="create_request">
            
            <!-- Request Details -->
            <div class="mb-6">
              <p class="text-sm text-gray-600">
                <i class="bi bi-info-circle mr-1"></i>
                Tanggal permintaan akan dicatat secara otomatis saat Anda mengirim formulir ini.
              </p>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
              <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan tambahan atau instruksi khusus..."></textarea>
            </div>

            <!-- Items Section -->
            <div class="mb-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Item Permintaan</h3>
                <button type="button" onclick="addItem()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                  <i class="bi bi-plus mr-2"></i>Tambah Item
                </button>
              </div>

              <div id="itemsContainer">
                <!-- Items will be added here dynamically -->
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
              <button type="button" onclick="resetForm()" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Reset
              </button>
              <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium">
                <i class="bi bi-check-circle mr-2"></i>Buat Permintaan
              </button>
            </div>
          </form>
        </div>

        <!-- Recent Requests -->
        <?php if (!empty($userRequests)): ?>
        <div class="mt-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Permintaan Terbaru</h3>
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Permintaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php foreach ($userRequests as $request): ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      <?= htmlspecialchars($request['request_number']) ?>
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
                           ($request['status'] === 'diproses' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                        <?= ucfirst($request['status']) ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <!-- Mobile pagination -->
                <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Sebelumnya
                </a>
                <?php endif; ?>
                <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Selanjutnya
                </a>
                <?php endif; ?>
              </div>
              
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Menampilkan 
                    <span class="font-medium">
                      <?= ($currentPage - 1) * $limit + 1 ?>
                    </span>
                    hingga 
                    <span class="font-medium">
                      <?= min($currentPage * $limit, $totalRequests) ?>
                    </span>
                    dari 
                    <span class="font-medium"><?= $totalRequests ?></span>
                    hasil
                  </p>
                </div>
                
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <!-- Previous button -->
                    <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                      <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php else: ?>
                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                      <i class="bi bi-chevron-left"></i>
                    </span>
                    <?php endif; ?>
                    
                    <!-- Page numbers -->
                    <?php 
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    if ($startPage > 1) {
                        echo '<a href="?page=1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>';
                        if ($startPage > 2) {
                            echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                        }
                    }
                    
                    for ($page = $startPage; $page <= $endPage; $page++) {
                        if ($page == $currentPage) {
                            echo '<span aria-current="page" class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">' . $page . '</span>';
                        } else {
                            echo '<a href="?page=' . $page . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $page . '</a>';
                        }
                    }
                    
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                        }
                        echo '<a href="?page=' . $totalPages . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $totalPages . '</a>';
                    }
                    ?>
                    
                    <!-- Next button -->
                    <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                      <i class="bi bi-chevron-right"></i>
                    </a>
                    <?php else: ?>
                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                      <i class="bi bi-chevron-right"></i>
                    </span>
                    <?php endif; ?>
                  </nav>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </main>
    </div>

    <!-- JavaScript -->
    <script>
      // Products data for dropdown
      const products = <?= json_encode($products) ?>;
      let itemCounter = 0;

      function addItem() {
        itemCounter++;
        const container = document.getElementById('itemsContainer');
        const itemHtml = `
          <div class="item-row border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50" data-item="${itemCounter}">
            <div class="flex justify-between items-center mb-3">
              <h4 class="text-sm font-medium text-gray-900">Item ${itemCounter}</h4>
              <button type="button" onclick="removeItem(${itemCounter})" class="text-red-600 hover:text-red-800">
                <i class="bi bi-trash"></i>
              </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                <select name="items[${itemCounter}][product_id]" onchange="updateProductInfo(${itemCounter})" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                  <option value="">Pilih Produk</option>
                  ${products.map(p => `<option value="${p.product_id}" data-name="${p.product_name}" data-unit="${p.unit}">${p.product_id} - ${p.product_name}</option>`).join('')}
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas</label>
                <input type="number" name="items[${itemCounter}][quantity]" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                <input type="text" name="items[${itemCounter}][unit]" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <input type="text" name="items[${itemCounter}][description]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Opsional">
              </div>
            </div>
            <input type="hidden" name="items[${itemCounter}][product_name]">
          </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
      }

      function removeItem(itemId) {
        const itemRow = document.querySelector(`[data-item="${itemId}"]`);
        if (itemRow) {
          itemRow.remove();
        }
      }

      function updateProductInfo(itemId) {
        const select = document.querySelector(`select[name="items[${itemId}][product_id]"]`);
        const option = select.options[select.selectedIndex];
        const unitInput = document.querySelector(`input[name="items[${itemId}][unit]"]`);
        const nameInput = document.querySelector(`input[name="items[${itemId}][product_name]"]`);
        
        if (option.value) {
          unitInput.value = option.dataset.unit;
          nameInput.value = option.dataset.name;
        } else {
          unitInput.value = '';
          nameInput.value = '';
        }
      }

      function resetForm() {
        document.getElementById('materialRequestForm').reset();
        document.getElementById('itemsContainer').innerHTML = '';
        itemCounter = 0;
      }

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

      // Add first item by default
      document.addEventListener('DOMContentLoaded', function() {
        addItem();
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

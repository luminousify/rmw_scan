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
                <a href="<?php echo url('app/controllers/settings.php'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
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
      const productSearchUrl = <?= json_encode(url('app/controllers/products_autocomplete.php')) ?>;
      let itemCounter = 0;

      function addItem() {
        // Validate existing items before adding a new one
        const existingItems = document.querySelectorAll('.item-row');
        for (let i = 0; i < existingItems.length; i++) {
          const itemRow = existingItems[i];
          const itemId = itemRow.getAttribute('data-item');
          
          if (!isItemValid(itemId)) {
            // Focus on the first invalid item and show error message
            const displayInput = itemRow.querySelector(`.product-autocomplete[data-item="${itemId}"]`);
            if (displayInput) {
              displayInput.focus();
              displayInput.setCustomValidity('Lengkapi item ini sebelum menambah item baru.');
              displayInput.reportValidity();
              
              // Clear the custom validity after a delay
              setTimeout(() => {
                displayInput.setCustomValidity('');
              }, 3000);
            }
            return; // Don't add new item if existing one is invalid
          }
        }
        
        // All existing items are valid, proceed to add new item
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
                <div class="relative">
                  <input
                    type="text"
                    class="product-autocomplete w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari produk (ID / Nama)..."
                    autocomplete="off"
                    data-item="${itemCounter}"
                    required
                  >
                  <div
                    class="product-suggestions hidden absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-20 max-h-60 overflow-auto"
                    data-item="${itemCounter}"
                  ></div>
                </div>
                <input type="hidden" name="items[${itemCounter}][product_id]" data-item="${itemCounter}">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas</label>
                <input type="number" name="items[${itemCounter}][quantity]" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
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

        // Wire autocomplete events for the new row
        const input = container.querySelector(`.product-autocomplete[data-item="${itemCounter}"]`);
        if (input) {
          setupProductAutocomplete(input);
        }
      }

      function isItemValid(itemId) {
        const displayInput = document.querySelector(`.product-autocomplete[data-item="${itemId}"]`);
        const idInput = document.querySelector(`input[name="items[${itemId}][product_id]"]`);
        const quantityInput = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
        
        // Check if product is selected (has product_id)
        const hasProduct = !!(idInput && idInput.value && String(idInput.value).trim() !== '');
        
        // Check if quantity is filled and valid
        const hasQuantity = !!(quantityInput && quantityInput.value && parseFloat(quantityInput.value) > 0);
        
        return hasProduct && hasQuantity;
      }

      function removeItem(itemId) {
        const itemRow = document.querySelector(`[data-item="${itemId}"]`);
        if (itemRow) {
          itemRow.remove();
          updateItemLabels();
        }
      }

      function updateItemLabels() {
        const itemRows = document.querySelectorAll('.item-row');
        itemRows.forEach((row, index) => {
          const labelElement = row.querySelector('h4');
          if (labelElement) {
            labelElement.textContent = `Item ${index + 1}`;
          }
        });
      }

      function hasDuplicateProduct(productId, excludeItemId) {
        const itemRows = document.querySelectorAll('.item-row');
        for (let i = 0; i < itemRows.length; i++) {
          const row = itemRows[i];
          const itemId = row.getAttribute('data-item');
          if (itemId === excludeItemId.toString()) continue; // Skip current item
          
          const idInput = row.querySelector(`input[name="items[${itemId}][product_id]"]`);
          if (idInput && idInput.value === productId) {
            return true; // Found duplicate
          }
        }
        return false; // No duplicate found
      }

      function setItemProduct(itemId, product) {
        const unitInput = document.querySelector(`input[name="items[${itemId}][unit]"]`);
        const nameInput = document.querySelector(`input[name="items[${itemId}][product_name]"]`);
        const idInput = document.querySelector(`input[name="items[${itemId}][product_id]"]`);
        const displayInput = document.querySelector(`.product-autocomplete[data-item="${itemId}"]`);

        const productId = product?.product_id || '';
        const productName = product?.product_name || '';
        const unit = product?.unit || '';
        
        // Check for duplicate product before setting
        if (productId && hasDuplicateProduct(productId, itemId)) {
          const confirmed = confirm(`Produk "${productName}" sudah ada dalam permintaan ini. Apakah ingin menggabungkan kuantitas?`);
          if (!confirmed) {
            // User cancelled, clear the selection
            if (displayInput) {
              displayInput.value = '';
              displayInput.focus();
            }
            clearItemProductFields(itemId);
            return;
          }
        }

        if (idInput) idInput.value = productId;
        if (nameInput) nameInput.value = productName;
        if (unitInput) unitInput.value = unit;
        // Only update the visible input on an actual selection. While typing, we should not wipe user input.
        if (displayInput && productId) {
          displayInput.value = productName ? `${productId} - ${productName}` : `${productId}`;
          // Clear any previous custom validity errors now that a product is selected.
          displayInput.setCustomValidity('');
        }
      }

      function clearItemProductFields(itemId) {
        const unitInput = document.querySelector(`input[name="items[${itemId}][unit]"]`);
        const nameInput = document.querySelector(`input[name="items[${itemId}][product_name]"]`);
        const idInput = document.querySelector(`input[name="items[${itemId}][product_id]"]`);
        if (idInput) idInput.value = '';
        if (nameInput) nameInput.value = '';
        if (unitInput) unitInput.value = '';
      }

      // --- Autocomplete implementation (server-side) ---
      const _acTimers = new Map(); // itemId -> timeoutId
      const _acAborters = new Map(); // itemId -> AbortController

      function setupProductAutocomplete(inputEl) {
        const itemId = inputEl.dataset.item;
        if (!itemId) return;

        inputEl.addEventListener('input', function () {
          // Invalidate current selection on any edit
          clearItemProductFields(itemId);
          // Clear any previous submit-time validation error while user is typing.
          inputEl.setCustomValidity('');

          const q = (inputEl.value || '').trim();
          scheduleSuggestions(itemId, q);
        });

        inputEl.addEventListener('focus', function () {
          const q = (inputEl.value || '').trim();
          if (q.length >= 2) {
            scheduleSuggestions(itemId, q);
          }
        });

        inputEl.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            hideSuggestions(itemId);
          }
        });
      }

      function scheduleSuggestions(itemId, q) {
        const timer = _acTimers.get(itemId);
        if (timer) window.clearTimeout(timer);

        // Don’t query for tiny inputs
        if (!q || q.length < 2) {
          renderSuggestions(itemId, [], { emptyState: q ? 'Ketik minimal 2 karakter' : '' });
          hideSuggestions(itemId);
          return;
        }

        _acTimers.set(itemId, window.setTimeout(() => {
          fetchSuggestions(itemId, q);
        }, 200));
      }

      async function fetchSuggestions(itemId, q) {
        // Abort any in-flight request for this item
        const prevAborter = _acAborters.get(itemId);
        if (prevAborter) {
          prevAborter.abort();
        }

        const aborter = new AbortController();
        _acAborters.set(itemId, aborter);

        renderSuggestions(itemId, [], { loading: true });
        showSuggestions(itemId);

        try {
          const url = new URL(productSearchUrl, window.location.origin);
          url.searchParams.set('q', q);
          url.searchParams.set('limit', '15');

          const res = await fetch(url.toString(), {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            signal: aborter.signal,
            credentials: 'same-origin',
          });

          if (!res.ok) {
            renderSuggestions(itemId, [], { emptyState: 'Gagal memuat produk' });
            return;
          }

          const data = await res.json();
          if (!Array.isArray(data) || data.length === 0) {
            renderSuggestions(itemId, [], { emptyState: 'Tidak ada hasil' });
            return;
          }

          renderSuggestions(itemId, data);
        } catch (err) {
          // Ignore abort errors
          if (err && err.name === 'AbortError') return;
          renderSuggestions(itemId, [], { emptyState: 'Gagal memuat produk' });
        }
      }

      function renderSuggestions(itemId, items, opts = {}) {
        const box = document.querySelector(`.product-suggestions[data-item="${itemId}"]`);
        if (!box) return;

        if (opts.loading) {
          box.innerHTML = `
            <div class="px-3 py-2 text-sm text-gray-500">Memuat...</div>
          `;
          return;
        }

        if (!items || items.length === 0) {
          const msg = opts.emptyState || 'Tidak ada hasil';
          box.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500">${escapeHtml(msg)}</div>`;
          return;
        }

        box.innerHTML = items.map((p, idx) => {
          const pid = p.product_id || '';
          const pname = p.product_name || '';
          const unit = p.unit || '';
          const label = pid && pname ? `${pid} - ${pname}` : (pid || pname);
          return `
            <button
              type="button"
              class="w-full text-left px-3 py-2 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none border-b border-gray-100 last:border-b-0"
              data-idx="${idx}"
            >
              <div class="text-sm text-gray-900 font-medium">${escapeHtml(label)}</div>
              <div class="text-xs text-gray-500">Satuan: ${escapeHtml(unit || '-')}</div>
            </button>
          `;
        }).join('');

        // Click handling
        Array.from(box.querySelectorAll('button[type="button"]')).forEach(btn => {
          btn.addEventListener('click', () => {
            const idx = Number(btn.getAttribute('data-idx') || '0');
            const product = items[idx];
            setItemProduct(itemId, product);
            hideSuggestions(itemId);
          });
        });
      }

      function showSuggestions(itemId) {
        const box = document.querySelector(`.product-suggestions[data-item="${itemId}"]`);
        if (!box) return;
        box.classList.remove('hidden');
      }

      function hideSuggestions(itemId) {
        const box = document.querySelector(`.product-suggestions[data-item="${itemId}"]`);
        if (!box) return;
        box.classList.add('hidden');
      }

      function escapeHtml(str) {
        return String(str ?? '')
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#39;');
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

        // Close product suggestions when clicking outside any autocomplete widget
        const isInsideAutocomplete = !!event.target.closest('.product-autocomplete') || !!event.target.closest('.product-suggestions');
        if (!isInsideAutocomplete) {
          document.querySelectorAll('.product-suggestions').forEach(el => el.classList.add('hidden'));
        }
      });

      // Form-level validation: ensure product_id is selected for each item row.
      document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('materialRequestForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
          let firstInvalid = null;
          document.querySelectorAll('.item-row').forEach(row => {
            const itemId = row.getAttribute('data-item');
            if (!itemId) return;

            const displayInput = row.querySelector(`.product-autocomplete[data-item="${itemId}"]`);
            const idInput = row.querySelector(`input[name="items[${itemId}][product_id]"]`);

            // Clear previous validation messages
            if (displayInput) {
              displayInput.setCustomValidity('');
            }

            const hasId = !!(idInput && idInput.value && String(idInput.value).trim() !== '');
            if (!hasId) {
              if (displayInput) {
                displayInput.setCustomValidity('Pilih produk dari daftar saran.');
                if (!firstInvalid) firstInvalid = displayInput;
              }
            }
          });

          if (firstInvalid) {
            e.preventDefault();
            firstInvalid.reportValidity();
            firstInvalid.focus();
            return false;
          }
        });
      });

      // Add first item by default
      document.addEventListener('DOMContentLoaded', function() {
        addItem();
      });
    </script>
  </body>
</html>

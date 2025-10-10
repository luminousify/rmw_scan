<body class="min-h-screen bg-gray-50">
    <!-- Navbar-->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <a href="<?php echo url('app/controllers/scanner.php'); ?>" class="text-xl font-bold text-gray-900">
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

    







    <!-- Scanner Content -->
    <div class="space-y-6">

      <!-- Scanner Card -->
      <div class="bg-white rounded-2xl shadow-xl border-4 border-blue-500 p-6">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">SCAN NO BON</h2>
            <?php if ($dat!=""): ?>
            <div class="text-right">
              <span class="text-lg font-semibold text-gray-700">LPB-SJ: <?=$nobon?></span>
            </div>
            <?php endif; ?>
          </div>
          
          <!-- Request Number Alert (shown when request number is passed from URL) -->
          <?php if (!empty($_GET['request_number']) && $nobon != ''): ?>
          <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 mb-4 animate-fade-in">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-check-circle-fill text-green-600 text-xl"></i>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Scanning for Request</h3>
                <p class="text-lg font-bold text-green-900 mt-1">
                  <i class="bi bi-upc-scan mr-2"></i><?= htmlspecialchars($nobon) ?>
                </p>
                <p class="text-sm text-green-700 mt-1">All scanned items will be associated with this request</p>
              </div>
              <div class="ml-auto">
                <a href="<?php echo url('app/controllers/scanner.php'); ?>" 
                   class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-800 rounded-md text-sm font-medium transition-colors">
                  <i class="bi bi-arrow-clockwise mr-1"></i>
                  Scan Different Request
                </a>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Scanner Input -->
        <div class="mb-6">
          <form name="form1" action="#" method="post">
            <div class="flex">
              <div class="scan-icon">
                <i class="bi bi-qr-code-scan text-lg font-bold text-gray-700"></i>
              </div>
              <input 
                id="lot_material_bc" 
                type="text" 
                class="scan-input" 
                name="nobon" 
                placeholder="[--FOCUS-QR-CODE--]" 
                value="<?= htmlspecialchars($nobon) ?>"
                required 
                <?php echo empty($nobon) ? 'autofocus' : ''; ?>
              >
            </div>
            <?php if (!empty($nobon)): ?>
            <div class="mt-3 text-sm text-gray-600">
              <i class="bi bi-info-circle mr-1"></i>
              Request number is pre-filled. You can scan additional QR codes or change the request number above.
            </div>
            <?php endif; ?>
          </form>
        </div>
        <!-- Data Table -->
        <?php if (!empty($dat)): ?>
        <div class="table-container">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-600 text-white">
                  <th class="border border-gray-300 px-4 py-3 text-center sticky top-0">No.</th>
                  <th class="border border-gray-300 px-4 py-3 text-left sticky top-0">Product ID</th>
                  <th class="border border-gray-300 px-4 py-3 text-left sticky top-0">Product Name</th>
                  <th class="border border-gray-300 px-4 py-3 text-left sticky top-0">Description</th>
                  <th class="border border-gray-300 px-4 py-3 text-left sticky top-0">Quantity</th>
                  <th class="border border-gray-300 px-4 py-3 text-left sticky top-0">Unit</th>
                  <th class="border border-gray-300 px-4 py-3 text-center sticky top-0">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach($dat as $d): ?>
                <tr class="bg-white hover:bg-gray-50">
                  <td class="border border-gray-300 px-4 py-3 text-center"><?=$no++?></td>
                  <td class="border border-gray-300 px-4 py-3"><?=htmlspecialchars($d['product_id'])?></td>
                  <td class="border border-gray-300 px-4 py-3"><?=htmlspecialchars($d['product_name'])?></td>
                  <td class="border border-gray-300 px-4 py-3"><?=htmlspecialchars($d['description'] ?? '')?></td>
                  <td class="border border-gray-300 px-4 py-3"><?=number_format($d['requested_quantity'], 0, '', ',')?></td>
                  <td class="border border-gray-300 px-4 py-3"><?=htmlspecialchars($d['unit'])?></td>
                  <td class="border border-gray-300 px-4 py-3 text-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                      <?= $d['item_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                         ($d['item_status'] === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                      <?= ucfirst($d['item_status']) ?>
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Request Information -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <h4 class="text-sm font-medium text-blue-800 mb-2">Request Information</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <span class="font-medium text-blue-700">Request Number:</span>
              <span class="text-blue-600"><?= htmlspecialchars($dat[0]['request_number']) ?></span>
            </div>
            <div>
              <span class="font-medium text-blue-700">Status:</span>
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                <?= $dat[0]['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                   ($dat[0]['status'] === 'diproses' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                <?= ucfirst($dat[0]['status']) ?>
              </span>
            </div>
            <div>
              <span class="font-medium text-blue-700">Requested by:</span>
              <span class="text-blue-600"><?= htmlspecialchars($dat[0]['production_user']) ?></span>
            </div>
          </div>
        </div>
        
        <?php elseif (isset($error_message)): ?>
        <div class="text-center py-12">
          <div class="text-red-400 mb-4">
            <i class="bi bi-exclamation-triangle text-6xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Error</h3>
          <p class="text-red-600"><?= htmlspecialchars($error_message) ?></p>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
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

<script src="<?php echo url('includes/js/main.js'); ?>"></script>
    <style>
      @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      .animate-fade-in {
        animation: fade-in 0.3s ease-out;
      }
    </style>
    
    <script>
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


        // Ambil semua checkbox dan tombol inputan
        const checkboxes = document.querySelectorAll('.checkbox');
        const inputButton = document.getElementById('inputButton');

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
    document.querySelector('form[action="../controllers/proses.php"]').addEventListener('submit', disableButton);
// Additional protection: Disable the button on click as well
inputButton.addEventListener('click', function() {
        // Disable immediately on click
        inputButton.disabled = true;
        inputButton.innerText = 'Processing...';

        document.querySelector('form[action="../controllers/proses.php"]').submit();
        
        // If for some reason the form isn't submitted within 5 seconds, re-enable the button
        // This is a safety measure in case something goes wrong with the form submission
        setTimeout(function() {
            if (inputButton.disabled) {
                inputButton.disabled = false;
                inputButton.innerText = 'Input Data';
            }
        }, 5000);
    });

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




  


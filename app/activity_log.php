<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url());
    exit();
}

$module_name = "activity_log";
$title = "Activity Log";
$name = $_SESSION['user'];
$department = $_SESSION['department'] ?? 'production';

include '../common/header.php';

try {
    include '../includes/conn_mysql.php';
    
    // Get recent activity log entries
    $query = "
        SELECT 
            al.*,
            u.full_name as user_name
        FROM activity_log al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT 50
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
    $activities = [];
}

include '../common/footer.php';
?>
<body class="min-h-screen bg-gray-50">
    <div class="flex">
      <?php include 'common/sidebar.php'; ?>

      <!-- Main content -->
      <main id="main-content" class="flex-1 p-6" role="main">
        <!-- Page Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Activity Log</h1>
          <p class="text-gray-600 mt-2">View and track all system activities</p>
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

        <!-- Activity Log Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">
                  <?php if (!empty($activities)): ?>
                    Showing <?= count($activities) ?> recent activities
                  <?php endif; ?>
                </span>
                <a href="<?php echo url('app/controllers/activity_log.php'); ?>" 
                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                  <i class="bi bi-arrow-clockwise mr-1"></i>
                  Refresh
                </a>
              </div>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200" role="table" aria-label="Activity log entries">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($activities)): ?>
                  <?php foreach ($activities as $activity): ?>
                  <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div class="flex flex-col">
                        <span class="font-medium text-gray-900">
                          <i class="bi bi-clock text-gray-400 mr-1"></i>
                          <time datetime="<?= $activity['created_at'] ?>"><?= date('M d, Y H:i:s', strtotime($activity['created_at'])) ?></time>
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <i class="bi bi-person text-gray-400 mr-2" aria-hidden="true"></i>
                        <span class="text-gray-900"><?= htmlspecialchars($activity['user_name']) ?></span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        <?= getActivityColor($activity['action']) ?>">
                        <?= formatAction($activity['action']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-md">
                      <div class="truncate" title="<?= htmlspecialchars($activity['new_values'] ?? 'No details') ?>">
                        <?= formatNewValues($activity['new_values']) ?>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <span class="text-xs text-gray-500">
                        <?= $activity['ip_address'] ?? 'N/A' ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="px-8 py-16 text-center text-gray-500">
                      <div class="space-y-6">
                        <i class="bi bi-inbox text-8xl text-gray-300 block mx-auto" aria-hidden="true"></i>
                        <div>
                          <h4 class="text-xl font-semibold text-gray-900 mb-3">No activities found</h4>
                          <p class="text-gray-600 mb-6 text-lg">No activities have been recorded yet</p>
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

    <!-- JavaScript for activity color classes -->
    <script>
    function getActivityColor(action) {
        const actionColors = {
            'CREATE_REQUEST': 'bg-green-100 text-green-800',
            'UPDATE_STATUS': 'bg-blue-100 text-blue-800',
            'SCAN_COMPLETE': 'bg-purple-100 text-purple-800',
            'COMPLETE_REQUEST': 'bg-green-100 text-green-800',
            'LOGIN': 'bg-indigo-100 text-indigo-800',
            'LOGOUT': 'bg-red-100 text-red-800'
        };
        return actionColors[action] || 'bg-gray-100 text-gray-800';
    }
    
    function formatAction(action) {
        const actionNames = {
            'CREATE_REQUEST': 'Created Request',
            'UPDATE_STATUS': 'Updated Status',
            'SCAN_COMPLETE': 'Scanned QR Code',
            'COMPLETE_REQUEST': 'Completed Request',
            'LOGIN': 'User Login',
            'LOGOUT': 'User Logout'
        };
        return actionNames[action] || action;
    }
    
    function formatNewValues(newValues) {
        try {
            const data = JSON.parse(newValues);
            if (typeof data === 'object' && data !== null) {
                const details = [];
                Object.keys(data).forEach(key => {
                    if (data[key] !== null && data[key] !== '') {
                        details.push(`${key}: ${data[key]}`);
                    }
                });
                return details.join(', ');
            }
            return 'No details provided';
        } catch (e) {
            return 'Invalid JSON format';
        }
    }
    </script>

    <!-- Page specific CSS -->
    <style>
      .activity-table {
        table-layout: auto;
      }
      
      .activity-table th {
        position: sticky;
        top: 0;
        z-index: 10;
      }
      
      .activity-table tbody tr:hover {
        background-color: #f9fafb;
      }
      
      .activity-table td {
        vertical-align: top;
      }
      
      .activity-table .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
      }
      
      .activity-table .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
      }
    </style>

<?php include '../common/footer.php'; ?>
</body>
</html>

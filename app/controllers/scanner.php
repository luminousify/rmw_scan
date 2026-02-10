<?php
require_once '../../config.php';
require_once '../../includes/DatabaseManager.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . url());
    exit();
}

$module_name = "scanner";
$title = "Scan QR Code";
$name = $_SESSION['user'];
$idlog = $_SESSION['idlog'];
$department = $_SESSION['department'];

// Import DatabaseManager for division queries (consistent with working controllers)
require_once '../../includes/DatabaseManager.php';

// Debug: Log session information
error_log("[Scanner] Session debug - User: '$name', ID: '$idlog', Department: '$department'");

// Get user's division (multiple sources with fallback)
$userDivision = null;

// 1. Try to get from session first (most reliable)
if (isset($_SESSION['division']) && !empty($_SESSION['division'])) {
    $userDivision = $_SESSION['division'];
    error_log("[Scanner] Division from session: $userDivision");
}

// 2. If session division is empty, query database
if (!$userDivision && $idlog > 0) {
    try {
        $db = DatabaseManager::getInstance();
        $stmt = $db->query("SELECT division FROM users WHERE id = ?", [$idlog]);
        $userDivision = $stmt->fetchColumn();
        
        error_log("[Scanner] Division from DB query: " . ($userDivision ?? 'NULL/empty'));
    } catch (Exception $e) {
        error_log("[Scanner] Error getting user division from DB: " . $e->getMessage());
    }
}

// 3. Final fallback
if (!$userDivision) {
    error_log("[Scanner] No division found for user ID $idlog, using default");
    $userDivision = 'Unassigned';
}

error_log("[Scanner] Final division value: $userDivision");

// Get request number from URL parameter if available
$requestNumberFromUrl = $_GET['request_number'] ?? '';

/**
 * Get StockDetailVer materials by LPB_SJ_No
 * Uses real data from StockDetailVer table
 */
function getStockDetailVerMaterialsByLpb($lpbSjNo) {
    try {
        // Use DatabaseManager to get StockDetailVer data
        $dbManager = DatabaseManager::getInstance();
        return $dbManager->getStockDetailVerMaterialsByLpbSjNo($lpbSjNo);
    } catch (Exception $e) {
        error_log("Error getting StockDetailVer LPB data: " . $e->getMessage());
        return null;
    }
}

/**
 * Compare request materials with customer reference materials
 * OPTIMIZATION: Use array_column() for faster array building (O(n) vs O(n²) complexity reduction)
 */
function compareMaterials($requestItems, $customerItems) {
    $comparison = [
        'matched' => [],
        'mismatched_quantities' => [],
        'missing_in_customer' => [],
        'extra_in_customer' => [],
        'summary' => []
    ];

    // OPTIMIZATION: Use array_column() to build maps directly (faster than foreach with nested arrays)
    $requestMap = [];
    $requestUnits = [];
    if (!empty($requestItems) && is_array($requestItems)) {
        $requestMap = array_column($requestItems, 'requested_quantity', 'product_id');
        $requestUnits = array_column($requestItems, 'unit', 'product_id');
    }

    // Normalize customer items by product_id
    $customerMap = [];
    $customerUnits = [];
    if (!empty($customerItems) && is_array($customerItems)) {
        $customerMap = array_column($customerItems, 'quantity', 'product_id');
        $customerUnits = array_column($customerItems, 'unit', 'product_id');
    }

    // Find matches and mismatches
    foreach ($requestMap as $productId => $requestedQty) {
        if (isset($customerMap[$productId])) {
            $customerQty = $customerMap[$productId];

            // Check for quantity differences
            if ($requestedQty != $customerQty) {
                $comparison['mismatched_quantities'][] = [
                    'product_id' => $productId,
                    'product_name' => $productId, // Using product_id as name since we optimized it out
                    'request_quantity' => $requestedQty,
                    'customer_quantity' => $customerQty
                ];
            }

            // If quantity matches (comparison is by product_id + quantity only)
            if ($requestedQty == $customerQty) {
                $comparison['matched'][] = [
                    'product_id' => $productId,
                    'product_name' => $productId, // Using product_id as name since we optimized it out
                    'quantity' => $requestedQty,
                    'unit' => $requestUnits[$productId] ?? 'pcs'
                ];
            }
        } else {
            // Item missing in customer reference
            $comparison['missing_in_customer'][] = $requestItem;
        }
    }
    
    // Find extra items in customer reference
    foreach ($customerMap as $productId => $customerQty) {
        if (!isset($requestMap[$productId])) {
            $comparison['extra_in_customer'][] = [
                'product_id' => $productId,
                'product_name' => $productId,
                'quantity' => $customerQty,
                'unit' => $customerUnits[$productId] ?? 'pcs'
            ];
        }
    }
    
    // Build summary
    $totalIssues = count($comparison['mismatched_quantities']) + 
                   count($comparison['missing_in_customer']) + 
                   count($comparison['extra_in_customer']);
    
    $comparison['summary'] = [
        'total_request_items' => count($requestItems),
        'total_customer_items' => count($customerItems),
        'matched_items' => count($comparison['matched']),
        'total_issues' => $totalIssues,
        'identical' => $totalIssues === 0
    ];
    
    return $comparison;
}

include '../common/header.php';

if ($_POST == NULL) {
    $dat = '';
    $nobon = ''; // Keep this empty for QR code scanning
    $requestDetails = null;
    $currentRequestNumber = ''; // Store request number for hidden field
    
    // If request number is passed via URL, pre-populate and auto-process
    if (!empty($requestNumberFromUrl)) {
        // Don't set $nobon to the request number - keep it empty for QR scanning
        $currentRequestNumber = $requestNumberFromUrl; // Store for hidden field
        
        try {
            include '../../includes/conn_mysql.php';
            
            // Check if request exists
            $requestQuery = "
                SELECT 
                    mr.id as request_id,
                    mr.request_number,
                    mr.status,
                    mr.priority,
                    mr.notes,
                    mr.customer_reference,
                    u.full_name as production_user
                FROM material_requests mr
                LEFT JOIN users u ON mr.production_user_id = u.id
                WHERE mr.request_number = ?
            ";
            
            $stmt = $pdo->prepare($requestQuery);
            $stmt->execute([$requestNumberFromUrl]);
            $requestResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($requestResult) {
                $requestDetails = $requestResult;
                
  
                
                // Get request items with enhanced error handling
                // OPTIMIZATION: Removed only 'id' and 'description' (kept product_name for UI display)
                $itemsQuery = "
                    SELECT
                        mri.product_id,
                        mri.product_name,
                        mri.description,
                        mri.machine,
                        mri.requested_quantity,
                        mri.unit
                    FROM material_request_items mri
                    WHERE mri.request_id = ?
                    ORDER BY mri.product_id ASC
                ";

                $stmt = $pdo->prepare($itemsQuery);
                $stmt->execute([$requestResult['request_id']]);
                $dat = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Diagnostics for empty items
                if (empty($dat)) {
                    // Check if items actually exist for this request
                    $countQuery = "SELECT COUNT(*) as count FROM material_request_items WHERE request_id = ?";
                    $stmt = $pdo->prepare($countQuery);
                    $stmt->execute([$requestResult['request_id']]);
                    $itemCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    if ($itemCount == 0) {
                        $info_message = "This request has no materials assigned. Please check the request setup.";
                    } else {
                        $error_message = "Database inconsistency detected. Found {$itemCount} items but could not retrieve them. Contact administrator.";
                    }
                }
                
            } else {
                $error_message = "Request not found: " . $requestNumberFromUrl;
            }
            
        } catch (Exception $e) {
            $error_message = "Error loading request: " . $e->getMessage();
        }
    }
    
    include '../scan.php';
} else {
    $id = $_POST['nobon'] ?? '';
    $lpbSjNo = trim($id);
    $currentRequestNumber = $_POST['current_request_number'] ?? '';
    
    error_log("[Scanner] Processing POST: nobon='$lpbSjNo', current_request_number='$currentRequestNumber'");
    
    // If no current request number, check for request number input
    if (empty($currentRequestNumber) && !empty($_POST['request_number_input'])) {
        $currentRequestNumber = trim($_POST['request_number_input']);
    }
    
    // Initialize variables for the view
    $dat = '';
    $nobon = '';
    $requestDetails = null;
    $customerReferenceData = null;
    $comparisonResults = null;
    
    try {
        include '../../includes/conn_mysql.php';
        
        // BENCHMARK: Start timing
        $benchmark_start = microtime(true);
        $benchmark_points = [];
        
        // Log: Request received
        $benchmark_points['start'] = microtime(true);
        error_log("[BENCHMARK] Scan started at: " . date('Y-m-d H:i:s') . " for request: $currentRequestNumber, LPB: $lpbSjNo");
        
        // Validate request number
        if (empty($currentRequestNumber)) {
            throw new Exception("No request number specified");
        }
        
        // Validate LPB_SJ_No using DatabaseManager
        $dbManager = DatabaseManager::getInstance();
        $validation = $dbManager->validateLpbSjNo($lpbSjNo);
        
        // BENCHMARK: After validation
        $benchmark_points['after_validation'] = microtime(true);
        error_log("[BENCHMARK] Validation completed in: " . number_format(($benchmark_points['after_validation'] - $benchmark_points['start']) * 1000, 2) . "ms");
        
        if (!$validation['valid']) {
            // Enhanced error message with debugging information
            $errorMsg = $validation['message'];
            if (stripos($errorMsg, 'tidak ditemukan') !== false) {
                $errorMsg .= ". Silakan periksa apakah Nomor Bon tersebut ada di Database.";
            }
            
            // Special handling for already verified items
            if (isset($validation['already_verified']) && $validation['already_verified']) {
                $errorMsg = "⚠️ " . $validation['message'] . 
                           " Nomor Bon ini telah diverifikasi " . $validation['verified_count'] . " record(s).";
            }
            
            throw new Exception($errorMsg);
        }
        
        // Log successful validation for debugging
        error_log("Nomor Bon validation successful: '$lpbSjNo' - " . $validation['message']);
        
        // Get request details (no status restriction for comparison)
        $requestQuery = "
            SELECT 
                mr.id as request_id,
                mr.request_number,
                mr.status,
                mr.priority,
                mr.notes,
                mr.customer_reference,
                u.full_name as production_user
            FROM material_requests mr
            LEFT JOIN users u ON mr.production_user_id = u.id
            WHERE mr.request_number = ?
        ";
        
        $stmt = $pdo->prepare($requestQuery);
        $stmt->execute([$currentRequestNumber]);
        $requestResult = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$requestResult) {
            throw new Exception("Request not found: " . $currentRequestNumber);
        }
        
        $requestDetails = $requestResult;
        
        // Get request items
        // OPTIMIZATION: Removed only 'id' and 'description' (kept product_name for UI display)
        $itemsQuery = "
            SELECT
                mri.product_id,
                mri.product_name,
                mri.description,
                mri.machine,
                mri.requested_quantity,
                mri.unit
            FROM material_request_items mri
            WHERE mri.request_id = ?
            ORDER BY mri.product_id ASC
        ";

        $stmt = $pdo->prepare($itemsQuery);
        $stmt->execute([$requestResult['request_id']]);
        $requestItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // BENCHMARK: After fetching request items
        $benchmark_points['after_request_items'] = microtime(true);
        error_log("[BENCHMARK] Request items fetched in: " . number_format(($benchmark_points['after_request_items'] - $benchmark_points['after_validation']) * 1000, 2) . "ms");
        
        if (empty($requestItems)) {
            $info_message = "Request has no materials to compare.";
        }
        
        // Get StockDetailVer LPB data
        $customerReferenceData = getStockDetailVerMaterialsByLpb($lpbSjNo);
        
        // BENCHMARK: After fetching StockDetailVer
        $benchmark_points['after_stockdetailver'] = microtime(true);
        error_log("[BENCHMARK] StockDetailVer fetched in: " . number_format(($benchmark_points['after_stockdetailver'] - $benchmark_points['after_request_items']) * 1000, 2) . "ms");
        
        if (!$customerReferenceData) {
            $warning_message = "Data tidak ditemukan untuk Nomor Bon: " . $lpbSjNo . " di Database";
        } else {
            // Perform comparison
            $comparisonResults = compareMaterials($requestItems, $customerReferenceData['items']);

            // BENCHMARK: After comparison
            $benchmark_points['after_comparison'] = microtime(true);
            error_log("[BENCHMARK] Comparison completed in: " . number_format(($benchmark_points['after_comparison'] - $benchmark_points['after_stockdetailver']) * 1000, 2) . "ms");

            // Debug summary for troubleshooting issue counts
            $summary = $comparisonResults['summary'] ?? [];
            error_log("[Scanner] Compare summary request_number='{$currentRequestNumber}' bon='{$lpbSjNo}' matched=" . ($summary['matched_items'] ?? 0) .
                      " issues=" . ($summary['total_issues'] ?? 0) .
                      " qty_mismatch=" . count($comparisonResults['mismatched_quantities'] ?? []) .
                      " missing=" . count($comparisonResults['missing_in_customer'] ?? []) .
                      " extra=" . count($comparisonResults['extra_in_customer'] ?? []));
            
            // Generate appropriate message based on comparison results
            if ($comparisonResults['summary']['identical']) {
                $success_message = "Perfect match! Both material lists are identical ({$comparisonResults['summary']['matched_items']} items)";
            } else {
                $warning_message = "Material differences detected: ";
                $issues = [];

                if (!empty($comparisonResults['mismatched_quantities'])) {
                    $issues[] = count($comparisonResults['mismatched_quantities']) . " quantity difference(s)";
                }
                if (!empty($comparisonResults['missing_in_customer'])) {
                    $issues[] = count($comparisonResults['missing_in_customer']) . " missing item(s)";
                }
                if (!empty($comparisonResults['extra_in_customer'])) {
                    $issues[] = count($comparisonResults['extra_in_customer']) . " extra item(s)";
                }

                $warning_message .= implode(", ", $issues) . ". See comparison details below.";
            }

            // Auto-complete on perfect match if enabled
            $auto_completed = false;
            if ($comparisonResults['summary']['identical']
                && $department === 'production'
                && $requestDetails['status'] === 'ready'
                && AUTO_COMPLETE_ON_PERFECT_MATCH) {

                // Check if any items are already verified
                $hasVerifiedItems = false;
                foreach ($customerReferenceData['items'] as $item) {
                    if (isset($item['Verifikasi']) && $item['Verifikasi'] == 1) {
                        $hasVerifiedItems = true;
                        break;
                    }
                }

                // Only auto-complete if no items are already verified
                if (!$hasVerifiedItems) {
                    try {
                        // Apply delay if configured
                        if (AUTO_COMPLETE_DELAY_SECONDS > 0) {
                            sleep(AUTO_COMPLETE_DELAY_SECONDS);
                        }

                        // Start transaction for atomic operations
                        $pdo->beginTransaction();

                        // 1. Update StockDetailVer Verifikasi = 1 for records with matching LPB_SJ_No
                        $stmt = $pdo->prepare("
                            UPDATE StockDetailVer
                            SET Verifikasi = 1
                            WHERE LPB_SJ_No = ?
                        ");
                        $stmt->execute([$lpbSjNo]);
                        $stockDetailVerUpdated = $stmt->rowCount();

                        // 2. Update material_requests status to completed
                        $completedBy = $_SESSION['full_name'] ?? $_SESSION['user'];
                        $stmt = $pdo->prepare("
                            UPDATE material_requests
                            SET status = 'completed',
                                completed_date = CURRENT_TIMESTAMP,
                                completed_by = ?,
                                updated_at = CURRENT_TIMESTAMP
                            WHERE request_number = ?
                        ");
                        $stmt->execute([$completedBy, $currentRequestNumber]);

                        // Commit transaction
                        $pdo->commit();
                        
                        // BENCHMARK: After updates
                        $benchmark_points['after_updates'] = microtime(true);
                        error_log("[BENCHMARK] Database updates completed in: " . number_format(($benchmark_points['after_updates'] - $benchmark_points['after_comparison']) * 1000, 2) . "ms");

                        // Set flag for view
                        $auto_completed = true;
                        $success_message = "✅ Perfect match! Request automatically completed ({$comparisonResults['summary']['matched_items']} items verified)";

                        // Prepare redirect URL (will be used in view)
                        $auto_complete_redirect = url('app/controllers/my_requests.php');

                        // Prepare activity log data
                        $activityData = [
                            'status' => 'completed',
                            'completed_by' => $completedBy,
                            'request_number' => $currentRequestNumber,
                            'scanned_reference' => $lpbSjNo,
                            'auto_completed' => true,
                            'stockdetailver_updated' => $stockDetailVerUpdated,
                            'lpb_sj_numbers' => [$lpbSjNo],
                            'comparison_results' => [
                                'identical' => true,
                                'has_differences' => false,
                                'matched_items' => $comparisonResults['summary']['matched_items'],
                                'total_issues' => 0
                            ]
                        ];

                        // Log activity with AUTO_COMPLETE_REQUEST action
                        $stmt = $pdo->prepare("
                            INSERT INTO activity_log (user_id, action, table_name, record_id, new_values)
                            VALUES (?, 'AUTO_COMPLETE_REQUEST', 'material_requests', ?, ?)
                        ");
                        $stmt->execute([$idlog, $requestDetails['request_id'], json_encode($activityData)]);

                        // Refresh request details after completion
                        $stmt = $pdo->prepare("
                            SELECT
                                mr.id as request_id,
                                mr.request_number,
                                mr.status,
                                mr.priority,
                                mr.notes,
                                mr.customer_reference,
                                u.full_name as production_user
                            FROM material_requests mr
                            LEFT JOIN users u ON mr.production_user_id = u.id
                            WHERE mr.request_number = ?
                        ");
                        $stmt->execute([$currentRequestNumber]);
                        $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);

                    } catch (Exception $e) {
                        // Rollback on error
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        $error_message = "Auto-completion failed: " . $e->getMessage();
                        $auto_completed = false;
                    }
                }
            }
        }

        $dat = $requestItems;
        $nobon = $lpbSjNo;
        
        // BENCHMARK: Total time
        $benchmark_end = microtime(true);
        $total_time = ($benchmark_end - $benchmark_start) * 1000;
        error_log("[BENCHMARK] TOTAL TIME: " . number_format($total_time, 2) . "ms");
        
    } catch (Exception $e) {
        $error_message = "Gagal memproses perbandingan: " . $e->getMessage();
        $dat = '';
        $nobon = '';
        $requestDetails = null;
        $customerReferenceData = null;
        $comparisonResults = null;
    }
    
    // Handle completion action (only for production users, only when status is 'ready')
    // Allow completion regardless of comparison differences
    if (isset($_POST['action']) && $_POST['action'] === 'complete_request' && $department === 'production') {
        try {
            if (empty($currentRequestNumber)) {
                throw new Exception("No request number specified");
            }
            
            // Verify request status is 'ready'
            $stmt = $pdo->prepare("SELECT id, status FROM material_requests WHERE request_number = ?");
            $stmt->execute([$currentRequestNumber]);
            $requestCheck = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$requestCheck) {
                throw new Exception("Request not found");
            }
            
            if ($requestCheck['status'] !== 'ready') {
                throw new Exception("Request must be in 'ready' status to complete. Current status: " . $requestCheck['status']);
            }
            
            // Get scanned reference from POST (may be empty if no scan was done)
            $scannedReference = $_POST['nobon'] ?? '';
            
            // Start transaction for atomic operations
            $pdo->beginTransaction();
            
            $completedBy = $_SESSION['full_name'] ?? $_SESSION['user'];
            $stockDetailVerUpdated = 0;
            $lpbSjNumbersUpdated = [];
            $itemsApproved = 0;
            
            try {
                // 1. Update StockDetailVer Verifikasi = 1 for records with matching LPB_SJ_No
                if (!empty($scannedReference)) {
                    // Update Verifikasi = 1 for all records with matching LPB_SJ_No
                    $stmt = $pdo->prepare("
                        UPDATE StockDetailVer 
                        SET Verifikasi = 1 
                        WHERE LPB_SJ_No = ?
                    ");
                    $stmt->execute([$scannedReference]);
                    $stockDetailVerUpdated = $stmt->rowCount();
                    $lpbSjNumbersUpdated = [$scannedReference];
                }
                
                // 2. Update material_requests status to completed
                $stmt = $pdo->prepare("
                    UPDATE material_requests 
                    SET status = 'completed', 
                        completed_date = CURRENT_TIMESTAMP, 
                        completed_by = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE request_number = ?
                ");
                $stmt->execute([$completedBy, $currentRequestNumber]);
                
                // Commit transaction
                $pdo->commit();
                
            } catch (Exception $e) {
                // Rollback on error
                $pdo->rollBack();
                throw $e;
            }
            
            // Prepare activity log data
            $activityData = [
                'status' => 'completed',
                'completed_by' => $completedBy,
                'request_number' => $currentRequestNumber,
                'scanned_reference' => !empty($scannedReference) ? $scannedReference : null,
                'completed_without_scan' => empty($scannedReference),
                'stockdetailver_updated' => $stockDetailVerUpdated,
                'lpb_sj_numbers' => $lpbSjNumbersUpdated,
                'items_approved' => $itemsApproved
            ];
            
            // Include comparison results if available (even with differences)
            // Note: comparisonResults may not be available in this scope if completion is done separately
            // This is fine - we allow completion regardless
            if (isset($comparisonResults)) {
                $activityData['comparison_results'] = [
                    'identical' => $comparisonResults['summary']['identical'] ?? false,
                    'has_differences' => !($comparisonResults['summary']['identical'] ?? true),
                    'matched_items' => $comparisonResults['summary']['matched_items'] ?? 0,
                    'total_issues' => $comparisonResults['summary']['total_issues'] ?? 0,
                    'mismatched_quantities_count' => count($comparisonResults['mismatched_quantities'] ?? []),
                    'missing_items_count' => count($comparisonResults['missing_in_customer'] ?? []),
                    'extra_items_count' => count($comparisonResults['extra_in_customer'] ?? [])
                ];
                $activityData['completed_with_differences'] = !($comparisonResults['summary']['identical'] ?? true);
            }
            
            // Log activity
            $stmt = $pdo->prepare("
                INSERT INTO activity_log (user_id, action, table_name, record_id, new_values) 
                VALUES (?, 'COMPLETE_REQUEST', 'material_requests', ?, ?)
            ");
            $stmt->execute([$idlog, $requestCheck['id'], json_encode($activityData)]);
            
            // Success message with details
            $success_message = "Request completed successfully! ";
            $details = [];
            
            if ($itemsApproved > 0) {
                $details[] = "{$itemsApproved} item(s) approved";
            }
            
            if ($stockDetailVerUpdated > 0) {
                $details[] = "{$stockDetailVerUpdated} StockDetailVer record(s) verified";
                if (!empty($lpbSjNumbersUpdated)) {
                    $details[] = "LPB_SJ_No: " . implode(', ', $lpbSjNumbersUpdated);
                }
            }
            
            if (!empty($details)) {
                $success_message .= implode(', ', $details) . ". ";
            }
            
            if (isset($comparisonResults) && !$comparisonResults['summary']['identical']) {
                $success_message .= "Note: Differences were found during scan. Please verify materials manually.";
            }
            
            // Refresh request details
            $refreshQuery = "
                SELECT 
                    mr.id as request_id,
                    mr.request_number,
                    mr.status,
                    mr.priority,
                    mr.notes,
                    mr.customer_reference,
                    u.full_name as production_user
                FROM material_requests mr
                LEFT JOIN users u ON mr.production_user_id = u.id
                WHERE mr.request_number = ?
            ";
            $stmt = $pdo->prepare($refreshQuery);
            $stmt->execute([$currentRequestNumber]);
            $requestDetails = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Clear comparison results after completion to prevent re-display
            $comparisonResults = null;
            $customerReferenceData = null;
            
        } catch (Exception $e) {
            $error_message = "Error completing request: " . $e->getMessage();
        }
    }
    
    include '../scan.php';
}

include '../common/footer.php';
?>
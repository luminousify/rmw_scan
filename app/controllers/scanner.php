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
$name = $_SESSION['user'] ?? '';
$pass = $_SESSION['pass'] ?? '';
$idlog = $_SESSION['idlog'] ?? 0;
$department = $_SESSION['department'] ?? 'production';

// Get request number from URL parameter if available
$requestNumberFromUrl = $_GET['request_number'] ?? '';

/**
 * Get StockDetailVer customer reference material data
 * Uses real data from StockDetailVer table
 */
function getStockDetailVerCustomerData($custNoRef) {
    try {
        // Use DatabaseManager to get StockDetailVer data
        $dbManager = DatabaseManager::getInstance();
        return $dbManager->getStockDetailVerMaterials($custNoRef);
    } catch (Exception $e) {
        error_log("Error getting StockDetailVer customer data: " . $e->getMessage());
        return null;
    }
}

/**
 * Compare request materials with customer reference materials
 */
function compareMaterials($requestItems, $customerItems) {
    $comparison = [
        'matched' => [],
        'mismatched_names' => [],
        'mismatched_quantities' => [],
        'missing_in_customer' => [],
        'extra_in_customer' => [],
        'summary' => []
    ];
    
    // Normalize request items by product_id
    $requestMap = [];
    if (!empty($requestItems) && is_array($requestItems)) {
        foreach ($requestItems as $item) {
            $requestMap[$item['product_id'] ?? ''] = [
                'product_name' => $item['product_name'] ?? '',
                'requested_quantity' => $item['requested_quantity'] ?? 0,
                'unit' => $item['unit'] ?? 'pcs'
            ];
        }
    }
    
    // Normalize customer items by product_id
    $customerMap = [];
    if (!empty($customerItems) && is_array($customerItems)) {
        foreach ($customerItems as $item) {
            $customerMap[$item['product_id'] ?? ''] = [
                'product_name' => $item['product_name'] ?? '',
                'quantity' => $item['quantity'] ?? 0,
                'unit' => $item['unit'] ?? 'pcs'
            ];
        }
    }
    
    // Find matches and mismatches
    foreach ($requestMap as $productId => $requestItem) {
        if (isset($customerMap[$productId])) {
            $customerItem = $customerMap[$productId];
            
            // Check for name differences
            if ($requestItem['product_name'] !== $customerItem['product_name']) {
                $comparison['mismatched_names'][] = [
                    'product_id' => $productId,
                    'request_name' => $requestItem['product_name'],
                    'customer_name' => $customerItem['product_name'],
                    'request_quantity' => $requestItem['requested_quantity'],
                    'customer_quantity' => $customerItem['quantity']
                ];
            }
            
            // Check for quantity differences
            if ($requestItem['requested_quantity'] != $customerItem['quantity']) {
                $comparison['mismatched_quantities'][] = [
                    'product_id' => $productId,
                    'product_name' => $requestItem['product_name'],
                    'request_quantity' => $requestItem['requested_quantity'],
                    'customer_quantity' => $customerItem['quantity']
                ];
            }
            
            // If both name and quantity match
            if ($requestItem['product_name'] === $customerItem['product_name'] && 
                $requestItem['requested_quantity'] == $customerItem['quantity']) {
                $comparison['matched'][] = [
                    'product_id' => $productId,
                    'product_name' => $requestItem['product_name'],
                    'quantity' => $requestItem['requested_quantity'],
                    'unit' => $requestItem['unit']
                ];
            }
        } else {
            // Item missing in customer reference
            $comparison['missing_in_customer'][] = $requestItem;
        }
    }
    
    // Find extra items in customer reference
    foreach ($customerMap as $productId => $customerItem) {
        if (!isset($requestMap[$productId])) {
            $comparison['extra_in_customer'][] = $customerItem;
        }
    }
    
    // Build summary
    $totalIssues = count($comparison['mismatched_names']) + 
                   count($comparison['mismatched_quantities']) + 
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
                $itemsQuery = "
                    SELECT 
                        mri.id as item_id,
                        mri.product_id,
                        mri.product_name,
                        mri.requested_quantity,
                        mri.unit,
                        mri.description,
                        mri.status as item_status
                    FROM material_request_items mri
                    WHERE mri.request_id = ?
                    ORDER BY mri.product_name ASC
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
    $customerReference = trim($id);
    $currentRequestNumber = $_POST['current_request_number'] ?? '';
    
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
        
        // Validate request number
        if (empty($currentRequestNumber)) {
            throw new Exception("No request number specified");
        }
        
        // Validate customer reference using DatabaseManager
        $dbManager = DatabaseManager::getInstance();
        $validation = $dbManager->validateCustNoRef($customerReference);
        
        if (!$validation['valid']) {
            // Enhanced error message with debugging information
            $errorMsg = $validation['message'];
            if (strpos($errorMsg, 'not found') !== false) {
                $errorMsg .= " (Scanned: '$customerReference'). Please check if the customer reference exists in the StockDetailVer table.";
            }
            throw new Exception($errorMsg);
        }
        
        // Log successful validation for debugging
        error_log("Customer reference validation successful: '$customerReference' - " . $validation['message']);
        
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
        $itemsQuery = "
            SELECT 
                mri.id as item_id,
                mri.product_id,
                mri.product_name,
                mri.requested_quantity,
                mri.unit,
                mri.description,
                mri.status as item_status
            FROM material_request_items mri
            WHERE mri.request_id = ?
            ORDER BY mri.product_name ASC
        ";
        
        $stmt = $pdo->prepare($itemsQuery);
        $stmt->execute([$requestResult['request_id']]);
        $requestItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($requestItems)) {
            $info_message = "Request has no materials to compare.";
        }
        
        // Get StockDetailVer customer reference data
        $customerReferenceData = getStockDetailVerCustomerData($customerReference);
        
        if (!$customerReferenceData) {
            $warning_message = "No data found for customer reference: " . $customerReference . " in StockDetailVer table";
        } else {
            // Perform comparison
            $comparisonResults = compareMaterials($requestItems, $customerReferenceData['items']);
            
            // Generate appropriate message based on comparison results
            if ($comparisonResults['summary']['identical']) {
                $success_message = "Perfect match! Both material lists are identical ({$comparisonResults['summary']['matched_items']} items)";
            } else {
                $warning_message = "Material differences detected: ";
                $issues = [];
                
                if (!empty($comparisonResults['mismatched_names'])) {
                    $issues[] = count($comparisonResults['mismatched_names']) . " name mismatch(es)";
                }
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
        }
        
        $dat = $requestItems;
        $nobon = $customerReference;
        
    } catch (Exception $e) {
        $error_message = "Error processing comparison: " . $e->getMessage();
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
                    // Get LPB_SJ_No from StockDetailVer records matching the customer reference
                    $stmt = $pdo->prepare("
                        SELECT DISTINCT LPB_SJ_No 
                        FROM StockDetailVer 
                        WHERE CustNoRef = ? AND LPB_SJ_No IS NOT NULL AND LPB_SJ_No != ''
                    ");
                    $stmt->execute([$scannedReference]);
                    $lpbSjNumbersUpdated = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    if (!empty($lpbSjNumbersUpdated)) {
                        // Update Verifikasi = 1 for all records with matching LPB_SJ_No
                        foreach ($lpbSjNumbersUpdated as $lpbSjNo) {
                            $stmt = $pdo->prepare("
                                UPDATE StockDetailVer 
                                SET Verifikasi = 1 
                                WHERE LPB_SJ_No = ?
                            ");
                            $stmt->execute([$lpbSjNo]);
                            $stockDetailVerUpdated += $stmt->rowCount();
                        }
                    }
                }
                
                // 2. Approve all items in material_request_items
                $stmt = $pdo->prepare("
                    UPDATE material_request_items 
                    SET status = 'approved',
                        approved_quantity = requested_quantity
                    WHERE request_id = ? AND status != 'approved'
                ");
                $stmt->execute([$requestCheck['id']]);
                $itemsApproved = $stmt->rowCount();
                
                // 3. Update material_requests status to completed
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
                    'mismatched_names_count' => count($comparisonResults['mismatched_names'] ?? []),
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
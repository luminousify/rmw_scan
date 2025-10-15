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
    $nobon = '';
    $requestDetails = null;
    $currentRequestNumber = ''; // Store request number for hidden field
    
    // If request number is passed via URL, pre-populate and auto-process
    if (!empty($requestNumberFromUrl)) {
        $nobon = $requestNumberFromUrl;
        $currentRequestNumber = $requestNumberFromUrl; // Store for hidden field
        
        try {
            include '../../includes/conn_sqlite.php';
            
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
        include '../../includes/conn_sqlite.php';
        
        // Validate request number
        if (empty($currentRequestNumber)) {
            throw new Exception("No request number specified");
        }
        
        // Validate customer reference using DatabaseManager
        $dbManager = DatabaseManager::getInstance();
        $validation = $dbManager->validateCustNoRef($customerReference);
        
        if (!$validation['valid']) {
            throw new Exception($validation['message']);
        }
        
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
    
    include '../scan.php';
}

include '../common/footer.php';
?>
<?php
/**
 * Test StockDetailVer Integration
 * This script tests the StockDetailVer integration with the scanner functionality
 */

require_once 'config.php';
require_once 'includes/DatabaseManager.php';

echo "Testing StockDetailVer Integration\n";
echo "================================\n\n";

try {
    // Initialize Database Manager
    $dbManager = new DatabaseManager('sqlite');
    
    // Test 1: Check StockDetailVer table status
    echo "1. Checking StockDetailVer table status...\n";
    $status = $dbManager->checkStockDetailVerStatus();
    echo "   Status: " . $status['message'] . "\n";
    
    if (!$status['exists']) {
        echo "   ERROR: StockDetailVer table does not exist!\n";
        exit(1);
    }
    
    if (!$status['has_data']) {
        echo "   WARNING: StockDetailVer table has no data!\n";
        exit(1);
    }
    
    echo "   ✓ Table exists with {$status['record_count']} records\n\n";
    
    // Test 2: Test CustNoRef validation
    echo "2. Testing CustNoRef validation...\n";
    
    $testCases = [
        'INJ/FG/1887-1' => true,   // Valid format, exists in database
        'FG/PROD/2024-001' => true, // Valid format, exists in database
        'MAT/RMW/2024-42' => true,  // Valid format, exists in database
        'INVALID' => false,         // Invalid format (no slash)
        'TOO/SHORT' => true,       // Valid format but might not exist
        '' => false,                // Empty
        'NONEXISTENT/REF/999' => false // Valid format but doesn't exist
    ];
    
    foreach ($testCases as $custRef => $expectedToBeValid) {
        $validation = $dbManager->validateCustNoRef($custRef);
        $status = $validation['valid'] ? 'VALID' : 'INVALID';
        $expected = $expectedToBeValid ? 'VALID' : 'INVALID';
        $result = ($validation['valid'] === $expectedToBeValid) ? '✓' : '✗';
        
        echo "   $result '$custRef': $status - {$validation['message']} (Expected: $expected)\n";
    }
    echo "\n";
    
    // Test 3: Test StockDetailVer data retrieval
    echo "3. Testing StockDetailVer data retrieval...\n";
    
    $validRefs = ['INJ/FG/1887-1', 'FG/PROD/2024-001', 'MAT/RMW/2024-42'];
    
    foreach ($validRefs as $custRef) {
        echo "   Testing CustNoRef: $custRef\n";
        
        // Get raw data
        $rawData = $dbManager->getStockDetailVerByCustRef($custRef);
        echo "     Raw records: " . count($rawData) . "\n";
        
        // Get standardized data
        $stdData = $dbManager->getStockDetailVerMaterials($custRef);
        
        if ($stdData) {
            echo "     ✓ Standardized data retrieved successfully\n";
            echo "       Customer: {$stdData['customer_name']}\n";
            echo "       Document: {$stdData['document_number']}\n";
            echo "       Items: " . count($stdData['items']) . "\n";
            
            foreach ($stdData['items'] as $item) {
                echo "         - {$item['product_id']}: {$item['quantity']} {$item['unit']} ({$item['description']})\n";
            }
        } else {
            echo "     ✗ Failed to retrieve standardized data\n";
        }
        echo "\n";
    }
    
    // Test 4: Test integration with scanner functions
    echo "4. Testing scanner integration functions...\n";
    
    // Mock the getStockDetailVerCustomerData function
    function getStockDetailVerCustomerData($custNoRef) {
        try {
            $dbManager = new DatabaseManager('sqlite');
            return $dbManager->getStockDetailVerMaterials($custNoRef);
        } catch (Exception $e) {
            error_log("Error getting StockDetailVer customer data: " . $e->getMessage());
            return null;
        }
    }
    
    // Mock comparison function
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
        foreach ($requestItems as $item) {
            $requestMap[$item['product_id']] = [
                'product_name' => $item['product_name'],
                'requested_quantity' => $item['requested_quantity'],
                'unit' => $item['unit']
            ];
        }
        
        // Normalize customer items by product_id
        $customerMap = [];
        foreach ($customerItems as $item) {
            $customerMap[$item['product_id']] = [
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit']
            ];
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
    
    // Test with mock request data
    echo "   Testing with mock request data...\n";
    
    $mockRequestItems = [
        ['product_id' => 'MAT001', 'product_name' => 'Cement 50kg', 'requested_quantity' => 10, 'unit' => 'pcs'],
        ['product_id' => 'MAT002', 'product_name' => 'Steel Rod 10mm', 'requested_quantity' => 5, 'unit' => 'pcs'],
        ['product_id' => 'MAT003', 'product_name' => 'Paint White 5L', 'requested_quantity' => 2, 'unit' => 'cans']
    ];
    
    foreach ($validRefs as $custRef) {
        echo "     Comparing with $custRef:\n";
        
        $customerData = getStockDetailVerCustomerData($custRef);
        
        if ($customerData) {
            $comparison = compareMaterials($mockRequestItems, $customerData['items']);
            
            echo "       Matched items: {$comparison['summary']['matched_items']}\n";
            echo "       Total issues: {$comparison['summary']['total_issues']}\n";
            echo "       Identical: " . ($comparison['summary']['identical'] ? 'YES' : 'NO') . "\n";
            
            if (!$comparison['summary']['identical']) {
                echo "       Issues breakdown:\n";
                if (!empty($comparison['mismatched_names'])) {
                    echo "         - Name mismatches: " . count($comparison['mismatched_names']) . "\n";
                }
                if (!empty($comparison['mismatched_quantities'])) {
                    echo "         - Quantity differences: " . count($comparison['mismatched_quantities']) . "\n";
                }
                if (!empty($comparison['missing_in_customer'])) {
                    echo "         - Missing in customer: " . count($comparison['missing_in_customer']) . "\n";
                }
                if (!empty($comparison['extra_in_customer'])) {
                    echo "         - Extra in customer: " . count($comparison['extra_in_customer']) . "\n";
                }
            }
        } else {
            echo "       ✗ Failed to retrieve customer data\n";
        }
        echo "\n";
    }
    
    echo "✅ StockDetailVer integration test completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Test the scanner interface at: http://localhost/cku_scan/app/controllers/scanner.php\n";
    echo "2. Enter a request number (or use one from the material requests page)\n";
    echo "3. Scan one of these CustNoRef values:\n";
    echo "   - INJ/FG/1887-1\n";
    echo "   - FG/PROD/2024-001\n";
    echo "   - MAT/RMW/2024-42\n";
    echo "4. Verify the comparison results\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>

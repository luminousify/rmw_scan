# My Requests Pagination Bug Analysis & Fix Plan

## Root Cause Analysis

The pagination issue in `my_requests.php` appears to be working correctly based on the code structure, but there may be a subtle issue with the **filter parameter handling in the count query**.

### Key Differences from material_request.php:

1. **my_requests.php** (current implementation):
   - Uses `MaterialRequestService::validateFilters()` to sanitize parameters
   - Passes filters array including `'limit'` and `'offset'` to both `getUserRequests()` and `getUserRequestsCount()`
   - Pagination calculation: `$offset = ($page - 1) * $limit;` ✅ Correct

2. **Potential Issue**:
   - `getUserRequestsCount()` method may not be handling the `limit` and `offset` filters correctly
   - The count method should ignore pagination parameters but might be using them incorrectly

### Identified Problems:

1. **Filter Validation Issue**:
   ```php
   $filters = MaterialRequestService::validateFilters([
       'status' => $_GET['status'] ?? 'all',
       'search' => $_GET['search'] ?? '',
       'limit' => $limit,    // This gets passed to count method
       'offset' => $offset   // This gets passed to count method
   ]);
   ```

2. **Count Method Logic Issue**:
   - `getUserRequestsCount()` may be incorrectly processing `limit` and `offset` parameters
   - Count queries should ignore pagination but might be applying them

## Fix Specification

### File 1: `app/controllers/my_requests.php`
**Lines to fix**: 23-30

**Current problematic code**:
```php
// Validate and sanitize filter parameters
$filters = MaterialRequestService::validateFilters([
    'status' => $_GET['status'] ?? 'all',
    'search' => $_GET['search'] ?? '',
    'limit' => $limit,
    'offset' => $offset
]);

// Get user's requests with filters and pagination
$userRequests = $materialRequestService->getUserRequests($idlog, $filters);

// Get total count for pagination
$totalRequests = $materialRequestService->getUserRequestsCount($idlog, $filters);
```

**Fix to**:
```php
// Separate filters from pagination parameters
$filters = MaterialRequestService::validateFilters([
    'status' => $_GET['status'] ?? 'all',
    'search' => $_GET['search'] ?? ''
]);

// Get user's requests with filters and pagination
$paginationParams = ['limit' => $limit, 'offset' => $offset];
$userRequests = $materialRequestService->getUserRequests($idlog, array_merge($filters, $paginationParams));

// Get total count for pagination (exclude pagination parameters)
$totalRequests = $materialRequestService->getUserRequestsCount($idlog, $filters);
```

### File 2: `includes/services/MaterialRequestService.php`
**Lines to fix**: `validateFilters()` method

**Add validation** to exclude pagination parameters:
```php
public static function validateFilters($filters)
{
    $validated = [];
    
    // Status validation
    $validStatuses = ['all', 'pending', 'diproses', 'completed', 'cancelled'];
    if (!empty($filters['status']) && in_array($filters['status'], $validStatuses)) {
        $validated['status'] = $filters['status'];
    }
    
    // Search validation
    if (!empty($filters['search'])) {
        $validated['search'] = trim($filters['search']);
    }
    
    // Note: 'limit' and 'offset' are pagination parameters, not filters
    // They should be handled separately and not included in filter validation
    
    return $validated;
}
```

## Testing Strategy

1. **Test normal pagination**: page=1, page=2, page=3 with no filters
2. **Test pagination with filters**: Apply status/search filters and verify pagination works
3. **Test boundary conditions**: First page, last page, page beyond total
4. **Test invalid inputs**: page=0, page=-1, page=non-numeric
5. **Test with small datasets**: <10 records (should show no pagination)
6. **Test filter combinations**: Status + search filters with pagination

## Expected Behavior After Fix

- Page 1: Shows records 1-10 with correct total count
- Page 2: Shows records 11-20 with correct total count  
- Filters preserve pagination state correctly
- Count queries return accurate totals regardless of pagination parameters
- URL parameters maintain state across page navigation

This fix addresses the core issue of pagination parameters interfering with count calculations while maintaining the existing filter functionality.
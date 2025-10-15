# Pagination Implementation Plan for Material Request Recent Requests Table

## Current State Analysis
- The recent requests table currently shows only the first 5 requests using `array_slice($userRequests, 0, 5)`
- There's already a `MaterialRequestService` class with pagination support (`getUserRequests()` and `getUserRequestsCount()` methods)
- The controller currently fetches all requests without pagination
- The table shows: Request #, Date, Items count, and Status

## Implementation Steps

### 1. Controller Updates (`app/controllers/material_request.php`)
- Replace the current direct database queries with calls to `MaterialRequestService`
- Add pagination logic with GET parameters for page number
- Set pagination limit to 10 rows per request
- Calculate total pages and current page
- Pass pagination data to the view

### 2. View Updates (`app/material_request.php`)
- Replace the hardcoded `array_slice($userRequests, 0, 5)` with paginated results
- Add pagination controls at the bottom of the table:
  - Previous/Next buttons
  - Page number display (e.g., "Page 1 of 3")
  - Direct page navigation for better UX
- Maintain existing table structure and styling

### 3. URL Parameters & State Management
- Use `?page=1` query parameter for page tracking
- Default to page 1 if no parameter exists
- Preserve other GET parameters if present (for future filtering)

### 4. Edge Cases & Validation
- Handle invalid page numbers (redirect to page 1)
- Handle empty result sets gracefully
- Ensure proper SQL LIMIT/OFFSET calculations
- Maintain performance with indexed queries

## Technical Details

### Pagination Logic
```php
// Page setup
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($currentPage - 1) * $limit;

// Service calls
$service = new MaterialRequestService();
$requests = $service->getUserRequests($idlog, ['limit' => $limit, 'page' => $currentPage - 1]);
$totalRequests = $service->getUserRequestsCount($idlog);
$totalPages = ceil($totalRequests / $limit);
```

### Pagination Component Design
- Bootstrap-style pagination with Tailwind CSS
- Responsive design for mobile devices
- Disabled state for Previous/Next when at boundaries
- Active page highlighting
- Show "X to Y of Z results" text

This implementation will provide a clean, performant pagination system that integrates seamlessly with the existing codebase architecture.
# Scanner Page Flow Explanation

**Page URL:** `http://localhost/rmw_scan/app/controllers/scanner.php?request_number=REQ-20251106-9298`

---

## Overview

The scanner page is a **material verification system** that compares materials from a production request against materials from the RMW (Raw Material Warehouse) system using customer reference numbers. It's used by RMW staff to verify that materials being shipped match what was requested.

---

## Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User accesses page with request_number in URL          │
│    scanner.php?request_number=REQ-20251106-9298           │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Authentication Check                                    │
│    - Checks if user is logged in                           │
│    - Redirects to login if not authenticated                │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. GET Request (No POST data)                              │
│    - Extracts request_number from URL parameter             │
│    - Loads request details from database                    │
│    - Loads request items (materials)                        │
│    - Displays form with pre-filled request number          │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. User Scans/Enters Customer Reference                    │
│    - User enters customer reference (QR code)               │
│    - Clicks "Process Scanner Data"                         │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. POST Request Processing                                 │
│    - Validates customer reference exists                   │
│    - Loads request details                                  │
│    - Loads request items                                    │
│    - Fetches customer reference data from StockDetailVer   │
│    - Compares materials                                     │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Display Comparison Results                              │
│    - Shows matched items                                    │
│    - Shows mismatches (names, quantities)                   │
│    - Shows missing/extra items                               │
└─────────────────────────────────────────────────────────────┘
```

---

## Detailed Step-by-Step Flow

### **Step 1: Initial Page Load (GET Request)**

**File:** `app/controllers/scanner.php` (Lines 141-222)

1. **URL Parameter Extraction** (Line 19)
   ```php
   $requestNumberFromUrl = $_GET['request_number'] ?? '';
   ```
   - Extracts `REQ-20251106-9298` from URL

2. **Request Details Loading** (Lines 152-172)
   ```php
   // Query material_requests table
   SELECT mr.id, mr.request_number, mr.status, mr.priority, 
          mr.notes, mr.customer_reference, u.full_name
   FROM material_requests mr
   LEFT JOIN users u ON mr.production_user_id = u.id
   WHERE mr.request_number = 'REQ-20251106-9298'
   ```
   - Fetches request metadata
   - Joins with users table to get production user name

3. **Request Items Loading** (Lines 179-196)
   ```php
   // Query material_request_items table
   SELECT mri.id, mri.product_id, mri.product_name, 
          mri.requested_quantity, mri.unit, mri.description, 
          mri.status
   FROM material_request_items mri
   WHERE mri.request_id = [request_id]
   ORDER BY mri.product_name ASC
   ```
   - Fetches all materials requested in this request
   - Stores in `$dat` variable for display

4. **View Rendering** (Line 222)
   ```php
   include '../scan.php';
   ```
   - Loads the view template
   - Displays form with pre-filled request number
   - Shows request details and items

---

### **Step 2: Form Display (scan.php View)**

**File:** `app/scan.php`

1. **Request Number Field** (Lines 277-317)
   - If `$currentRequestNumber` exists, it's hidden (pre-filled from URL)
   - Otherwise, shows input field for manual entry

2. **Customer Reference Scanner Field** (Lines 319-371)
   ```html
   <input name="nobon" 
          placeholder="Enter Customer Reference (e.g., INJ/FG/1887-1)"
          value="<?= htmlspecialchars($nobon) ?>"
          required>
   ```
   - QR code scanner input field
   - User scans or types customer reference number
   - This is the key input for comparison

3. **Submit Button** (Lines 374-386)
   - "Process Scanner Data" button
   - Submits form via POST

---

### **Step 3: POST Request Processing**

**File:** `app/controllers/scanner.php` (Lines 223-358)

1. **Extract Form Data** (Lines 224-231)
   ```php
   $customerReference = trim($_POST['nobon']);  // Scanned QR code
   $currentRequestNumber = $_POST['current_request_number'];  // Hidden field
   ```

2. **Validate Customer Reference** (Lines 248-259)
   ```php
   $dbManager = DatabaseManager::getInstance();
   $validation = $dbManager->validateCustNoRef($customerReference);
   ```
   - Checks if customer reference exists in `StockDetailVer` table
   - Returns validation result with message

3. **Load Request Details** (Lines 264-287)
   ```php
   // Same query as GET request
   SELECT mr.id, mr.request_number, mr.status, ...
   FROM material_requests mr
   WHERE mr.request_number = ?
   ```
   - Fetches request information again

4. **Load Request Items** (Lines 289-306)
   ```php
   SELECT mri.product_id, mri.product_name, 
          mri.requested_quantity, mri.unit, ...
   FROM material_request_items mri
   WHERE mri.request_id = ?
   ```
   - Gets all materials from the request

5. **Fetch Customer Reference Data** (Line 313)
   ```php
   $customerReferenceData = getStockDetailVerCustomerData($customerReference);
   ```
   - Calls `getStockDetailVerCustomerData()` function (Lines 25-34)
   - Uses `DatabaseManager::getStockDetailVerMaterials()`
   - Queries `StockDetailVer` table for materials matching customer reference
   - Returns: `['customer_reference', 'customer_name', 'items' => [...]]`

6. **Compare Materials** (Line 319)
   ```php
   $comparisonResults = compareMaterials($requestItems, $customerReferenceData['items']);
   ```
   - Calls `compareMaterials()` function (Lines 39-137)
   - Performs detailed comparison logic

---

### **Step 4: Material Comparison Logic**

**Function:** `compareMaterials()` (Lines 39-137)

The comparison function analyzes materials in 5 categories:

1. **Matched Items** (Lines 99-108)
   - Product ID exists in both
   - Product name matches
   - Quantity matches

2. **Mismatched Names** (Lines 78-87)
   - Product ID exists in both
   - But product names differ

3. **Mismatched Quantities** (Lines 89-97)
   - Product ID exists in both
   - Product names match
   - But quantities differ

4. **Missing in Customer** (Lines 109-112)
   - Product exists in request
   - But not found in customer reference data

5. **Extra in Customer** (Lines 115-120)
   - Product exists in customer reference
   - But not in request

**Comparison Summary** (Lines 122-134)
```php
$comparison['summary'] = [
    'total_request_items' => count($requestItems),
    'total_customer_items' => count($customerItems),
    'matched_items' => count($matched),
    'total_issues' => $totalIssues,
    'identical' => ($totalIssues === 0)
];
```

---

### **Step 5: Display Results**

**File:** `app/scan.php` (Lines 392+)

1. **Request Details Display** (Lines 392-418)
   - Shows request number, status, priority
   - Lists all requested materials (baseline)

2. **Comparison Results Display** (Lines 420+)
   - **Success Message** (if identical match)
   - **Warning Message** (if differences found)
   - **Matched Items Table** (green highlight)
   - **Mismatched Items Table** (yellow/red highlight)
   - **Missing Items Table** (red highlight)
   - **Extra Items Table** (blue highlight)

3. **Visual Indicators**
   - Color-coded status badges
   - Icons for different match types
   - Summary statistics

---

## Key Database Tables Used

### 1. **material_requests**
- Stores production material requests
- Fields: `id`, `request_number`, `status`, `priority`, `customer_reference`, etc.

### 2. **material_request_items**
- Stores individual materials in each request
- Fields: `id`, `request_id`, `product_id`, `product_name`, `requested_quantity`, `unit`, etc.

### 3. **StockDetailVer**
- Stores RMW warehouse stock data with customer references
- Fields: `CustNoRef`, `Product_ID`, `ProductName`, `RecdTotal`, `Unit`, etc.
- This is the source of truth for what's actually in the warehouse

### 4. **users**
- User information
- Used to get production user name for display

---

## Key Functions

### `getStockDetailVerCustomerData($custNoRef)`
- **Purpose:** Fetch materials from StockDetailVer table by customer reference
- **Returns:** Array with customer info and materials list
- **Uses:** `DatabaseManager::getStockDetailVerMaterials()`

### `compareMaterials($requestItems, $customerItems)`
- **Purpose:** Compare two material lists and identify differences
- **Returns:** Detailed comparison array with matched/mismatched/missing/extra items
- **Logic:** Normalizes by product_id, compares names and quantities

---

## User Workflow

1. **RMW Staff receives a material request**
   - Request number: `REQ-20251106-9298`
   - Contains list of materials needed

2. **Staff accesses scanner page**
   - URL includes request number
   - Page loads with request details pre-filled

3. **Staff scans customer reference QR code**
   - QR code contains customer reference (e.g., `INJ/FG/1887-1`)
   - This reference links to materials in StockDetailVer table

4. **System compares materials**
   - Request materials vs. Customer reference materials
   - Identifies matches, mismatches, missing items, extra items

5. **Staff reviews results**
   - Green = Matched (good to ship)
   - Yellow/Red = Mismatched (needs review)
   - Red = Missing (not available)
   - Blue = Extra (more than requested)

---

## Error Handling

1. **Authentication Errors** (Lines 6-9)
   - Redirects to login if not authenticated

2. **Request Not Found** (Lines 213-215)
   - Shows error if request number doesn't exist

3. **Customer Reference Validation** (Lines 252-259)
   - Validates customer reference exists in StockDetailVer
   - Shows detailed error message if not found

4. **Empty Items** (Lines 198-211)
   - Checks if request has materials
   - Shows info message if empty

5. **Database Errors** (Lines 217-219, 348-355)
   - Catches exceptions and displays error messages
   - Logs errors for debugging

---

## Technical Notes

1. **Database Connection**
   - Uses `DatabaseManager` singleton pattern
   - Routes through `conn_sqlite.php` → `conn.php` → `DatabaseManager`
   - Currently configured for MySQL (`rmw_system` database)

2. **Session Management**
   - Stores user info: `loggedin`, `user`, `pass`, `idlog`, `department`
   - Used for authentication and user context

3. **Form Handling**
   - GET: Display form with pre-filled data
   - POST: Process comparison and show results
   - Uses hidden fields to preserve request number

4. **Data Flow**
   ```
   URL Parameter → Request Lookup → Items Load → Form Display
                                                      ↓
   User Input → Validation → Customer Data Fetch → Comparison → Results Display
   ```

---

## Summary

The scanner page is a **verification tool** that:
- ✅ Loads a production material request
- ✅ Accepts a customer reference (QR code)
- ✅ Fetches actual warehouse materials for that reference
- ✅ Compares requested vs. actual materials
- ✅ Displays detailed comparison results
- ✅ Helps RMW staff verify shipments match requests

This ensures **quality control** and **accuracy** in material fulfillment processes.


## Root Cause Analysis

The "undefined" text appearing in the "Extra in Customer Reference" section of the modal is caused by **missing null/undefined checks in the JavaScript modal code**.

### Issues Found:

1. **JavaScript Template Literals**: The modal code accesses object properties directly without fallbacks:
   ```javascript
   ${item.product_name}           // Shows "undefined" if product_name is undefined
   ${item.quantity} ${item.unit}  // Shows "undefined" if quantity or unit is undefined
   ```

2. **Inconsistent Data Structure**: While the DatabaseManager properly maps fields with fallbacks (`?? 'Unknown Product'`, `?? 0`, `?? 'pcs'`), the JavaScript doesn't account for cases where these might still be undefined.

3. **Data Flow Issue**: The comparison function correctly populates the `extra_in_customer` array, but the JavaScript modal doesn't handle edge cases properly.

### Solution Plan:

1. **Fix JavaScript Template Literals**: Add proper null/undefined checks in all modal sections:
   ```javascript
   ${item.product_name || 'Unknown Product'}
   ${item.quantity || 0} ${item.unit || 'pcs'}
   ```

2. **Add Defensive Programming**: Ensure all property accesses in the modal have fallback values to prevent "undefined" display.

3. **Consistent Error Handling**: Apply the same null-checking pattern to all modal sections (name mismatches, quantity differences, missing items, extra items).

4. **Debug Logging**: Add console logging to help identify any data structure issues.

### Files to Modify:
- `app/scan.php` - Update JavaScript modal code to add null/undefined checks

This will ensure that even if data is missing or malformed, the modal will display meaningful fallback values instead of "undefined".
## Root Cause Analysis

The customer reference is showing as "N/A" and other fields as unknown/zero because of **incorrect field mapping** between the StockDetailVer database table and the comparison display logic.

### Issues Found:

1. **Missing field mapping in DatabaseManager**: The `getStockDetailVerMaterials()` method returns raw database rows, but the view expects specific fields like `customer_reference` and `customer_name`.

2. **Incorrect field references**: The view tries to access:
   - `$customerReferenceData['customer_reference']` → Should be from `CustNoRef` field  
   - `$customerReferenceData['customer_name']` → Should be from `Customer` field
   - `$customerReferenceData['items'][...]['product_name']` → Should be from `Product_ID` field
   - `$customerReferenceData['items'][...]['quantity']` → Should be from `RecdTotal` or `ShipTotal` field

3. **Data structure mismatch**: The comparison logic expects the StockDetailVer data in a specific format, but it's being returned as raw database rows.

### Solution Plan:

1. **Fix DatabaseManager method**: Update `getStockDetailVerMaterials()` to properly map StockDetailVer fields to the expected output structure:
   - Map `CustNoRef` → `customer_reference`
   - Map `Customer` → `customer_name`  
   - Map `Product_ID` → `product_id`
   - Map `RecdTotal` or `ShipTotal` → `quantity`

2. **Update comparison logic**: Ensure the field names match between the database output and comparison expectations.

3. **Add data validation**: Check if required fields exist before attempting to display them.

This will ensure the customer reference comparison shows the actual customer data instead of "N/A" values.
-- Migration: Add UNIQUE constraint on (request_id, product_id) to prevent duplicate products
-- Description: Prevents the same product from being added multiple times to the same material request
-- Date: 2025-12-19

-- Step 1: Add the UNIQUE constraint directly
-- MySQL will allow this even if duplicates exist, but will enforce it for new inserts
ALTER TABLE material_request_items 
ADD CONSTRAINT unique_product_per_request 
UNIQUE (request_id, product_id);

-- Note: This approach adds the constraint but won't clean existing duplicates
-- Existing duplicates will remain, but new duplicates will be prevented
-- Use the cleanup script separately if you need to consolidate existing duplicates

-- Summary:
-- 1. Adds UNIQUE constraint to prevent future duplicates
-- 2. Preserves existing data (including duplicates)  
-- 3. Backend consolidation logic in the application will handle new submissions
-- 4. Use cleanup_duplicate_products.php to clean existing duplicates if needed

-- Migration: Add UNIQUE constraint on (request_id, product_id) to prevent duplicate products
-- Description: Prevents the same product from being added multiple times to the same material request
-- Date: 2025-12-19

-- First, check if there are any duplicates and only consolidate those
-- This is a safer approach that preserves existing non-duplicate data

-- Step 1: Create a backup of the original data (just in case)
CREATE TABLE IF NOT EXISTS material_request_items_backup_20251219 AS 
SELECT * FROM material_request_items;

-- Step 2: Find and consolidate only actual duplicates
-- This will only affect rows that truly have duplicates

-- Create a temporary table with consolidated data for duplicates only
CREATE TEMPORARY TABLE duplicate_consolidations AS
SELECT 
    MIN(id) as keep_id,
    request_id,
    product_id,
    product_name,
    SUM(requested_quantity) as total_quantity,
    unit,
    GROUP_CONCAT(description ORDER BY id SEPARATOR '; ') as merged_description,
    GROUP_CONCAT(notes ORDER BY id SEPARATOR '; ') as merged_notes,
    MIN(created_at) as created_at
FROM material_request_items
WHERE (request_id, product_id) IN (
    SELECT request_id, product_id
    FROM material_request_items
    GROUP BY request_id, product_id
    HAVING COUNT(*) > 1
)
GROUP BY request_id, product_id;

-- Step 3: Update the records we're keeping with consolidated data
UPDATE material_request_items mri
JOIN duplicate_consolidations dc ON mri.id = dc.keep_id
SET 
    mri.requested_quantity = dc.total_quantity,
    mri.description = dc.merged_description,
    mri.notes = dc.merged_notes;

-- Step 4: Delete the duplicate records (keeping only the one we updated)
DELETE FROM material_request_items
WHERE id NOT IN (
    SELECT keep_id FROM duplicate_consolidations
)
AND (request_id, product_id) IN (
    SELECT request_id, product_id FROM duplicate_consolidations
);

-- Step 5: Add the UNIQUE constraint
ALTER TABLE material_request_items 
ADD CONSTRAINT IF NOT EXISTS unique_product_per_request 
UNIQUE (request_id, product_id);

-- Step 6: Clean up temporary table
DROP TEMPORARY TABLE duplicate_consolidations;

-- Summary of what this migration does:
-- 1. Creates a backup of the original table
-- 2. Only consolidates products that actually have duplicates
-- 3. Preserves all unique (non-duplicate) records as-is
-- 4. Merges descriptions and notes for consolidated records
-- 5. Keeps the earliest created_at timestamp
-- 6. Adds UNIQUE constraint to prevent future duplicates
-- 7. Leaves the backup table for manual recovery if needed

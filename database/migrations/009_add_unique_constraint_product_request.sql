-- Migration: Add UNIQUE constraint on (request_id, product_id) to prevent duplicate products
-- Description: Prevents the same product from being added multiple times to the same material request
-- Date: 2025-12-19

-- First, clean up any existing duplicates by consolidating them
CREATE TEMPORARY TABLE consolidated_items AS
SELECT 
    MIN(id) as id,
    request_id,
    product_id,
    product_name,
    SUM(requested_quantity) as requested_quantity,
    unit,
    GROUP_CONCAT(description SEPARATOR '; ') as description,
    created_at
FROM material_request_items
GROUP BY request_id, product_id;

-- Delete original items with duplicates
DELETE FROM material_request_items 
WHERE id NOT IN (SELECT id FROM consolidated_items);

-- Insert back consolidated data
INSERT INTO material_request_items (
    id, request_id, product_id, product_name, requested_quantity, unit, description, created_at
)
SELECT 
    id, request_id, product_id, product_name, requested_quantity, unit, description, created_at
FROM consolidated_items;

-- Add the UNIQUE constraint
ALTER TABLE material_request_items 
ADD CONSTRAINT unique_product_per_request 
UNIQUE (request_id, product_id);

-- Drop temporary table
DROP TEMPORARY TABLE consolidated_items;

-- Note: This migration will:
-- 1. Consolidate existing duplicate products by summing their quantities
-- 2. Merge descriptions using semicolon separators
-- 3. Add a UNIQUE constraint to prevent future duplicates
-- 4. Maintain the original created_at timestamps and use the minimum id for consolidated records

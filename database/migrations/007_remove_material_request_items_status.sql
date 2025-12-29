-- Migration: Remove status column from material_request_items table
-- Description: Remove redundant status column since request status is already tracked in material_requests table
-- Date: 2025-12-18

-- Remove the status column from material_request_items table
ALTER TABLE material_request_items DROP COLUMN IF EXISTS status;

-- Note: No indexes to remove since there were no indexes specifically on the status column 
-- in material_request_items table (unlike material_requests table which has idx_material_requests_status)

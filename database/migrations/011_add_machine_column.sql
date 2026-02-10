-- Migration: Add machine column to material_request_items
-- Description: Allow users to specify which machine will use each material
-- Date: 2026-02-10

ALTER TABLE material_request_items 
ADD COLUMN machine VARCHAR(100) DEFAULT NULL AFTER description;

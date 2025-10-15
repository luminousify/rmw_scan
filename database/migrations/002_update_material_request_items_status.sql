-- Migration: Update material_request_items status constraint to allow 'cancelled'
-- Date: 2025-01-13
-- Description: Add 'cancelled' as a valid status for material_request_items

-- For SQLite, we need to recreate the table with the new constraint
-- First, create a backup of existing data
CREATE TABLE IF NOT EXISTS material_request_items_backup AS 
SELECT * FROM material_request_items;

-- Drop the old table
DROP TABLE IF EXISTS material_request_items;

-- Recreate with updated constraint
CREATE TABLE IF NOT EXISTS material_request_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    request_id INTEGER NOT NULL,
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    requested_quantity INTEGER NOT NULL,
    unit VARCHAR(20) DEFAULT 'pcs',
    description TEXT,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'completed', 'cancelled')),
    approved_quantity INTEGER,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES material_requests(id) ON DELETE CASCADE
);

-- Restore data from backup
INSERT INTO material_request_items 
SELECT * FROM material_request_items_backup;

-- Drop backup table
DROP TABLE IF EXISTS material_request_items_backup;

-- Recreate index
CREATE INDEX IF NOT EXISTS idx_material_request_items_request ON material_request_items(request_id);

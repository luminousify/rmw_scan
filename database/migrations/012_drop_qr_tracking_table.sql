-- Migration: Drop unused qr_tracking table
-- Date: 2025-12-23
-- Reason: Table is not used by application (no write operations)
-- Impact: No data loss (table is empty/unused)

-- Drop indexes first (if they exist)
DROP INDEX IF EXISTS idx_qr_tracking_code ON qr_tracking;
DROP INDEX IF EXISTS idx_qr_tracking_status ON qr_tracking;

-- Drop table
DROP TABLE IF EXISTS qr_tracking;

-- Verify table was dropped
-- Run: SHOW TABLES; -- qr_tracking should not be in the list

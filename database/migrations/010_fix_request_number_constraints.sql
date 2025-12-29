-- Fix Request Number Constraints
-- Migration to ensure request numbers are always unique and properly indexed

-- Check if the unique constraint already exists
SET @exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'material_requests'
    AND CONSTRAINT_NAME = 'uq_material_requests_request_number'
);

-- Add unique constraint if it doesn't exist
SET @sql = IF(@exists = 0,
    'ALTER TABLE material_requests ADD CONSTRAINT uq_material_requests_request_number UNIQUE (request_number)',
    'SELECT ''Constraint already exists'' as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for better performance on request_number lookups
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'material_requests'
    AND INDEX_NAME = 'idx_material_requests_request_number'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_material_requests_request_number ON material_requests (request_number)',
    'SELECT ''Index already exists'' as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add composite index for date-based queries
SET @composite_index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'material_requests'
    AND INDEX_NAME = 'idx_material_requests_created_date'
);

SET @sql = IF(@composite_index_exists = 0,
    'CREATE INDEX idx_material_requests_created_date ON material_requests (created_at, request_number)',
    'SELECT ''Composite index already exists'' as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Migration completed successfully!' as status;

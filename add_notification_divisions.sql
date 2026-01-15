-- Migration: Add Notification Divisions Mapping Table
-- Description: Allow RMW users to receive notifications from multiple divisions
-- Version: 009

-- Step 1: Create the notification divisions mapping table
CREATE TABLE IF NOT EXISTS user_notification_divisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    division VARCHAR(50) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_division (user_id, division),
    INDEX idx_user_id (user_id),
    INDEX idx_division (division)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 2: Migrate existing division assignments from users table
INSERT INTO user_notification_divisions (user_id, division)
SELECT id, division
FROM users
WHERE department = 'rmw'
  AND division IS NOT NULL
  AND division != '';

-- Step 3: Example - Add multiple divisions to a specific RMW user
-- Uncomment and modify user_id as needed to test multi-division functionality
-- INSERT IGNORE INTO user_notification_divisions (user_id, division) VALUES
-- (5, 'Injection'),
-- (5, 'Assembly'),
-- (5, 'Quality Control');

-- Migration: Add Notification System for OM Messenger Integration
-- Version: 008
-- Description: Add om_username field to users table and notification_logs table

-- Add om_username field to users table for future flexibility
ALTER TABLE users 
ADD COLUMN om_username VARCHAR(50) NULL AFTER full_name,
ADD INDEX idx_om_username (om_username);

-- Create notification_logs table to track sent messages
CREATE TABLE IF NOT EXISTS notification_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NULL,
    notification_type ENUM('material_request_created', 'status_updated', 'other') DEFAULT 'material_request_created',
    target_username VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    api_response TEXT NULL,
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    error_message TEXT NULL,
    sent_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (request_id) REFERENCES material_requests(id) ON DELETE SET NULL,
    INDEX idx_notification_type (notification_type),
    INDEX idx_target_username (target_username),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Insert sample OM username for pratama (update as needed)
UPDATE users 
SET om_username = 'pratama' 
WHERE username = 'endang' OR full_name LIKE '%pratama%' OR id = 5;

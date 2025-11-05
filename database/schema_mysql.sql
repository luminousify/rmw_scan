-- RMW Database Schema
-- MySQL version matching SQLite schema

-- User Management
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department ENUM('production', 'rmw', 'admin') NOT NULL,
    division VARCHAR(50),
    full_name VARCHAR(100),
    email VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Material Requests
CREATE TABLE IF NOT EXISTS material_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(50) UNIQUE NOT NULL,
    production_user_id INT NOT NULL,
    request_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'diproses', 'completed', 'cancelled') DEFAULT 'pending',
    rmw_user_id INT,
    processed_date DATETIME,
    completed_date DATETIME,
    created_by VARCHAR(100),
    processed_by VARCHAR(100),
    completed_by VARCHAR(100),
    customer_reference VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (production_user_id) REFERENCES users(id),
    FOREIGN KEY (rmw_user_id) REFERENCES users(id)
);

-- Request Items
CREATE TABLE IF NOT EXISTS material_request_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    requested_quantity INT NOT NULL,
    unit VARCHAR(20) DEFAULT 'pcs',
    description TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    approved_quantity INT,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES material_requests(id) ON DELETE CASCADE
);

-- QR Code Tracking
CREATE TABLE IF NOT EXISTS qr_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    qr_code VARCHAR(100) UNIQUE NOT NULL,
    request_id INT NOT NULL,
    item_id INT NOT NULL,
    status ENUM('generated', 'scanned', 'completed') DEFAULT 'generated',
    generated_by INT NOT NULL,
    scanned_by INT,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    scanned_at DATETIME,
    FOREIGN KEY (request_id) REFERENCES material_requests(id),
    FOREIGN KEY (item_id) REFERENCES material_request_items(id),
    FOREIGN KEY (generated_by) REFERENCES users(id),
    FOREIGN KEY (scanned_by) REFERENCES users(id)
);

-- Activity Log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Products/Materials Master Data
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(50) UNIQUE NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    unit VARCHAR(20) DEFAULT 'pcs',
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Stock Detail Verification Table
CREATE TABLE IF NOT EXISTS StockDetailVer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Fields from second image
    StockDateNo VARCHAR(50),
    StockDate DATETIME,
    StockRefNo VARCHAR(50) UNIQUE NOT NULL,
    Ke_Dari VARCHAR(100),
    LPB_SJ_No VARCHAR(50),
    Mode VARCHAR(50),
    Product_ID VARCHAR(50),
    Unit VARCHAR(50),
    PONO VARCHAR(50),
    SuppID VARCHAR(50),
    SuppRef VARCHAR(50),
    SuppRefDate DATETIME,
    Customer VARCHAR(100),
    CustNoRef VARCHAR(50),
    CustRefDate DATETIME,
    Keterangan VARCHAR(255),
    RecdTotal DECIMAL(15,2),
    ShipTotal DECIMAL(15,2),
    CumInv DECIMAL(15,2),

    -- Fields from first image
    RecdNG DECIMAL(15,2),
    ShipNG DECIMAL(15,2),
    CumInvNG DECIMAL(15,2),
    NoSJSales VARCHAR(50),
    timetransfer DATETIME,
    status VARCHAR(50),
    Verifikasi TINYINT(1) DEFAULT 0,
    `copy` TINYINT(1) DEFAULT 0,
    Oldqty DECIMAL(15,2),
    Revby VARCHAR(100),
    KodekaitProduksi VARCHAR(100),
    transferby VARCHAR(100),
    Revisitime DATETIME,
    
    -- Indexes
    INDEX idx_NoSJSales (NoSJSales),
    INDEX idx_Product_ID (Product_ID),
    INDEX idx_StockDate (StockDate),
    INDEX idx_Status (status),
    INDEX idx_Verifikasi (Verifikasi)
);

-- Create indexes for better performance (MySQL 5.7 compatible)
-- Note: MySQL 5.7 doesn't support IF NOT EXISTS or IF EXISTS for INDEX operations
-- These indexes may already exist from the table definitions above

-- Additional indexes for better query performance
CREATE INDEX idx_material_requests_status ON material_requests(status);
CREATE INDEX idx_material_requests_user ON material_requests(production_user_id);
CREATE INDEX idx_material_request_items_request ON material_request_items(request_id);
CREATE INDEX idx_qr_tracking_code ON qr_tracking(qr_code);
CREATE INDEX idx_qr_tracking_status ON qr_tracking(status);
CREATE INDEX idx_activity_log_user ON activity_log(user_id);
CREATE INDEX idx_activity_log_date ON activity_log(created_at);
CREATE INDEX idx_stockdetailver_custnoref ON StockDetailVer(CustNoRef);
CREATE INDEX idx_users_department ON users(department);
CREATE INDEX idx_users_division ON users(division);

-- Insert test users for all roles
INSERT IGNORE INTO users (username, password, department, division, full_name) VALUES
('prod', 'prod123', 'production', 'Assembly', 'Production User'),
('prod1', 'prod123', 'production', 'Quality Control', 'Production Worker 1'),
('prod2', 'prod123', 'production', 'Packaging', 'Production Worker 2'),
('rmw', 'rmw123', 'rmw', 'Receiving', 'RMW Administrator'),
('rmw1', 'rmw123', 'rmw', 'Warehousing', 'RMW Staff 1'),
('rmw2', 'rmw123', 'rmw', 'Shipping', 'RMW Staff 2'),
('admin', 'admin123', 'admin', 'Management', 'System Administrator');

-- Insert sample products
INSERT IGNORE INTO products (product_id, product_name, category, unit, description) VALUES
('MAT001', 'Steel Rod 10mm', 'Raw Materials', 'pcs', 'Steel rod diameter 10mm, length 1m'),
('MAT002', 'Aluminum Sheet 2mm', 'Raw Materials', 'sheet', 'Aluminum sheet thickness 2mm'),
('MAT003', 'Rubber Gasket', 'Components', 'pcs', 'Rubber gasket for sealing'),
('MAT004', 'Copper Wire 2.5mm', 'Electrical', 'meter', 'Copper wire 2.5mm²'),
('MAT005', 'Plastic Housing', 'Components', 'pcs', 'Plastic housing for electronics');

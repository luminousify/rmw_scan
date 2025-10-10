-- MySQL version of the RMW database schema
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    department ENUM('production', 'rmw') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(50) NOT NULL UNIQUE,
    product_name VARCHAR(255) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS material_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(100) NOT NULL UNIQUE,
    production_user_id INT NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    notes TEXT,
    status ENUM('pending', 'diproses', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (production_user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS material_request_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    requested_quantity INT NOT NULL,
    unit VARCHAR(20) NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (request_id) REFERENCES material_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE IF NOT EXISTS qr_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    qr_code VARCHAR(255) NOT NULL UNIQUE,
    request_item_id INT NOT NULL,
    generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    scanned_by_user_id INT,
    scanned_date TIMESTAMP NULL,
    status ENUM('generated', 'scanned') DEFAULT 'generated',
    FOREIGN KEY (request_item_id) REFERENCES material_request_items(id) ON DELETE CASCADE,
    FOREIGN KEY (scanned_by_user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default users
INSERT IGNORE INTO users (username, password, full_name, department) VALUES
('tama', '1234', 'Tama Production', 'production'),
('rmw_admin', 'rmw123', 'RMW Administrator', 'rmw');

-- Insert default products
INSERT IGNORE INTO products (product_id, product_name, unit) VALUES
('MAT001', 'Steel Rod 10mm', 'pcs'),
('MAT002', 'Aluminum Sheet 2mm', 'sheet'),
('MAT003', 'Rubber Gasket', 'pcs'),
('MAT004', 'Copper Wire 2.5mm', 'meter'),
('MAT005', 'Plastic Housing', 'pcs');

-- Insert sample material request
INSERT IGNORE INTO material_requests (request_number, production_user_id, priority, notes, status) VALUES
('REQ-20250101-0001', 1, 'medium', 'Sample request for testing', 'pending');

-- Stock Detail Verification Table
CREATE TABLE IF NOT EXISTS StockDetailVer (
    -- Fields from second image
    StockDateNo VARCHAR(50),
    StockDate DATETIME,
    StockRefNo VARCHAR(50) PRIMARY KEY,
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
    Verifikasi BOOLEAN DEFAULT FALSE,
    copy BOOLEAN DEFAULT FALSE,
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

-- Insert sample material request items
INSERT IGNORE INTO material_request_items (request_id, product_id, product_name, requested_quantity, unit, description) VALUES
(1, 'MAT001', 'Steel Rod 10mm', 50, 'pcs', 'Steel rod for construction'),
(1, 'MAT002', 'Aluminum Sheet 2mm', 20, 'sheet', 'Aluminum sheet for fabrication'),
(1, 'MAT003', 'Rubber Gasket', 100, 'pcs', 'Rubber gaskets for sealing');

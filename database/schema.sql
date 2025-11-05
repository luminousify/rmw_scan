-- RMW Database Schema
-- SQLite compatible with MySQL migration support

-- User Management
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(20) NOT NULL CHECK (department IN ('production', 'rmw')),
    division VARCHAR(50),
    full_name VARCHAR(100),
    email VARCHAR(100),
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Material Requests
CREATE TABLE IF NOT EXISTS material_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    request_number VARCHAR(50) UNIQUE NOT NULL,
    production_user_id INTEGER NOT NULL,
    request_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'diproses', 'completed', 'cancelled')),
    rmw_user_id INTEGER,
    processed_date DATETIME,
    completed_date DATETIME,
    created_by VARCHAR(100),
    processed_by VARCHAR(100),
    completed_by VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (production_user_id) REFERENCES users(id),
    FOREIGN KEY (rmw_user_id) REFERENCES users(id)
);

-- Request Items
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

-- QR Code Tracking
CREATE TABLE IF NOT EXISTS qr_tracking (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    qr_code VARCHAR(100) UNIQUE NOT NULL,
    request_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    status VARCHAR(20) DEFAULT 'generated' CHECK (status IN ('generated', 'scanned', 'completed')),
    generated_by INTEGER NOT NULL,
    scanned_by INTEGER,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    scanned_at DATETIME,
    FOREIGN KEY (request_id) REFERENCES material_requests(id),
    FOREIGN KEY (item_id) REFERENCES material_request_items(id),
    FOREIGN KEY (generated_by) REFERENCES users(id),
    FOREIGN KEY (scanned_by) REFERENCES users(id)
);

-- Activity Log
CREATE TABLE IF NOT EXISTS activity_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INTEGER,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Products/Materials Master Data
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id VARCHAR(50) UNIQUE NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    unit VARCHAR(20) DEFAULT 'pcs',
    description TEXT,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert test users for all roles
INSERT OR IGNORE INTO users (username, password, department, division, full_name) VALUES
('prod', 'prod123', 'production', 'Assembly', 'Production User'),
('prod1', 'prod123', 'production', 'Quality Control', 'Production Worker 1'),
('prod2', 'prod123', 'production', 'Packaging', 'Production Worker 2'),
('rmw', 'rmw123', 'rmw', 'Receiving', 'RMW Administrator'),
('rmw1', 'rmw123', 'rmw', 'Warehousing', 'RMW Staff 1'),
('rmw2', 'rmw123', 'rmw', 'Shipping', 'RMW Staff 2'),
('admin', 'admin123', 'admin', 'Management', 'System Administrator');

-- Insert sample products
INSERT OR IGNORE INTO products (product_id, product_name, category, unit, description) VALUES
('MAT001', 'Steel Rod 10mm', 'Raw Materials', 'pcs', 'Steel rod diameter 10mm, length 1m'),
('MAT002', 'Aluminum Sheet 2mm', 'Raw Materials', 'sheet', 'Aluminum sheet thickness 2mm'),
('MAT003', 'Rubber Gasket', 'Components', 'pcs', 'Rubber gasket for sealing'),
('MAT004', 'Copper Wire 2.5mm', 'Electrical', 'meter', 'Copper wire 2.5mm²'),
('MAT005', 'Plastic Housing', 'Components', 'pcs', 'Plastic housing for electronics');

-- Stock Detail Verification Table
CREATE TABLE IF NOT EXISTS StockDetailVer (
    -- Auto-increment ID for SQLite compatibility
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    
    -- Fields from second image
    StockDateNo TEXT,
    StockDate DATETIME,
    StockRefNo TEXT UNIQUE NOT NULL,
    Ke_Dari TEXT,
    LPB_SJ_No TEXT,
    Mode TEXT,
    Product_ID TEXT,
    Unit TEXT,
    PONO TEXT,
    SuppID TEXT,
    SuppRef TEXT,
    SuppRefDate DATETIME,
    Customer TEXT,
    CustNoRef TEXT,
    CustRefDate DATETIME,
    Keterangan TEXT,
    RecdTotal REAL,
    ShipTotal REAL,
    CumInv REAL,

    -- Fields from first image
    RecdNG REAL,
    ShipNG REAL,
    CumInvNG REAL,
    NoSJSales TEXT,
    timetransfer DATETIME,
    status TEXT,
    Verifikasi INTEGER DEFAULT 0,
    copy INTEGER DEFAULT 0,
    Oldqty REAL,
    Revby TEXT,
    KodekaitProduksi TEXT,
    transferby TEXT,
    Revisitime DATETIME
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_material_requests_status ON material_requests(status);
CREATE INDEX IF NOT EXISTS idx_material_requests_user ON material_requests(production_user_id);
CREATE INDEX IF NOT EXISTS idx_material_request_items_request ON material_request_items(request_id);
CREATE INDEX IF NOT EXISTS idx_qr_tracking_code ON qr_tracking(qr_code);
CREATE INDEX IF NOT EXISTS idx_qr_tracking_status ON qr_tracking(status);
CREATE INDEX IF NOT EXISTS idx_activity_log_user ON activity_log(user_id);
CREATE INDEX IF NOT EXISTS idx_activity_log_date ON activity_log(created_at);

-- StockDetailVer indexes
CREATE INDEX IF NOT EXISTS idx_stockdetailver_nosjsales ON StockDetailVer(NoSJSales);
CREATE INDEX IF NOT EXISTS idx_stockdetailver_product_id ON StockDetailVer(Product_ID);
CREATE INDEX IF NOT EXISTS idx_stockdetailver_stockdate ON StockDetailVer(StockDate);
CREATE INDEX IF NOT EXISTS idx_stockdetailver_stockrefno ON StockDetailVer(StockRefNo);
CREATE INDEX IF NOT EXISTS idx_stockdetailver_status ON StockDetailVer(status);
CREATE INDEX IF NOT EXISTS idx_stockdetailver_verifikasi ON StockDetailVer(Verifikasi);

-- Users indexes
CREATE INDEX IF NOT EXISTS idx_users_department ON users(department);
CREATE INDEX IF NOT EXISTS idx_users_division ON users(division);

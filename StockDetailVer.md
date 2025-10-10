CREATE TABLE StockDetailVer (
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
    Verifikasi BOOLEAN,
    copy BOOLEAN,
    Oldqty DECIMAL(15,2),
    Revby VARCHAR(100),
    KodekaitProduksi VARCHAR(100),
    transferby VARCHAR(100),
    Revisitime DATETIME,

    -- Indexes (optional, for performance)
    INDEX idx_NoSJSales (NoSJSales),
    INDEX idx_Product_ID (Product_ID),
    INDEX idx_StockDate (StockDate)
);

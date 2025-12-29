-- 006_change_quantities_to_decimal.sql
-- Migrate request quantities from INT to DECIMAL(15,2) to match StockDetailVer precision.
-- Safe for existing integer data (INT values are valid DECIMAL values).

ALTER TABLE material_request_items
  MODIFY requested_quantity DECIMAL(15,2) NOT NULL,
  MODIFY approved_quantity  DECIMAL(15,2) NULL;



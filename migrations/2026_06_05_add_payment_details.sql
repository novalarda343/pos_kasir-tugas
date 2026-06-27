USE pos_kasir_tailadmin;

ALTER TABLE sales
  ADD COLUMN discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER subtotal,
  ADD COLUMN discount_amount DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER discount_percent,
  ADD COLUMN tax_enabled TINYINT(1) NOT NULL DEFAULT 1 AFTER discount_amount,
  ADD COLUMN tax_amount DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER tax_enabled,
  ADD COLUMN total_amount DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER tax_amount,
  ADD COLUMN payment_method VARCHAR(30) NOT NULL DEFAULT 'Tunai' AFTER total_amount;

UPDATE sales
SET total_amount = subtotal
WHERE total_amount = 0;

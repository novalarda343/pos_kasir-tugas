CREATE DATABASE IF NOT EXISTS pos_kasir_tailadmin
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE pos_kasir_tailadmin;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NULL,
  sku VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  image_path VARCHAR(255) NULL,
  purchase_price DECIMAL(14,2) NOT NULL DEFAULT 0,
  selling_price DECIMAL(14,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  min_stock INT NOT NULL DEFAULT 5,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sales (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sale_number VARCHAR(40) NOT NULL UNIQUE,
  customer_name VARCHAR(120) NOT NULL DEFAULT 'Umum',
  subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  discount_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  tax_enabled TINYINT(1) NOT NULL DEFAULT 1,
  tax_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  payment_method VARCHAR(30) NOT NULL DEFAULT 'Tunai',
  paid_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  change_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sale_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sale_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(14,2) NOT NULL,
  total DECIMAL(14,2) NOT NULL,
  CONSTRAINT fk_sale_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
  CONSTRAINT fk_sale_items_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS stock_movements (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  type ENUM('in','out') NOT NULL,
  quantity INT NOT NULL,
  note VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_stock_movements_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (id, name) VALUES
  (1, 'Minuman'),
  (2, 'Makanan'),
  (3, 'Kebutuhan Harian');

INSERT IGNORE INTO products (id, category_id, sku, name, purchase_price, selling_price, stock, min_stock) VALUES
  (1, 1, 'MNM-001', 'Air Mineral 600ml', 2500, 4000, 48, 10),
  (2, 1, 'MNM-002', 'Teh Botol', 3500, 6000, 36, 8),
  (3, 2, 'MKN-001', 'Roti Coklat', 4500, 7500, 25, 6),
  (4, 3, 'KHR-001', 'Tisu Wajah', 9000, 13500, 18, 5);

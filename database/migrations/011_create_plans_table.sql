CREATE TABLE IF NOT EXISTS plans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product VARCHAR(100) NOT NULL,
    plan VARCHAR(100) NOT NULL,
    status ENUM('available','unavailable') NOT NULL DEFAULT 'available',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    KEY plans_product_index (product),
    KEY plans_status_index (status),
    KEY plans_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

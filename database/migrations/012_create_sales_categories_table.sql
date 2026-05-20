CREATE TABLE IF NOT EXISTS sales_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sales_category VARCHAR(150) NOT NULL,
    sales_manager VARCHAR(150) NOT NULL,
    type ENUM('partner','inhouse') NOT NULL,
    tl_status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    validation ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    KEY sales_categories_name_index (sales_category),
    KEY sales_categories_manager_index (sales_manager),
    KEY sales_categories_type_index (type),
    KEY sales_categories_validation_index (validation),
    KEY sales_categories_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

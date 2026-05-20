CREATE TABLE IF NOT EXISTS sales_agents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sales_category_id INT UNSIGNED NOT NULL,
    agent_name VARCHAR(150) NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    validation ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    KEY sales_agents_category_index (sales_category_id),
    KEY sales_agents_status_index (status),
    KEY sales_agents_validation_index (validation),
    KEY sales_agents_created_at_index (created_at),
    CONSTRAINT fk_sales_agents_category FOREIGN KEY (sales_category_id) REFERENCES sales_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

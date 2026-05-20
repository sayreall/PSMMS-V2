CREATE TABLE IF NOT EXISTS agent_codes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sales_category_id INT UNSIGNED NOT NULL,
    sales_agent_id INT UNSIGNED NOT NULL,
    agent_code VARCHAR(50) NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    validation ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY agent_codes_code_unique (agent_code),
    KEY agent_codes_category_index (sales_category_id),
    KEY agent_codes_agent_index (sales_agent_id),
    KEY agent_codes_status_index (status),
    KEY agent_codes_validation_index (validation),
    KEY agent_codes_created_at_index (created_at),
    CONSTRAINT fk_agent_codes_category FOREIGN KEY (sales_category_id) REFERENCES sales_categories(id) ON DELETE CASCADE,
    CONSTRAINT fk_agent_codes_agent FOREIGN KEY (sales_agent_id) REFERENCES sales_agents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

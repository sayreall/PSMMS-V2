CREATE TABLE IF NOT EXISTS managers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    manager_name VARCHAR(150) NOT NULL,
    position VARCHAR(100) NOT NULL,
    contact_no VARCHAR(30) DEFAULT NULL,
    company_email VARCHAR(150) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    status ENUM('pending','active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY managers_company_email_unique (company_email),
    UNIQUE KEY managers_email_unique (email),
    KEY managers_user_id_index (user_id),
    KEY managers_position_index (position),
    KEY managers_status_index (status),
    KEY managers_created_at_index (created_at),
    CONSTRAINT fk_managers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS installer_tech_team_areas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    area VARCHAR(150) NOT NULL,
    team VARCHAR(100) NOT NULL,
    validation_status ENUM('approved','pending','declined') NOT NULL DEFAULT 'pending',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX area_index (area),
    INDEX team_index (team),
    INDEX validation_status_index (validation_status),
    INDEX status_index (status),
    INDEX created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS regions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    region_name VARCHAR(120) NOT NULL,
    region_code VARCHAR(40) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_region_name (region_name),
    UNIQUE KEY uniq_region_code (region_code),
    INDEX idx_region_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS provinces (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    region_id BIGINT UNSIGNED NOT NULL,
    province_name VARCHAR(120) NOT NULL,
    province_code VARCHAR(40) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_region_province (region_id, province_name),
    UNIQUE KEY uniq_province_code (province_code),
    INDEX idx_province_status (status),
    INDEX idx_province_region (region_id),
    CONSTRAINT fk_provinces_region
        FOREIGN KEY (region_id) REFERENCES regions(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS municipalities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    province_id BIGINT UNSIGNED NOT NULL,
    municipality_name VARCHAR(120) NOT NULL,
    municipality_code VARCHAR(40) DEFAULT NULL,
    zip_code VARCHAR(20) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_province_municipality (province_id, municipality_name),
    UNIQUE KEY uniq_municipality_code (municipality_code),
    INDEX idx_municipality_status (status),
    INDEX idx_municipality_province (province_id),
    CONSTRAINT fk_municipalities_province
        FOREIGN KEY (province_id) REFERENCES provinces(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

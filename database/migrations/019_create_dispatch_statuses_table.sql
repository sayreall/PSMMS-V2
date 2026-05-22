CREATE TABLE IF NOT EXISTS dispatch_statuses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dispatch_status VARCHAR(150) NOT NULL,
    color CHAR(7) NOT NULL DEFAULT '#ffffff',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY dispatch_statuses_name_unique (dispatch_status),
    KEY dispatch_statuses_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

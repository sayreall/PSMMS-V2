CREATE TABLE IF NOT EXISTS dispatch_remarks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dispatch_remarks VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY dispatch_remarks_name_unique (dispatch_remarks),
    KEY dispatch_remarks_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE inhouse_sales
    ADD COLUMN IF NOT EXISTS inhouse_id INT UNSIGNED NULL AFTER id,
    ADD COLUMN IF NOT EXISTS create_at DATETIME NULL AFTER inhouse_id,
    ADD COLUMN IF NOT EXISTS user_type VARCHAR(50) NULL AFTER create_at,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS contact VARCHAR(30) NULL AFTER last_name,
    ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL AFTER email,
    ADD COLUMN IF NOT EXISTS photos VARCHAR(255) NULL AFTER password,
    ADD COLUMN IF NOT EXISTS address VARCHAR(255) NULL AFTER photos;

ALTER TABLE inhouse_sales
    MODIFY COLUMN sales_manager VARCHAR(150) NOT NULL AFTER user_type,
    MODIFY COLUMN first_name VARCHAR(100) NOT NULL AFTER sales_manager,
    MODIFY COLUMN middle_name VARCHAR(100) NULL AFTER first_name,
    MODIFY COLUMN last_name VARCHAR(100) NOT NULL AFTER middle_name,
    MODIFY COLUMN contact VARCHAR(30) NULL AFTER last_name,
    MODIFY COLUMN email VARCHAR(150) NOT NULL AFTER contact,
    MODIFY COLUMN password VARCHAR(255) NULL AFTER email,
    MODIFY COLUMN photos VARCHAR(255) NULL AFTER password,
    MODIFY COLUMN address VARCHAR(255) NULL AFTER photos,
    MODIFY COLUMN sales_category VARCHAR(100) NOT NULL AFTER address,
    MODIFY COLUMN status ENUM('pending','active','inactive') NOT NULL DEFAULT 'pending' AFTER sales_category;

UPDATE inhouse_sales ih
LEFT JOIN users u ON u.id = ih.user_id
SET
    ih.inhouse_id = COALESCE(ih.inhouse_id, ih.id),
    ih.create_at = COALESCE(ih.create_at, ih.created_at),
    ih.user_type = COALESCE(ih.user_type, u.role, 'inhouse_sales'),
    ih.middle_name = COALESCE(ih.middle_name, u.middle_name),
    ih.contact = COALESCE(ih.contact, u.contact_no),
    ih.password = COALESCE(ih.password, u.password);

SET @inhouse_contact_no_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inhouse_sales'
      AND COLUMN_NAME = 'contact_no'
);

SET @copy_inhouse_contact_no_sql := IF(
    @inhouse_contact_no_exists > 0,
    'UPDATE inhouse_sales SET contact = COALESCE(contact, contact_no)',
    'SELECT 1'
);

PREPARE copy_inhouse_contact_no_stmt FROM @copy_inhouse_contact_no_sql;
EXECUTE copy_inhouse_contact_no_stmt;
DEALLOCATE PREPARE copy_inhouse_contact_no_stmt;

SET @inhouse_profile_picture_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inhouse_sales'
      AND COLUMN_NAME = 'profile_picture'
);

SET @copy_inhouse_profile_picture_sql := IF(
    @inhouse_profile_picture_exists > 0,
    'UPDATE inhouse_sales SET photos = COALESCE(photos, profile_picture)',
    'SELECT 1'
);

PREPARE copy_inhouse_profile_picture_stmt FROM @copy_inhouse_profile_picture_sql;
EXECUTE copy_inhouse_profile_picture_stmt;
DEALLOCATE PREPARE copy_inhouse_profile_picture_stmt;

ALTER TABLE inhouse_sales
    DROP COLUMN IF EXISTS employee_id,
    DROP COLUMN IF EXISTS contact_no,
    DROP COLUMN IF EXISTS profile_picture;

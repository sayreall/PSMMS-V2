ALTER TABLE managers
    ADD COLUMN IF NOT EXISTS manager_id INT UNSIGNED NULL AFTER id,
    ADD COLUMN IF NOT EXISTS create_at DATETIME NULL AFTER manager_id,
    ADD COLUMN IF NOT EXISTS user_type VARCHAR(50) NULL AFTER create_at,
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) NULL AFTER position,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) NULL AFTER middle_name,
    ADD COLUMN IF NOT EXISTS sales_manager VARCHAR(150) NULL AFTER last_name,
    ADD COLUMN IF NOT EXISTS contact VARCHAR(30) NULL AFTER contact_no,
    ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL AFTER company_email,
    ADD COLUMN IF NOT EXISTS photos VARCHAR(255) NULL AFTER email;

UPDATE managers m
LEFT JOIN users u ON u.id = m.user_id
SET
    m.manager_id = COALESCE(m.manager_id, m.id),
    m.create_at = COALESCE(m.create_at, m.created_at),
    m.user_type = COALESCE(m.user_type, u.role, 'asm_manager'),
    m.first_name = COALESCE(m.first_name, u.first_name),
    m.middle_name = COALESCE(m.middle_name, u.middle_name),
    m.last_name = COALESCE(m.last_name, u.last_name),
    m.sales_manager = COALESCE(m.sales_manager, m.manager_name, u.name),
    m.contact = COALESCE(m.contact, m.contact_no, u.contact_no),
    m.password = COALESCE(m.password, u.password);

SET @manager_profile_picture_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'managers'
      AND COLUMN_NAME = 'profile_picture'
);

SET @copy_manager_profile_picture_sql := IF(
    @manager_profile_picture_exists > 0,
    'UPDATE managers SET photos = COALESCE(photos, profile_picture)',
    'SELECT 1'
);

PREPARE copy_manager_profile_picture_stmt FROM @copy_manager_profile_picture_sql;
EXECUTE copy_manager_profile_picture_stmt;
DEALLOCATE PREPARE copy_manager_profile_picture_stmt;

ALTER TABLE managers
    DROP COLUMN IF EXISTS profile_picture;

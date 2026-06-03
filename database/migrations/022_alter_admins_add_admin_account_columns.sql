ALTER TABLE admins
    ADD COLUMN IF NOT EXISTS admin_id INT UNSIGNED NULL AFTER id,
    ADD COLUMN IF NOT EXISTS create_at DATETIME NULL AFTER admin_id,
    ADD COLUMN IF NOT EXISTS user_type VARCHAR(50) NULL AFTER create_at,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS username VARCHAR(100) NULL AFTER last_name,
    ADD COLUMN IF NOT EXISTS contact VARCHAR(30) NULL AFTER contact_no,
    ADD COLUMN IF NOT EXISTS address VARCHAR(255) NULL AFTER contact,
    ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL AFTER company_email,
    ADD COLUMN IF NOT EXISTS photos VARCHAR(255) NULL AFTER email;

UPDATE admins a
LEFT JOIN users u ON u.id = a.user_id
SET
    a.admin_id = COALESCE(a.admin_id, a.id),
    a.create_at = COALESCE(a.create_at, a.created_at),
    a.user_type = COALESCE(a.user_type, u.role, 'admin'),
    a.middle_name = COALESCE(a.middle_name, u.middle_name),
    a.username = COALESCE(a.username, u.name),
    a.contact = COALESCE(a.contact, a.contact_no),
    a.password = COALESCE(a.password, u.password);

SET @admin_profile_picture_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'admins'
      AND COLUMN_NAME = 'profile_picture'
);

SET @copy_admin_profile_picture_sql := IF(
    @admin_profile_picture_exists > 0,
    'UPDATE admins SET photos = COALESCE(photos, profile_picture)',
    'SELECT 1'
);

PREPARE copy_admin_profile_picture_stmt FROM @copy_admin_profile_picture_sql;
EXECUTE copy_admin_profile_picture_stmt;
DEALLOCATE PREPARE copy_admin_profile_picture_stmt;

ALTER TABLE admins
    DROP COLUMN IF EXISTS profile_picture;

ALTER TABLE msa_partners
    ADD COLUMN IF NOT EXISTS partners_id INT UNSIGNED NULL AFTER id,
    ADD COLUMN IF NOT EXISTS create_at DATETIME NULL AFTER partners_id,
    ADD COLUMN IF NOT EXISTS user_type VARCHAR(50) NULL AFTER create_at,
    ADD COLUMN IF NOT EXISTS sales_manager VARCHAR(150) NULL AFTER user_type,
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) NULL AFTER company_name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) NULL AFTER middle_name,
    ADD COLUMN IF NOT EXISTS contact VARCHAR(30) NULL AFTER last_name,
    ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL AFTER email,
    ADD COLUMN IF NOT EXISTS photos VARCHAR(255) NULL AFTER password,
    ADD COLUMN IF NOT EXISTS area_type VARCHAR(30) NULL AFTER photos,
    ADD COLUMN IF NOT EXISTS sales_category VARCHAR(100) NULL AFTER address;

ALTER TABLE msa_partners
    MODIFY COLUMN sales_manager VARCHAR(150) NULL AFTER user_type,
    MODIFY COLUMN company_name VARCHAR(150) NOT NULL AFTER sales_manager,
    MODIFY COLUMN first_name VARCHAR(100) NULL AFTER company_name,
    MODIFY COLUMN middle_name VARCHAR(100) NULL AFTER first_name,
    MODIFY COLUMN last_name VARCHAR(100) NULL AFTER middle_name,
    MODIFY COLUMN contact VARCHAR(30) NULL AFTER last_name,
    MODIFY COLUMN email VARCHAR(150) NOT NULL AFTER contact,
    MODIFY COLUMN password VARCHAR(255) NULL AFTER email,
    MODIFY COLUMN photos VARCHAR(255) NULL AFTER password,
    MODIFY COLUMN area_type VARCHAR(30) NULL AFTER photos,
    MODIFY COLUMN address VARCHAR(255) NOT NULL AFTER area_type,
    MODIFY COLUMN sales_category VARCHAR(100) NULL AFTER address,
    MODIFY COLUMN status ENUM('pending','active','inactive') NOT NULL DEFAULT 'pending' AFTER sales_category;

UPDATE msa_partners mp
LEFT JOIN users u ON u.id = mp.user_id
SET
    mp.partners_id = COALESCE(mp.partners_id, mp.id),
    mp.create_at = COALESCE(mp.create_at, mp.created_at),
    mp.user_type = COALESCE(mp.user_type, u.role, 'msa_partners'),
    mp.first_name = COALESCE(mp.first_name, u.first_name),
    mp.middle_name = COALESCE(mp.middle_name, u.middle_name),
    mp.last_name = COALESCE(mp.last_name, u.last_name),
    mp.contact = COALESCE(mp.contact, u.contact_no),
    mp.password = COALESCE(mp.password, u.password);

SET @msa_contact_no_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'msa_partners'
      AND COLUMN_NAME = 'contact_no'
);

SET @copy_msa_contact_no_sql := IF(
    @msa_contact_no_exists > 0,
    'UPDATE msa_partners SET contact = COALESCE(contact, contact_no)',
    'SELECT 1'
);

PREPARE copy_msa_contact_no_stmt FROM @copy_msa_contact_no_sql;
EXECUTE copy_msa_contact_no_stmt;
DEALLOCATE PREPARE copy_msa_contact_no_stmt;

SET @msa_type_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'msa_partners'
      AND COLUMN_NAME = 'msa_type'
);

SET @copy_msa_type_sql := IF(
    @msa_type_exists > 0,
    'UPDATE msa_partners SET area_type = COALESCE(area_type, msa_type)',
    'SELECT 1'
);

PREPARE copy_msa_type_stmt FROM @copy_msa_type_sql;
EXECUTE copy_msa_type_stmt;
DEALLOCATE PREPARE copy_msa_type_stmt;

SET @msa_profile_picture_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'msa_partners'
      AND COLUMN_NAME = 'profile_picture'
);

SET @copy_msa_profile_picture_sql := IF(
    @msa_profile_picture_exists > 0,
    'UPDATE msa_partners SET photos = COALESCE(photos, profile_picture)',
    'SELECT 1'
);

PREPARE copy_msa_profile_picture_stmt FROM @copy_msa_profile_picture_sql;
EXECUTE copy_msa_profile_picture_stmt;
DEALLOCATE PREPARE copy_msa_profile_picture_stmt;

ALTER TABLE msa_partners
    DROP COLUMN IF EXISTS username,
    DROP COLUMN IF EXISTS contact_no,
    DROP COLUMN IF EXISTS installer,
    DROP COLUMN IF EXISTS msa_type,
    DROP COLUMN IF EXISTS profile_picture;

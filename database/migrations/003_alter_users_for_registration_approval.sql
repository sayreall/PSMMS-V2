ALTER TABLE users
    ADD COLUMN IF NOT EXISTS company_email VARCHAR(150) NULL AFTER email;

ALTER TABLE users
    MODIFY COLUMN role ENUM('accounting','asm_manager','admin','head_manager','super_admin') NOT NULL DEFAULT 'accounting';

ALTER TABLE users
    MODIFY COLUMN status ENUM('pending','active','inactive') NOT NULL DEFAULT 'active';

CREATE UNIQUE INDEX users_company_email_unique ON users (company_email);

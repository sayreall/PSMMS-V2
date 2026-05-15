ALTER TABLE users
    ADD COLUMN IF NOT EXISTS contact_no VARCHAR(30) NULL AFTER company_email;

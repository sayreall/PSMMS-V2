ALTER TABLE users
    MODIFY COLUMN role ENUM('accounting','asm_manager','admin','head_manager','super_admin','msa_partners','inhouse_sales','sme_sales') NOT NULL DEFAULT 'accounting';

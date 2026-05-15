ALTER TABLE users
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) NULL AFTER name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) NULL AFTER middle_name;

UPDATE users
SET
    first_name = COALESCE(NULLIF(first_name, ''), name),
    last_name = COALESCE(NULLIF(last_name, ''), ''),
    middle_name = COALESCE(middle_name, NULL)
WHERE first_name IS NULL;

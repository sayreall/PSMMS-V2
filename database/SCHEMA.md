# Database Schema

This document describes the current MySQL schema used by PSMMS Dashboard.

## Migration order
1. `001_create_users_and_activity_logs.sql`
2. `002_create_password_resets.sql`
3. `003_alter_users_for_registration_approval.sql`
4. `004_add_name_parts_to_users.sql`
5. `005_create_managers_table.sql`
6. `006_create_admins_table.sql`
7. `007_add_contact_no_to_users.sql`
8. `008_create_inhouse_sales_table.sql`
9. `009_create_msa_partners_table.sql`
10. `010_alter_users_roles_for_partner_roles.sql`

## Tables

### `users`
Purpose: stores application user accounts and RBAC role/state.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `name` `VARCHAR(100)` not null
- `first_name` `VARCHAR(100)` nullable
- `middle_name` `VARCHAR(100)` nullable
- `last_name` `VARCHAR(100)` nullable
- `email` `VARCHAR(150)` not null, unique
- `company_email` `VARCHAR(150)` nullable, unique
- `password` `VARCHAR(255)` not null (hashed)
- `role` `ENUM('accounting','asm_manager','admin','head_manager','super_admin','msa_partners','inhouse_sales','sme_sales')` not null default `accounting`
- `status` `ENUM('pending','active','inactive')` not null default `active`
- `avatar` `VARCHAR(255)` nullable
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- unique `users_email_unique (email)`
- unique `users_company_email_unique (company_email)`
- `users_role_index (role)`
- `users_status_index (status)`
- `users_created_at_index (created_at)`

### `activity_logs`
Purpose: audit trail of user actions (login/logout/register/etc.).

Columns:
- `id` `BIGINT UNSIGNED` PK auto increment
- `user_id` `INT UNSIGNED` not null, FK to `users.id`
- `action` `VARCHAR(50)` not null
- `description` `VARCHAR(255)` nullable
- `ip_address` `VARCHAR(45)` nullable
- `user_agent` `VARCHAR(255)` nullable
- `created_at` `DATETIME` not null

Indexes:
- `activity_user_index (user_id)`
- `activity_action_index (action)`
- `activity_created_at_index (created_at)`

Foreign keys:
- `fk_activity_user`: `activity_logs.user_id` -> `users.id` (`ON DELETE CASCADE`)

### `password_resets`
Purpose: stores password reset tokens with expiry.

Columns:
- `id` `BIGINT UNSIGNED` PK auto increment
- `user_id` `INT UNSIGNED` not null, FK to `users.id`
- `token` `VARCHAR(64)` not null
- `expires_at` `DATETIME` not null
- `created_at` `DATETIME` not null

Indexes:
- `password_resets_token_index (token)`
- `password_resets_user_index (user_id)`

Foreign keys:
- `fk_password_reset_user`: `password_resets.user_id` -> `users.id` (`ON DELETE CASCADE`)

### `managers`
Purpose: stores manager profile records used by the Managers module.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `manager_name` `VARCHAR(150)` not null
- `position` `VARCHAR(100)` not null
- `contact_no` `VARCHAR(30)` nullable
- `company_email` `VARCHAR(150)` nullable, unique
- `email` `VARCHAR(150)` nullable, unique
- `profile_picture` `VARCHAR(255)` nullable
- `status` `ENUM('pending','active','inactive')` not null default `active`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- unique `managers_company_email_unique (company_email)`
- unique `managers_email_unique (email)`
- `managers_user_id_index (user_id)`
- `managers_position_index (position)`
- `managers_status_index (status)`
- `managers_created_at_index (created_at)`

Foreign keys:
- `fk_managers_user`: `managers.user_id` -> `users.id` (`ON DELETE SET NULL`)

### `admins`
Purpose: stores admin profile records used by the Admin module.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `first_name` `VARCHAR(100)` not null
- `last_name` `VARCHAR(100)` not null
- `position` `VARCHAR(100)` not null
- `area` `VARCHAR(150)` nullable
- `contact_no` `VARCHAR(30)` nullable
- `employee_id` `VARCHAR(50)` not null, unique
- `department` `VARCHAR(100)` not null
- `company_email` `VARCHAR(150)` nullable, unique
- `email` `VARCHAR(150)` nullable, unique
- `profile_picture` `VARCHAR(255)` nullable
- `status` `ENUM('pending','active','inactive')` not null default `active`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- unique `admins_employee_id_unique (employee_id)`
- unique `admins_company_email_unique (company_email)`
- unique `admins_email_unique (email)`
- `admins_user_id_index (user_id)`
- `admins_department_index (department)`
- `admins_status_index (status)`
- `admins_created_at_index (created_at)`

Foreign keys:
- `fk_admins_user`: `admins.user_id` -> `users.id` (`ON DELETE SET NULL`)

### `inhouse_sales`
Purpose: stores in-house sales profile records.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `sales_manager` `VARCHAR(150)` not null
- `sales_category` `VARCHAR(100)` not null
- `first_name` `VARCHAR(100)` not null
- `last_name` `VARCHAR(100)` not null
- `employee_id` `VARCHAR(50)` not null, unique
- `contact_no` `VARCHAR(30)` not null
- `email` `VARCHAR(150)` not null, unique
- `profile_picture` `VARCHAR(255)` nullable
- `status` `ENUM('pending','active','inactive')` not null default `pending`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- unique `inhouse_employee_id_unique (employee_id)`
- unique `inhouse_email_unique (email)`
- `inhouse_user_id_index (user_id)`
- `inhouse_status_index (status)`
- `inhouse_created_at_index (created_at)`

Foreign keys:
- `fk_inhouse_user`: `inhouse_sales.user_id` -> `users.id` (`ON DELETE SET NULL`)

### `msa_partners`
Purpose: stores MSA partner profile records.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `company_name` `VARCHAR(150)` not null
- `username` `VARCHAR(100)` not null, unique
- `contact_no` `VARCHAR(30)` not null
- `address` `VARCHAR(255)` not null
- `installer` `VARCHAR(150)` not null
- `msa_type` `VARCHAR(30)` not null
- `email` `VARCHAR(150)` not null, unique
- `profile_picture` `VARCHAR(255)` nullable
- `status` `ENUM('pending','active','inactive')` not null default `pending`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- unique `msa_username_unique (username)`
- unique `msa_email_unique (email)`
- `msa_user_id_index (user_id)`
- `msa_type_index (msa_type)`
- `msa_status_index (status)`
- `msa_created_at_index (created_at)`

Foreign keys:
- `fk_msa_user`: `msa_partners.user_id` -> `users.id` (`ON DELETE SET NULL`)

## Notes
- Charset/collation: `utf8mb4` / `utf8mb4_unicode_ci`.
- All migrations are idempotent via `CREATE TABLE IF NOT EXISTS`.

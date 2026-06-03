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
11. `011_create_plans_table.sql`
12. `012_create_sales_categories_table.sql`
13. `013_create_sales_agents_table.sql`
14. `014_create_agent_codes_table.sql`
15. `015_create_installer_tech_data_table.sql`
16. `016_create_installer_tech_team_areas_table.sql`
17. `017_create_asm_names_table.sql`
18. `018_create_asm_per_areas_table.sql`
19. `019_create_dispatch_statuses_table.sql`
20. `020_create_dispatch_remarks_table.sql`
21. `021_create_address_tables.sql`
22. `022_alter_admins_add_admin_account_columns.sql`
23. `023_alter_managers_add_manager_account_columns.sql`
24. `024_alter_inhouse_sales_add_inhouse_account_columns.sql`
25. `025_alter_msa_partners_add_partners_account_columns.sql`

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
- `manager_id` `INT UNSIGNED` nullable
- `create_at` `DATETIME` nullable
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `user_type` `VARCHAR(50)` nullable
- `manager_name` `VARCHAR(150)` not null
- `position` `VARCHAR(100)` not null
- `first_name` `VARCHAR(100)` nullable
- `middle_name` `VARCHAR(100)` nullable
- `last_name` `VARCHAR(100)` nullable
- `sales_manager` `VARCHAR(150)` nullable
- `contact_no` `VARCHAR(30)` nullable
- `contact` `VARCHAR(30)` nullable
- `company_email` `VARCHAR(150)` nullable, unique
- `password` `VARCHAR(255)` nullable
- `email` `VARCHAR(150)` nullable, unique
- `photos` `VARCHAR(255)` nullable
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
- `admin_id` `INT UNSIGNED` nullable
- `create_at` `DATETIME` nullable
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `user_type` `VARCHAR(50)` nullable
- `first_name` `VARCHAR(100)` not null
- `middle_name` `VARCHAR(100)` nullable
- `last_name` `VARCHAR(100)` not null
- `username` `VARCHAR(100)` nullable
- `position` `VARCHAR(100)` not null
- `area` `VARCHAR(150)` nullable
- `contact_no` `VARCHAR(30)` nullable
- `contact` `VARCHAR(30)` nullable
- `address` `VARCHAR(255)` nullable
- `employee_id` `VARCHAR(50)` not null, unique
- `department` `VARCHAR(100)` not null
- `company_email` `VARCHAR(150)` nullable, unique
- `password` `VARCHAR(255)` nullable
- `email` `VARCHAR(150)` nullable, unique
- `photos` `VARCHAR(255)` nullable
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
- `inhouse_id` `INT UNSIGNED` nullable
- `create_at` `DATETIME` nullable
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `user_type` `VARCHAR(50)` nullable
- `sales_manager` `VARCHAR(150)` not null
- `first_name` `VARCHAR(100)` not null
- `middle_name` `VARCHAR(100)` nullable
- `last_name` `VARCHAR(100)` not null
- `contact` `VARCHAR(30)` nullable
- `email` `VARCHAR(150)` not null, unique
- `password` `VARCHAR(255)` nullable
- `photos` `VARCHAR(255)` nullable
- `address` `VARCHAR(255)` nullable
- `sales_category` `VARCHAR(100)` not null
- `status` `ENUM('pending','active','inactive')` not null default `pending`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
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
- `partners_id` `INT UNSIGNED` nullable
- `create_at` `DATETIME` nullable
- `user_id` `INT UNSIGNED` nullable, FK to `users.id`
- `user_type` `VARCHAR(50)` nullable
- `sales_manager` `VARCHAR(150)` nullable
- `company_name` `VARCHAR(150)` not null
- `first_name` `VARCHAR(100)` nullable
- `middle_name` `VARCHAR(100)` nullable
- `last_name` `VARCHAR(100)` nullable
- `contact` `VARCHAR(30)` nullable
- `email` `VARCHAR(150)` not null, unique
- `password` `VARCHAR(255)` nullable
- `photos` `VARCHAR(255)` nullable
- `area_type` `VARCHAR(30)` not null
- `address` `VARCHAR(255)` not null
- `sales_category` `VARCHAR(100)` nullable
- `status` `ENUM('pending','active','inactive')` not null default `pending`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- unique `msa_email_unique (email)`
- `msa_user_id_index (user_id)`
- `msa_area_type_index (area_type)`
- `msa_status_index (status)`
- `msa_created_at_index (created_at)`

Foreign keys:
- `fk_msa_user`: `msa_partners.user_id` -> `users.id` (`ON DELETE SET NULL`)

### `plans`
Purpose: stores sales product plans and availability.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `product` `VARCHAR(100)` not null
- `plan` `VARCHAR(100)` not null
- `status` `ENUM('available','unavailable')` not null default `available`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- `plans_product_index (product)`
- `plans_status_index (status)`
- `plans_created_at_index (created_at)`

### `sales_categories`
Purpose: stores sales categories for partners and in-house.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `sales_category` `VARCHAR(150)` not null
- `sales_manager` `VARCHAR(150)` not null
- `type` `ENUM('partner','inhouse')` not null
- `tl_status` `ENUM('active','inactive')` not null default `active`
- `validation` `ENUM('approved','pending','declined')` not null default `pending`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- `sales_categories_name_index (sales_category)`
- `sales_categories_manager_index (sales_manager)`
- `sales_categories_type_index (type)`
- `sales_categories_validation_index (validation)`
- `sales_categories_created_at_index (created_at)`

### `sales_agents`
Purpose: stores sales agents under a sales category.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `sales_category_id` `INT UNSIGNED` not null, FK to `sales_categories.id`
- `agent_name` `VARCHAR(150)` not null
- `status` `ENUM('active','inactive')` not null default `active`
- `validation` `ENUM('approved','pending','declined')` not null default `pending`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- `sales_agents_category_index (sales_category_id)`
- `sales_agents_status_index (status)`
- `sales_agents_validation_index (validation)`
- `sales_agents_created_at_index (created_at)`

Foreign keys:
- `fk_sales_agents_category`: `sales_agents.sales_category_id` -> `sales_categories.id` (`ON DELETE CASCADE`)

### `agent_codes`
Purpose: stores agent codes for sales agents.

Columns:
- `id` `INT UNSIGNED` PK auto increment
- `sales_category_id` `INT UNSIGNED` not null, FK to `sales_categories.id`
- `sales_agent_id` `INT UNSIGNED` not null, FK to `sales_agents.id`
- `agent_code` `VARCHAR(50)` not null, unique
- `status` `ENUM('active','inactive')` not null default `active`
- `validation` `ENUM('approved','pending','declined')` not null default `pending`
- `created_at` `DATETIME` not null
- `updated_at` `DATETIME` not null

Indexes:
- unique `agent_codes_code_unique (agent_code)`
- `agent_codes_category_index (sales_category_id)`
- `agent_codes_agent_index (sales_agent_id)`
- `agent_codes_status_index (status)`
- `agent_codes_validation_index (validation)`
- `agent_codes_created_at_index (created_at)`

Foreign keys:
- `fk_agent_codes_category`: `agent_codes.sales_category_id` -> `sales_categories.id` (`ON DELETE CASCADE`)
- `fk_agent_codes_agent`: `agent_codes.sales_agent_id` -> `sales_agents.id` (`ON DELETE CASCADE`)

## Notes
- Charset/collation: `utf8mb4` / `utf8mb4_unicode_ci`.
- All migrations are idempotent via `CREATE TABLE IF NOT EXISTS`.

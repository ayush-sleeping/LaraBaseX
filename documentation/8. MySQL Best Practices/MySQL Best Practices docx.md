# 🗄️ MySQL Best Practices Documentation (LaraBaseX)

This section documents the MySQL/database practices actually used in LaraBaseX, based strictly on the codebase.

#

## 1. Database Configuration

- MySQL is the default database connection (`config/database.php`).
- Uses `utf8mb4` charset and `utf8mb4_unicode_ci` collation for full Unicode support.
- Strict mode is enabled for safer queries.
- SSL options and dump settings are configured for secure backups.

**Where:**
- `config/database.php`

**How to test:**
- Check `.env` for DB settings and run `php artisan migrate` to verify connection.

#

## 2. Migration Best Practices

- All tables use `bigIncrements('id')` for primary keys.
- Foreign keys are defined with `onDelete('cascade')` for referential integrity.
- Timestamps (`created_at`, `updated_at`) are included by default.
- Auditing columns (`created_by`, `updated_by`) are present in key tables.
- Indexes and unique constraints are used for performance and data integrity.
- Separate tables for roles, permissions, password resets, sessions, etc.

**Where:**
- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/2025_08_01_000002_create_roles_table.php`
- Other migration files in `database/migrations/`

**How to test:**
- Run `php artisan migrate:fresh` and inspect the database schema.
- Check foreign key constraints and indexes in your MySQL client.

#

## 3. Model Conventions

- Models use mass assignment protection (`$fillable` array).
- Traits for caching, activity logging, and role/permission management are included.
- Relationships and attribute casting are defined for robust data handling.

**Where:**
- `app/Models/User.php`, `Role.php`, `Permission.php`, etc.

**How to test:**
- Create/update models in code and verify correct data handling and relationships.

#

## 4. Data Integrity & Auditing

- Foreign keys and cascading deletes maintain data integrity.
- Activity logging via Spatie package tracks user actions.
- Auditing columns (`created_by`, `updated_by`) record who made changes.

**Where:**
- Migrations, models, and `app/Models/User.php`

**How to test:**
- Perform create/update/delete actions and check audit columns and logs.

#

## 5. Unicode & Collation

- All tables use `utf8mb4` charset and `utf8mb4_unicode_ci` collation for emoji and multilingual support.

**Where:**
- `config/database.php`, migration files

**How to test:**
- Insert Unicode/emoji data and verify correct storage/retrieval.

#

## 6. Performance & Security

- Indexes and unique constraints for fast queries.
- Strict mode and SSL options for safer operations.
- Exclude heavy tables from dumps for faster backups.

**Where:**
- `config/database.php`, migration files

**How to test:**
- Run queries and check performance; review backup/dump settings.

#

> All practices above are strictly based on the LaraBaseX codebase. For more details, see the referenced files and try them in your project.

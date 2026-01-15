# CI4 Schema to Migration

A CodeIgniter 4 package that generates migration files from your existing database tables.

## Installation

### Option 1: Via Composer (Recommended)
Run the following command in your CodeIgniter 4 project root:

```bash
composer require sabarinathan/ci4-schema-to-migration
```

### Option 2: Manual Installation (Local Repositories)
If the package is not available on Packagist yet, you can refer to it locally.

1.  Open your CI4 project's `composer.json`.
2.  Add a `repositories` section:
    ```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/sabarinathan/ci4-schema-to-migration"
        }
    ],
    ```
3.  Run:
    ```bash
    composer require sabarinathan/ci4-schema-to-migration:dev-main
    ```

### Command Discovery
After installation, the command `dbmigration:generate` will be automatically discovered by CodeIgniter 4's `Spark` CLI through the `composer` autoloading. You don't need to manually register it.

Verify it is listed by running:
```bash
php spark list
```
You should see `dbmigration:generate` under the `Database` group.

## Usage

Run the Spark command to generate a migration for a specific table:

```bash
php spark dbmigration:generate <table_name>
```

Or generate migrations for **ALL** tables in the database:

```bash
php spark dbmigration:generate --all
```

Example:

```bash
php spark dbmigration:generate users
```

This will create a new migration file in `app/Database/Migrations/`.

## Features

- **Inspects Database**: Reads column definitions, types, defaults, and nullability.
- **Keys Support**: Detects Primary Keys, Foreign Keys, and other Indexes.
- **CI4 Compatibility**: Generates standard `CodeIgniter\Database\Migration` classes using `Forge`.
- **Safe**: Uses `createTable(..., true)` to prevent errors if the table already exists (IF NOT EXISTS).

## Structure

- `src/Commands/GenerateMigration.php`: The Spark command.
- `src/Libraries/DatabaseInspector.php`: Abstraction for reading DB schema.
- `src/Libraries/MigrationGenerator.php`: Logic to build migration code.
- `src/Templates/migration.tpl.php`: Template for the migration file.

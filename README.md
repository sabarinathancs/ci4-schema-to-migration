# CI4 Schema to Migration

A CodeIgniter 4 package that generates migration files from your existing database tables.

## Installation

1. Install via Composer:

   ```bash
   composer require sabarinathan/ci4-schema-to-migration
   ```
   *(Note: Adjust the package name if installed locally or via a different repository)*

2. Ensure CodeIgniter CLI is working.

## Usage

Run the Spark command to generate a migration for a specific table:

```bash
php spark migration:generate <table_name>
```

Or generate migrations for **ALL** tables in the database:

```bash
php spark migration:generate --all
```

Example:

```bash
php spark migration:generate users
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

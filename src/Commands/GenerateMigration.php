<?php

namespace Ci4SchemaToMigration\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Ci4SchemaToMigration\Libraries\DatabaseInspector;
use Ci4SchemaToMigration\Libraries\MigrationGenerator;

class GenerateMigration extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'migration:generate';
    protected $description = 'Generates a migration file from an existing database table.';
    protected $usage = 'migration:generate [table_name]';
    protected $arguments = [
        'table_name' => 'The name of the table to generate a migration for.',
    ];

    public function run(array $params)
    {
        $table = array_shift($params);

        if (empty($table)) {
            $table = CLI::prompt('Enter the table name');
        }

        if (empty($table)) {
            CLI::error('Table name is required.');
            return;
        }

        CLI::write("Generating migration for table: {$table}...", 'yellow');

        try {
            $inspector = new DatabaseInspector();
            $generator = new MigrationGenerator($inspector);

            $migrationContent = $generator->generate($table);

            // Generate filename: YYYY-MM-DD-HHMMSS_Create[Table]Table.php
            $timestamp = date('Y-m-d-His');
            $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $table))) . 'Table';
            $filename = $timestamp . '_' . $className . '.php';

            // Determine path - assume default App/Database/Migrations
            // We use APPPATH constant if available, otherwise fallback (mostly for testing context)
            $path = defined('APPPATH') ? APPPATH . 'Database/Migrations/' : getcwd() . '/app/Database/Migrations/';

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $filepath = $path . $filename;

            if (file_put_contents($filepath, $migrationContent)) {
                CLI::write("Migration created: {$filepath}", 'green');
            } else {
                CLI::error("Failed to write migration file to: {$filepath}");
            }

        } catch (\Exception $e) {
            CLI::error("Error: " . $e->getMessage());
            CLI::write($e->getTraceAsString(), 'red');
        }
    }
}

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
    protected $usage = 'migration:generate [table_name] [options]';
    protected $arguments = [
        'table_name' => 'The name of the table to generate a migration for.',
    ];
    protected $options = [
        '-a|--all' => 'Generate migrations for all tables in the database.',
    ];

    public function run(array $params)
    {
        $all = array_key_exists('all', $params) || array_key_exists('a', $params) || CLI::getOption('all') || CLI::getOption('a');
        $table = array_shift($params);

        $inspector = new DatabaseInspector();
        $generator = new MigrationGenerator($inspector);

        if ($all) {
            $tables = $inspector->getTables();
            if (empty($tables)) {
                CLI::error('No tables found in the database.');
                return;
            }

            CLI::write("Found " . count($tables) . " tables. Starting generation...", 'yellow');

            foreach ($tables as $t) {
                // Skip migrations table usually
                if ($t === 'migrations') {
                    continue;
                }
                $this->generateMigrationForTable($generator, $t);
            }
            return;
        }

        if (empty($table)) {
            $table = CLI::prompt('Enter the table name');
        }

        if (empty($table)) {
            CLI::error('Table name is required.');
            return;
        }

        $this->generateMigrationForTable($generator, $table);
    }

    protected function generateMigrationForTable(MigrationGenerator $generator, string $table)
    {
        CLI::write("Generating migration for table: {$table}...", 'yellow');

        try {
            $migrationContent = $generator->generate($table);

            // Generate filename: YYYY-MM-DD-HHMMSS_Create[Table]Table.php
            // Add sleep to ensure unique timestamps if running in loop fast
            usleep(100000);
            $timestamp = date('Y-m-d-His');
            $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $table))) . 'Table';
            $filename = $timestamp . '_' . $className . '.php';

            // Determine path - assume default App/Database/Migrations
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

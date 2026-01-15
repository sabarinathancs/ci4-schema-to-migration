<?php

namespace Ci4SchemaToMigration\Libraries;

class MigrationGenerator
{
    protected $inspector;

    public function __construct(DatabaseInspector $inspector)
    {
        $this->inspector = $inspector;
    }

    public function generate(string $table, string $namespace = 'App'): string
    {
        $columns = $this->inspector->getColumns($table);
        $foreignKeys = $this->inspector->getForeignKeys($table);
        $indexes = $this->inspector->getIndexes($table);

        $fields = $this->prepareFields($columns);
        $primaryKeys = $this->getPrimaryKeys($columns, $indexes);
        $keys = $this->getKeys($indexes, $primaryKeys);

        $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $table))) . 'Table';

        $templatePath = __DIR__ . '/../Templates/migration.tpl.php';

        // Use include for template rendering to handle namespaces correctly
        $output = $this->render($templatePath, [
            'className' => $className,
            'tableName' => $table,
            'fields' => $this->exportArray($fields),
            'primaryKeys' => $primaryKeys,
            'keys' => $keys,
            'foreignKeys' => $foreignKeys
        ]);

        return $output;
    }

    protected function prepareFields(array $columns): array
    {
        $fields = [];
        foreach ($columns as $col) {
            $field = [
                'type' => strtoupper($col->type),
            ];

            if ($col->max_length) {
                $field['constraint'] = $col->max_length;
            }

            if ($col->default !== null) {
                $field['default'] = $col->default;
            }

            if ($col->nullable) {
                $field['null'] = true;
            }

            // Auto increment usually needs checking extra flags or specific types depending on DB driver
            // CI4 getFieldData might not expose auto_increment consistently across drivers without custom queries
            // checking 'primary_key' often helps but not always implies AI.
            // For now, we omit 'auto_increment' unless we can be sure, or add heuristic.

            $fields[$col->name] = $field;
        }
        return $fields;
    }

    protected function getPrimaryKeys(array $columns, array $indexes): array
    {
        $pks = [];
        foreach ($columns as $col) {
            if (isset($col->primary_key) && $col->primary_key) {
                $pks[] = $col->name;
            }
        }
        return $pks;
    }

    protected function getKeys(array $indexes, array $pks): array
    {
        // Extract non-primary keys
        $keys = [];
        foreach ($indexes as $key => $data) {
            // Implementation depends on structure of getIndexData
            // Assuming $data has fields. 
            // This needs adjustment based on actual CI4 return structure which varies.
            // taking a simplified approach for now:
            if ($key !== 'PRIMARY') {
                // simplify
                $keys[] = $key;
            }
        }
        return array_diff($keys, $pks);
    }

    protected function exportArray(array $data): string
    {
        return var_export($data, true);
    }

    protected function render($templatePath, $data)
    {
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}

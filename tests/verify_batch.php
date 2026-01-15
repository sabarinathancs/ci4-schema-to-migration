<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ci4SchemaToMigration\Libraries\MigrationGenerator;
use Ci4SchemaToMigration\Libraries\DatabaseInspector;

// Mock Schema Data
$mockColumns = [
    (object) ['name' => 'id', 'type' => 'int', 'max_length' => 11, 'default' => null, 'primary_key' => 1, 'nullable' => false],
];
$mockIndexes = ['PRIMARY' => ['fields' => ['id']]];
$mockForeignKeys = [];
$mockTables = ['users', 'posts', 'migrations'];

// Mock Inspector with getTables support
$inspectorStub = new class ($mockColumns, $mockIndexes, $mockForeignKeys, $mockTables) extends DatabaseInspector {
    private $cols, $idxs, $fks, $tbls;
    public function __construct($c, $i, $f, $t)
    {
        $this->cols = $c;
        $this->idxs = $i;
        $this->fks = $f;
        $this->tbls = $t;
    }
    public function getColumns(string $t): array
    {
        return $this->cols;
    }
    public function getForeignKeys(string $t): array
    {
        return $this->fks;
    }
    public function getIndexes(string $t): array
    {
        return $this->idxs;
    }
    public function getTables(): array
    {
        return $this->tbls;
    }
};

echo "Running Batch Verification...\n";

// Simulate Command Logic
$tables = $inspectorStub->getTables();
echo "Found " . count($tables) . " tables.\n";

$generator = new MigrationGenerator($inspectorStub);

foreach ($tables as $t) {
    if ($t === 'migrations') {
        echo "Skipping migrations table.\n";
        continue;
    }
    echo "Generating for $t...\n";
    $code = $generator->generate($t);
    if (strpos($code, "class Create" . ucwords($t) . "Table") !== false) {
        echo " - Class name check OK\n";
    } else {
        echo " - Class name check FAILED\n";
    }
}

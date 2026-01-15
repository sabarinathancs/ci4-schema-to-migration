<?php

namespace Ci4SchemaToMigration\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class DatabaseInspector
{
    protected $db;

    public function __construct(BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getTables(): array
    {
        return $this->db->listTables();
    }

    public function getColumns(string $table): array
    {
        return $this->db->getFieldData($table);
    }

    public function getForeignKeys(string $table): array
    {
        return $this->db->getForeignKeyData($table);
    }

    public function getIndexes(string $table): array
    {
        return $this->db->getIndexData($table);
    }
}

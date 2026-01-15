<?= "<?php\n" ?>

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class <?= $className ?> extends Migration
{
    public function up()
    {
        // Fields
        $this->forge->addField(<?= $fields ?>);

        // Keys
<?php if (!empty($primaryKeys)): ?>
<?php foreach ($primaryKeys as $key): ?>
        $this->forge->addKey('<?= $key ?>', true);
<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($keys)): ?>
<?php foreach ($keys as $key): ?>
        $this->forge->addKey('<?= $key ?>');
<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($foreignKeys)): ?>
<?php foreach ($foreignKeys as $fk): ?>
        $this->forge->addForeignKey('<?= $fk['column_name'] ?>', '<?= $fk['foreign_table_name'] ?>', '<?= $fk['foreign_column_name'] ?>', '<?= $fk['on_delete'] ?>', '<?= $fk['on_update'] ?>');
<?php endforeach; ?>
<?php endif; ?>

        // Create Table
        $this->forge->createTable('<?= $tableName ?>', true);
    }

    public function down()
    {
        $this->forge->dropTable('<?= $tableName ?>');
    }
}
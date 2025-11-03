<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLocationColumnsToHomes extends Migration
{
    public function up()
    {
        // For SQLite simple ALTER TABLE ADD COLUMN is supported for new columns
        $fields = [
            'road_id'     => ['type' => 'INTEGER', 'null' => true],
            'sub_road_id' => ['type' => 'INTEGER', 'null' => true],
            'address_id'  => ['type' => 'INTEGER', 'null' => true],
        ];
        $this->forge->addColumn('homes', $fields);
    }

    public function down()
    {
        // SQLite does not support DROP COLUMN easily; leave as no-op to avoid data loss in down.
        // If needed, recreate table without columns in a proper migration rollback sequence.
    }
}

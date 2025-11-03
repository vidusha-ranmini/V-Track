<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLocationColumnsToHomes extends Migration
{
    public function up()
    {
        // For SQLite simple ALTER TABLE ADD COLUMN is supported for new columns
        // But be defensive: only add columns that do not already exist
        $existing = [];
        try {
            $existing = $this->db->getFieldNames('homes');
        } catch (\Exception $e) {
            $existing = [];
        }

        $fields = [];
        if (!in_array('road_id', $existing)) {
            $fields['road_id'] = ['type' => 'INTEGER', 'null' => true];
        }
        if (!in_array('sub_road_id', $existing)) {
            $fields['sub_road_id'] = ['type' => 'INTEGER', 'null' => true];
        }
        if (!in_array('address_id', $existing)) {
            $fields['address_id'] = ['type' => 'INTEGER', 'null' => true];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('homes', $fields);
        }
    }

    public function down()
    {
        // SQLite does not support DROP COLUMN easily; leave as no-op to avoid data loss in down.
        // If needed, recreate table without columns in a proper migration rollback sequence.
    }
}

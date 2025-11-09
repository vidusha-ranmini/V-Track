<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentSubRoadToSubRoads extends Migration
{
    public function up()
    {
        // Add parent_sub_road_id to sub_roads table for nested hierarchy
        $fields = [
            'parent_sub_road_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'road_id'
            ]
        ];
        
        $this->forge->addColumn('sub_roads', $fields);
        
        // Add foreign key constraint
        $this->forge->addForeignKey('parent_sub_road_id', 'sub_roads', 'id', 'SET NULL', 'CASCADE', 'fk_sub_roads_parent');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        // Drop foreign key first
        if ($this->db->DBDriver === 'MySQLi') {
            $this->forge->dropForeignKey('sub_roads', 'fk_sub_roads_parent');
        }
        
        // Drop the column
        $this->forge->dropColumn('sub_roads', 'parent_sub_road_id');
    }
}

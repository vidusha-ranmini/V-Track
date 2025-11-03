<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoadsTables extends Migration
{
    public function up()
    {
        // roads table
        $this->forge->addField([
            'id' => [ 'type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'name' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => false ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('name');
        $this->forge->createTable('roads', true);

        // sub_roads table
        $this->forge->addField([
            'id' => [ 'type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'road_id' => [ 'type' => 'INTEGER', 'constraint' => 11, 'null' => false ],
            'name' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => false ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('road_id');
        $this->forge->createTable('sub_roads', true);

        // addresses table
        $this->forge->addField([
            'id' => [ 'type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'road_id' => [ 'type' => 'INTEGER', 'constraint' => 11, 'null' => true ],
            'sub_road_id' => [ 'type' => 'INTEGER', 'constraint' => 11, 'null' => true ],
            'address' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => false ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('road_id');
        $this->forge->addKey('sub_road_id');
        $this->forge->createTable('addresses', true);
    }

    public function down()
    {
        $this->forge->dropTable('addresses', true);
        $this->forge->dropTable('sub_roads', true);
        $this->forge->dropTable('roads', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLampsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'lamp_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'road_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'sub_road_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'address_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'is_broken' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('road_id', 'roads', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('sub_road_id', 'sub_roads', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('address_id', 'addresses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('lamps');
    }

    public function down()
    {
        $this->forge->dropTable('lamps');
    }
}

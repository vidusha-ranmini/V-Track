<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBusinessesTable extends Migration
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
            'business_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'business_owner' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
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
            'business_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addKey('road_id');
        $this->forge->addKey('sub_road_id');
        $this->forge->addKey('address_id');
        
        // Add foreign keys if using InnoDB
        $this->forge->addForeignKey('road_id', 'roads', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('sub_road_id', 'sub_roads', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('address_id', 'addresses', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('businesses', true);
    }

    public function down()
    {
        $this->forge->dropTable('businesses', true);
    }
}

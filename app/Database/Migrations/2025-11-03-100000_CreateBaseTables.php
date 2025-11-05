<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaseTables extends Migration
{
    public function up()
    {
        // Create homes table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'resident_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'waste_collector' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
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
        $this->forge->createTable('homes', true);

        // Create members table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'home_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'name_with_initial' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'member_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nic' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'gender' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'age' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'occupation' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'occupation_other' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'whatsapp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'school' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'grade' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
            ],
            'university_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'land_house_status' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'disabled' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'no',
            ],
            'cv' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
        $this->forge->addKey('home_id');
        $this->forge->addForeignKey('home_id', 'homes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('members', true);

        // Create member_offers table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'offer' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_offers', true);
    }

    public function down()
    {
        $this->forge->dropTable('member_offers', true);
        $this->forge->dropTable('members', true);
        $this->forge->dropTable('homes', true);
    }
}

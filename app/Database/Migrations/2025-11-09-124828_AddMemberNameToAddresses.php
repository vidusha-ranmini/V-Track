<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMemberNameToAddresses extends Migration
{
    public function up()
    {
        // Add member_name column to addresses table
        $fields = [
            'member_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'address'
            ]
        ];
        
        $this->forge->addColumn('addresses', $fields);
    }

    public function down()
    {
        // Drop the member_name column
        $this->forge->dropColumn('addresses', 'member_name');
    }
}

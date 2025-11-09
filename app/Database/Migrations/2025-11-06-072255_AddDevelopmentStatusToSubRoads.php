<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDevelopmentStatusToSubRoads extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sub_roads', [
            'is_developed' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'name'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sub_roads', 'is_developed');
    }
}

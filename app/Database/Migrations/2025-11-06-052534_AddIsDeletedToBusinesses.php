<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsDeletedToBusinesses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('businesses', [
            'is_deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'business_type'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('businesses', 'is_deleted');
    }
}

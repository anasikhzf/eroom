<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasswordToBoards extends Migration
{
    public function up()
    {
        $this->forge->addColumn('boards', [
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('boards', 'password');
    }
}

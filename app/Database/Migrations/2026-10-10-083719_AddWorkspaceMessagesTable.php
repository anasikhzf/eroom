<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWorkspaceMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'board_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sender_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('board_id');
        $this->forge->addForeignKey('board_id', 'boards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('workspace_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('workspace_messages', true);
    }
}

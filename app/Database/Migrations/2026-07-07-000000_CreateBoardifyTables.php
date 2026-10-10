<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBoardifyTables extends Migration
{
    public function up()
    {
        // Drop old files table if exists
        if ($this->db->tableExists('files')) {
            $this->forge->dropTable('files');
        }

        // Boards table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => '#026aa7',
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
        $this->forge->createTable('boards');

        // Lists table
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'position' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
        $this->forge->addForeignKey('board_id', 'boards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lists');

        // Cards table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'list_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'position' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'label_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
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
        $this->forge->addForeignKey('list_id', 'lists', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cards');
    }

    public function down()
    {
        $this->forge->dropTable('cards');
        $this->forge->dropTable('lists');
        $this->forge->dropTable('boards');
    }
}

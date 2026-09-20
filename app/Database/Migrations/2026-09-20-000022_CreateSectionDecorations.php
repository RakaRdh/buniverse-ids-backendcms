<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSectionDecorations extends Migration
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
            'event_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'section_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'layout_mode' => [
                'type'       => 'ENUM',
                'constraint' => ['manual', 'auto'],
                'default'    => 'manual',
            ],
            'position' => [
                'type'       => 'ENUM',
                'constraint' => ['top-left', 'top-right', 'middle-left', 'middle-right', 'bottom-left', 'bottom-right'],
                'null'       => true,
            ],
            'auto_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'auto_gap_px' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 100,
            ],
            'image_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'size_px' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 200,
            ],
            'opacity' => [
                'type'       => 'DECIMAL',
                'constraint' => '3,2',
                'default'    => 1.00,
            ],
            'is_rotated' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'rotation_direction' => [
                'type'       => 'ENUM',
                'constraint' => ['clockwise', 'counter-clockwise'],
                'default'    => 'clockwise',
            ],
            'show_on_mobile' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addKey(['event_id', 'section_key', 'is_active']);
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('section_decorations', true);
    }

    public function down()
    {
        $this->forge->dropTable('section_decorations', true);
    }
}

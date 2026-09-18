<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventHeroImages extends Migration
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
            'image_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'alt_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
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
        $this->forge->addKey('event_id');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('event_hero_images', true);
    }

    public function down()
    {
        $this->forge->dropTable('event_hero_images', true);
    }
}

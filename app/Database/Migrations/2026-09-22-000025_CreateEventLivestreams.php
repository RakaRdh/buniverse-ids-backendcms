<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventLivestreams extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('event_livestreams')) {
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
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => 'Live Streaming Plenary Session',
                ],
                'youtube_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'default'    => 'https://www.youtube.com/watch?v=rFZHOHl-L8A',
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
            $this->forge->addKey('event_id');
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('event_livestreams', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('event_livestreams')) {
            $this->forge->dropTable('event_livestreams', true);
        }
    }
}

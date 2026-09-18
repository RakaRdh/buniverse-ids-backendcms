<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveCapacityAndAddScheduleHierarchy extends Migration
{
    public function up()
    {
        // 1. Remove capacity from schedule_rooms if exists
        if ($this->db->fieldExists('capacity', 'schedule_rooms')) {
            $this->forge->dropColumn('schedule_rooms', 'capacity');
        }

        // 2. Create schedule_session_rooms
        if (!$this->db->tableExists('schedule_session_rooms')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'session_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'room_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1,
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
            $this->forge->addKey('session_id');
            $this->forge->addKey('room_id');
            $this->forge->addForeignKey('session_id', 'schedule_sessions', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('room_id', 'schedule_rooms', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('schedule_session_rooms', true);
        }

        // 3. Create schedule_session_items
        if (!$this->db->tableExists('schedule_session_items')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'session_room_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'subtitle' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1,
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
            $this->forge->addKey('session_room_id');
            $this->forge->addForeignKey('session_room_id', 'schedule_session_rooms', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('schedule_session_items', true);
        }

        // 4. Create session_item_moderators
        if (!$this->db->tableExists('session_item_moderators')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'item_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'moderator_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('item_id');
            $this->forge->addKey('moderator_id');
            $this->forge->addForeignKey('item_id', 'schedule_session_items', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('moderator_id', 'moderators', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('session_item_moderators', true);
        }

        // 5. Create session_item_speakers
        if (!$this->db->tableExists('session_item_speakers')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'item_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'speaker_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('item_id');
            $this->forge->addKey('speaker_id');
            $this->forge->addForeignKey('item_id', 'schedule_session_items', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('speaker_id', 'speakers', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('session_item_speakers', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('session_item_speakers', true);
        $this->forge->dropTable('session_item_moderators', true);
        $this->forge->dropTable('schedule_session_items', true);
        $this->forge->dropTable('schedule_session_rooms', true);

        if (!$this->db->fieldExists('capacity', 'schedule_rooms')) {
            $this->forge->addColumn('schedule_rooms', [
                'capacity' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]);
        }
    }
}

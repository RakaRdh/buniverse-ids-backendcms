<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScheduleSessions extends Migration
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
            'schedule_day_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'parent_session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'room_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
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
            'session_type' => [
                'type'       => 'ENUM',
                'constraint' => ['registration', 'plenary', 'keynote', 'special_session', 'break', 'closing'],
                'default'    => 'plenary',
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
        $this->forge->addKey('schedule_day_id');
        $this->forge->addKey('parent_session_id');
        $this->forge->addKey('room_id');
        $this->forge->addForeignKey('schedule_day_id', 'schedule_days', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('parent_session_id', 'schedule_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('room_id', 'schedule_rooms', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('schedule_sessions', true);
    }

    public function down()
    {
        $this->forge->dropTable('schedule_sessions', true);
    }
}

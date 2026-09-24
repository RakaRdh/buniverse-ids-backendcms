<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQna extends Migration
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
            'schedule_day_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'schedule_session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'asker_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'question' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('schedule_day_id');
        $this->forge->addKey('schedule_session_id');

        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('schedule_day_id', 'schedule_days', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('schedule_session_id', 'schedule_sessions', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('qna', true);
    }

    public function down()
    {
        $this->forge->dropTable('qna', true);
    }
}

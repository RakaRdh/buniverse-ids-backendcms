<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketClickLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'clicked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'referrer' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('event_id');
        $this->forge->addKey('clicked_at');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ticket_click_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('ticket_click_logs', true);
    }
}

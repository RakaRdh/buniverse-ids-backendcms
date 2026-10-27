<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvitationMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'Undangan Resmi BeritaSatu Malam Apresiasi 2026',
            ],
            'greeting' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'Kepada Yth. {name}',
            ],
            'body_content' => [
                'type' => 'TEXT',
            ],
            'button_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Buka Undangan Digital',
            ],
            'closing_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu berkenan hadir.',
            ],
            'footer_note' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => 'Panitia Pelaksana BeritaSatu Malam Apresiasi 2026',
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
        $this->forge->addKey('event_id', false, true); // unique per event
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invitation_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('invitation_messages', true);
    }
}

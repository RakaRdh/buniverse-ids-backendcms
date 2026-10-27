<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvitationListsTable extends Migration
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
            'template_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'invitation_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'institution' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'rsvp_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'attending', 'declined'],
                'default'    => 'pending',
            ],
            'rsvp_pax' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'rsvp_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'blast_status' => [
                'type'       => 'ENUM',
                'constraint' => ['unsend', 'sent', 'failed'],
                'default'    => 'unsend',
            ],
            'blast_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'blast_error' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'view_count' => [
                'type'       => 'INT',
                'constraint' => 10,
                'default'    => 0,
            ],
            'last_viewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('invitation_code', false, true); // unique
        $this->forge->addKey(['event_id', 'slug'], false, true); // unique
        $this->forge->addKey('blast_status');
        $this->forge->addKey('rsvp_status');
        $this->forge->addKey('name');
        $this->forge->addKey('institution');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('template_id', 'invitation_templates', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('invitation_lists', true);
    }

    public function down()
    {
        $this->forge->dropTable('invitation_lists', true);
    }
}

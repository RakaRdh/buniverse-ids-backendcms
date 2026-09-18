<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvents extends Migration
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
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'year' => [
                'type'       => 'SMALLINT',
                'constraint' => 4,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'theme' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'event_date_start' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'event_date_end' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default'    => 'draft',
            ],
            'news_tag_slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'template_version' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'v1',
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
        $this->forge->addUniqueKey('slug');
        $this->forge->addUniqueKey('year');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('created_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('events', true);
    }

    public function down()
    {
        $this->forge->dropTable('events', true);
    }
}

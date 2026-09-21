<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewsPortals extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('news_portals')) {
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
                'portal_key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'portal_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'portal_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
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
            $this->forge->createTable('news_portals', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('news_portals')) {
            $this->forge->dropTable('news_portals', true);
        }
    }
}

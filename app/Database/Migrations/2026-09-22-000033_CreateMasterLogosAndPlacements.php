<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterLogosAndPlacements extends Migration
{
    public function up()
    {
        // 1. Create event_logos table
        if (!$this->db->tableExists('event_logos')) {
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
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'logo_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                ],
                'logo_white_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                ],
                'default_target_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
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
            $this->forge->createTable('event_logos', true);
        }

        // 2. Create section_logo_placements table
        if (!$this->db->tableExists('section_logo_placements')) {
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
                'slot_key' => [
                    'type'       => 'ENUM',
                    'constraint' => ['navbar_slot_1', 'navbar_slot_2', 'overview', 'floating_bottom', 'footer'],
                ],
                'logo_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'variant' => [
                    'type'       => 'ENUM',
                    'constraint' => ['color', 'white', 'auto'],
                    'default'    => 'auto',
                ],
                'target_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
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
            $this->forge->addUniqueKey(['event_id', 'slot_key']);
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('logo_id', 'event_logos', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('section_logo_placements', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('section_logo_placements')) {
            $this->forge->dropTable('section_logo_placements', true);
        }
        if ($this->db->tableExists('event_logos')) {
            $this->forge->dropTable('event_logos', true);
        }
    }
}

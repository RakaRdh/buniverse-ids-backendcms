<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTintColorToDecorations extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('section_decorations') && !$this->db->fieldExists('tint_color', 'section_decorations')) {
            $this->forge->addColumn('section_decorations', [
                'tint_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'opacity',
                ],
            ]);
        }

        if ($this->db->tableExists('custom_placements') && !$this->db->fieldExists('tint_color', 'custom_placements')) {
            $this->forge->addColumn('custom_placements', [
                'tint_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'opacity',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('section_decorations') && $this->db->fieldExists('tint_color', 'section_decorations')) {
            $this->forge->dropColumn('section_decorations', 'tint_color');
        }

        if ($this->db->tableExists('custom_placements') && $this->db->fieldExists('tint_color', 'custom_placements')) {
            $this->forge->dropColumn('custom_placements', 'tint_color');
        }
    }
}

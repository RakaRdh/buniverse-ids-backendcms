<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAutoFieldsToSectionDecorations extends Migration
{
    public function up()
    {
        $fields = [
            'auto_start_side' => [
                'type'       => 'ENUM',
                'constraint' => ['left', 'right'],
                'default'    => 'left',
                'after'      => 'auto_gap_px',
            ],
            'auto_gap_unit' => [
                'type'       => 'ENUM',
                'constraint' => ['px', 'vh', 'rem', 'percent'],
                'default'    => 'px',
                'after'      => 'auto_start_side',
            ],
            'edge_offset_mode' => [
                'type'       => 'ENUM',
                'constraint' => ['fixed', 'custom'],
                'default'    => 'fixed',
                'after'      => 'auto_gap_unit',
            ],
            'edge_offset_percent' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 50,
                'after'      => 'edge_offset_mode',
            ],
        ];

        $this->forge->addColumn('section_decorations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('section_decorations', [
            'auto_start_side',
            'auto_gap_unit',
            'edge_offset_mode',
            'edge_offset_percent',
        ]);
    }
}

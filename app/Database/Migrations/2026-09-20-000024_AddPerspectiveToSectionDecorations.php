<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerspectiveToSectionDecorations extends Migration
{
    public function up()
    {
        $fields = [
            'perspective_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'rotation_direction',
            ],
            'perspective_value' => [
                'type'       => 'INT',
                'constraint' => 6,
                'default'    => 800,
                'after'      => 'perspective_enabled',
            ],
            'rotate_x' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 0.0,
                'after'      => 'perspective_value',
            ],
            'rotate_y' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 0.0,
                'after'      => 'rotate_x',
            ],
        ];

        $this->forge->addColumn('section_decorations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('section_decorations', [
            'perspective_enabled',
            'perspective_value',
            'rotate_x',
            'rotate_y',
        ]);
    }
}

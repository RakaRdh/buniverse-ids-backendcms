<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerspectiveAndRotateZToCustomPlacements extends Migration
{
    public function up()
    {
        // 1. Add 3D perspective and rotate X, Y, Z to custom_placements
        if ($this->db->tableExists('custom_placements')) {
            $cpFields = [];
            if (!$this->db->fieldExists('perspective_enabled', 'custom_placements')) {
                $cpFields['perspective_enabled'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'rotation_direction',
                ];
            }
            if (!$this->db->fieldExists('perspective_value', 'custom_placements')) {
                $cpFields['perspective_value'] = [
                    'type'       => 'INT',
                    'constraint' => 6,
                    'default'    => 800,
                    'after'      => 'perspective_enabled',
                ];
            }
            if (!$this->db->fieldExists('rotate_x', 'custom_placements')) {
                $cpFields['rotate_x'] = [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,1',
                    'default'    => 0.0,
                    'after'      => 'perspective_value',
                ];
            }
            if (!$this->db->fieldExists('rotate_y', 'custom_placements')) {
                $cpFields['rotate_y'] = [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,1',
                    'default'    => 0.0,
                    'after'      => 'rotate_x',
                ];
            }
            if (!$this->db->fieldExists('rotate_z', 'custom_placements')) {
                $cpFields['rotate_z'] = [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,1',
                    'default'    => 0.0,
                    'after'      => 'rotate_y',
                ];
            }

            if (!empty($cpFields)) {
                $this->forge->addColumn('custom_placements', $cpFields);
            }
        }

        // 2. Add rotate_z to section_decorations if not exists
        if ($this->db->tableExists('section_decorations') && !$this->db->fieldExists('rotate_z', 'section_decorations')) {
            $this->forge->addColumn('section_decorations', [
                'rotate_z' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,1',
                    'default'    => 0.0,
                    'after'      => 'rotate_y',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('custom_placements')) {
            $dropFields = array_intersect(
                ['perspective_enabled', 'perspective_value', 'rotate_x', 'rotate_y', 'rotate_z'],
                $this->db->getFieldNames('custom_placements')
            );
            if (!empty($dropFields)) {
                $this->forge->dropColumn('custom_placements', $dropFields);
            }
        }

        if ($this->db->tableExists('section_decorations') && $this->db->fieldExists('rotate_z', 'section_decorations')) {
            $this->forge->dropColumn('section_decorations', 'rotate_z');
        }
    }
}

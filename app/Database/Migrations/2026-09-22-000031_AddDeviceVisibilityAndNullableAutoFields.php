<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeviceVisibilityAndNullableAutoFields extends Migration
{
    public function up()
    {
        // 1. Add device_visibility to section_decorations
        if ($this->db->tableExists('section_decorations') && !$this->db->fieldExists('device_visibility', 'section_decorations')) {
            $this->forge->addColumn('section_decorations', [
                'device_visibility' => [
                    'type'       => 'ENUM',
                    'constraint' => ['all', 'desktop_only', 'mobile_only'],
                    'default'    => 'all',
                    'after'      => 'show_on_mobile',
                ],
            ]);

            // Data sync based on existing show_on_mobile
            $this->db->query("UPDATE section_decorations SET device_visibility = 'desktop_only' WHERE show_on_mobile = 0");
            $this->db->query("UPDATE section_decorations SET device_visibility = 'all' WHERE show_on_mobile = 1");
        }

        // 2. Add device_visibility to custom_placements
        if ($this->db->tableExists('custom_placements') && !$this->db->fieldExists('device_visibility', 'custom_placements')) {
            $this->forge->addColumn('custom_placements', [
                'device_visibility' => [
                    'type'       => 'ENUM',
                    'constraint' => ['all', 'desktop_only', 'mobile_only'],
                    'default'    => 'all',
                    'after'      => 'show_on_mobile',
                ],
            ]);

            // Data sync based on existing show_on_mobile
            $this->db->query("UPDATE custom_placements SET device_visibility = 'desktop_only' WHERE show_on_mobile = 0");
            $this->db->query("UPDATE custom_placements SET device_visibility = 'all' WHERE show_on_mobile = 1");
        }

        // 3. Make auto-layout fields nullable on section_decorations
        if ($this->db->tableExists('section_decorations')) {
            $modifyFields = [
                'auto_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                ],
                'auto_gap_px' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                ],
            ];

            if ($this->db->fieldExists('auto_start_side', 'section_decorations')) {
                $modifyFields['auto_start_side'] = [
                    'type'       => 'ENUM',
                    'constraint' => ['left', 'right'],
                    'null'       => true,
                    'default'    => null,
                ];
            }

            if ($this->db->fieldExists('auto_gap_unit', 'section_decorations')) {
                $modifyFields['auto_gap_unit'] = [
                    'type'       => 'ENUM',
                    'constraint' => ['px', 'vh', 'rem', 'percent'],
                    'null'       => true,
                    'default'    => null,
                ];
            }

            if ($this->db->fieldExists('edge_offset_mode', 'section_decorations')) {
                $modifyFields['edge_offset_mode'] = [
                    'type'       => 'ENUM',
                    'constraint' => ['fixed', 'custom'],
                    'null'       => true,
                    'default'    => null,
                ];
            }

            if ($this->db->fieldExists('edge_offset_percent', 'section_decorations')) {
                $modifyFields['edge_offset_percent'] = [
                    'type'       => 'INT',
                    'constraint' => 3,
                    'null'       => true,
                    'default'    => null,
                ];
            }

            $this->forge->modifyColumn('section_decorations', $modifyFields);

            // Clean up existing manual records: set auto fields to NULL
            $this->db->query("UPDATE section_decorations SET auto_count = NULL, auto_gap_px = NULL, auto_start_side = NULL, auto_gap_unit = NULL, edge_offset_mode = NULL, edge_offset_percent = NULL WHERE layout_mode = 'manual'");
        }
    }

    public function down()
    {
        if ($this->db->tableExists('section_decorations') && $this->db->fieldExists('device_visibility', 'section_decorations')) {
            $this->forge->dropColumn('section_decorations', 'device_visibility');
        }

        if ($this->db->tableExists('custom_placements') && $this->db->fieldExists('device_visibility', 'custom_placements')) {
            $this->forge->dropColumn('custom_placements', 'device_visibility');
        }
    }
}

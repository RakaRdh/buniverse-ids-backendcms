<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventThemePropertiesAndExtendSettings extends Migration
{
    public function up()
    {
        // 1. Create event_theme_properties (EAV table for CSS root variables & colors)
        if (!$this->db->tableExists('event_theme_properties')) {
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
                'css_property' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100, // e.g. '--primary-1-1', '--bg-page'
                ],
                'css_value' => [
                    'type' => 'TEXT',    // hex, linear-gradient(...), font string, etc.
                ],
                'value_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['solid', 'gradient', 'text', 'dimension'],
                    'default'    => 'solid',
                ],
                'category' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,  // 'neutral', 'primary-1', 'primary-2', 'semantic', 'fonts', 'spacing'
                    'default'    => 'semantic',
                ],
                'label' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150, // Human friendly label
                    'null'       => true,
                ],
                'sort_order' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
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
            $this->forge->addKey('event_id');
            $this->forge->addUniqueKey(['event_id', 'css_property']);
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('created_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->addForeignKey('updated_by', 'admin_users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('event_theme_properties', true);
        }

        // 2. Extend event_settings with cta_label, copyright_text, nav_items_json, logo_footer_url
        $fieldsToAdd = [];

        if (!$this->db->fieldExists('cta_label', 'event_settings')) {
            $fieldsToAdd['cta_label'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => 'Get Tickets',
            ];
        }

        if (!$this->db->fieldExists('copyright_text', 'event_settings')) {
            $fieldsToAdd['copyright_text'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => 'Investor Daily Summit.',
            ];
        }

        if (!$this->db->fieldExists('nav_items_json', 'event_settings')) {
            $fieldsToAdd['nav_items_json'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('logo_footer_url', 'event_settings')) {
            $fieldsToAdd['logo_footer_url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('event_settings', $fieldsToAdd);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('event_theme_properties')) {
            $this->forge->dropTable('event_theme_properties', true);
        }

        $fieldsToDrop = ['cta_label', 'copyright_text', 'nav_items_json', 'logo_footer_url'];
        foreach ($fieldsToDrop as $field) {
            if ($this->db->fieldExists($field, 'event_settings')) {
                $this->forge->dropColumn('event_settings', $field);
            }
        }
    }
}

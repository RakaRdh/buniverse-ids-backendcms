<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLogoVariantsAndContactToEventSettings extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('logo_a_white_url', 'event_settings')) {
            $fields['logo_a_white_url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'logo_a_url',
            ];
        }

        if (!$this->db->fieldExists('logo_a_target_url', 'event_settings')) {
            $fields['logo_a_target_url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => '#home',
                'after'      => 'logo_a_white_url',
            ];
        }

        if (!$this->db->fieldExists('logo_b_white_url', 'event_settings')) {
            $fields['logo_b_white_url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'logo_b_url',
            ];
        }

        if (!$this->db->fieldExists('logo_b_target_url', 'event_settings')) {
            $fields['logo_b_target_url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => 'https://investor.id',
                'after'      => 'logo_b_white_url',
            ];
        }

        if (!$this->db->fieldExists('contact_phone', 'event_settings')) {
            $fields['contact_phone'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => '+62 855-1235-000',
            ];
        }

        if (!$this->db->fieldExists('contact_label', 'event_settings')) {
            $fields['contact_label'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'Phone',
            ];
        }

        if (!empty($fields) && $this->db->tableExists('event_settings')) {
            $this->forge->addColumn('event_settings', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('event_settings')) {
            $cols = ['logo_a_white_url', 'logo_a_target_url', 'logo_b_white_url', 'logo_b_target_url', 'contact_phone', 'contact_label'];
            $existing = [];
            foreach ($cols as $c) {
                if ($this->db->fieldExists($c, 'event_settings')) {
                    $existing[] = $c;
                }
            }
            if (!empty($existing)) {
                $this->forge->dropColumn('event_settings', $existing);
            }
        }
    }
}

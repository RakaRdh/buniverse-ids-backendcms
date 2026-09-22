<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactUrlFloatingBarAndCtaActiveToEventSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('event_settings')) {
            $fields = [];

            if (!$this->db->fieldExists('contact_url', 'event_settings')) {
                $fields['contact_url'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'after'      => 'contact_phone',
                ];
            }

            if (!$this->db->fieldExists('cta_ticket_active', 'event_settings')) {
                $fields['cta_ticket_active'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'ticket_url',
                ];
            }

            if (!$this->db->fieldExists('floating_bar_enabled', 'event_settings')) {
                $fields['floating_bar_enabled'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'cta_ticket_active',
                ];
            }

            if (!$this->db->fieldExists('floating_bar_text', 'event_settings')) {
                $fields['floating_bar_text'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => 'Get Your Seat Now!',
                    'after'      => 'floating_bar_enabled',
                ];
            }

            if (!$this->db->fieldExists('floating_bar_btn_label', 'event_settings')) {
                $fields['floating_bar_btn_label'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => 'Get Tickets',
                    'after'      => 'floating_bar_text',
                ];
            }

            if (!$this->db->fieldExists('floating_bar_btn_url', 'event_settings')) {
                $fields['floating_bar_btn_url'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'after'      => 'floating_bar_btn_label',
                ];
            }

            if (!empty($fields)) {
                $this->forge->addColumn('event_settings', $fields);
            }

            // Update default contact phone to +62 812-1267-8789 if it matches old dummy
            $this->db->query("UPDATE event_settings SET contact_phone = '+62 812-1267-8789' WHERE contact_phone = '+62 855-1235-000' OR contact_phone IS NULL");
        }
    }

    public function down()
    {
        if ($this->db->tableExists('event_settings')) {
            $cols = ['contact_url', 'cta_ticket_active', 'floating_bar_enabled', 'floating_bar_text', 'floating_bar_btn_label', 'floating_bar_btn_url'];
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

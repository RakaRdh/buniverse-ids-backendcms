<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQuotaAndGatePasscodeToEvents extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('quota', 'events')) {
            $fields['quota'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'template_version',
            ];
        }

        if (!$this->db->fieldExists('gate_passcode', 'events')) {
            $fields['gate_passcode'] = [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => '123456',
                'after'      => 'quota',
            ];
        }

        if (!empty($fields) && $this->db->tableExists('events')) {
            $this->forge->addColumn('events', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('events')) {
            $cols = ['quota', 'gate_passcode'];
            $existing = [];
            foreach ($cols as $c) {
                if ($this->db->fieldExists($c, 'events')) {
                    $existing[] = $c;
                }
            }
            if (!empty($existing)) {
                $this->forge->dropColumn('events', $existing);
            }
        }
    }
}

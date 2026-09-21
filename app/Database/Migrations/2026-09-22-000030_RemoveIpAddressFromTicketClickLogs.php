<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveIpAddressFromTicketClickLogs extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('ip_address', 'ticket_click_logs')) {
            $this->forge->dropColumn('ticket_click_logs', 'ip_address');
        }
    }

    public function down()
    {
        if (!$this->db->fieldExists('ip_address', 'ticket_click_logs')) {
            $this->forge->addColumn('ticket_click_logs', [
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => true,
                    'after'      => 'clicked_at',
                ],
            ]);
        }
    }
}

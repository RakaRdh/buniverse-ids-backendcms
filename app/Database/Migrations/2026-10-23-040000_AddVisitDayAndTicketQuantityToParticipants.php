<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitDayAndTicketQuantityToParticipants extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('participants')) {
            $fields = [];
            if (!$this->db->fieldExists('visit_day', 'participants')) {
                $fields['visit_day'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'all_days',
                    'null'       => false,
                    'after'      => 'participant_type',
                ];
            }
            if (!$this->db->fieldExists('order_id', 'participants')) {
                $fields['order_id'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                    'null'       => true,
                    'after'      => 'ticket_code',
                ];
            }
            if (!$this->db->fieldExists('ticket_index', 'participants')) {
                $fields['ticket_index'] = [
                    'type'       => 'INT',
                    'constraint' => 5,
                    'default'    => 1,
                    'null'       => false,
                    'after'      => 'order_id',
                ];
            }
            if (!empty($fields)) {
                $this->forge->addColumn('participants', $fields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('participants')) {
            $cols = [];
            if ($this->db->fieldExists('visit_day', 'participants')) {
                $cols[] = 'visit_day';
            }
            if ($this->db->fieldExists('order_id', 'participants')) {
                $cols[] = 'order_id';
            }
            if ($this->db->fieldExists('ticket_index', 'participants')) {
                $cols[] = 'ticket_index';
            }
            if (!empty($cols)) {
                $this->forge->dropColumn('participants', $cols);
            }
        }
    }
}

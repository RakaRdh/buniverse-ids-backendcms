<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerDayCheckinColumnsToParticipants extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('participants')) {
            $fields = [];
            if (!$this->db->fieldExists('checked_in_day1_at', 'participants')) {
                $fields['checked_in_day1_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'checked_in_at',
                ];
            }
            if (!$this->db->fieldExists('checked_in_day2_at', 'participants')) {
                $fields['checked_in_day2_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'checked_in_day1_at',
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
            if ($this->db->fieldExists('checked_in_day1_at', 'participants')) {
                $cols[] = 'checked_in_day1_at';
            }
            if ($this->db->fieldExists('checked_in_day2_at', 'participants')) {
                $cols[] = 'checked_in_day2_at';
            }
            if (!empty($cols)) {
                $this->forge->dropColumn('participants', $cols);
            }
        }
    }
}

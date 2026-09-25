<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSessionItemIdToQna extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('qna') && !$this->db->fieldExists('session_item_id', 'qna')) {
            $this->forge->addColumn('qna', [
                'session_item_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'schedule_session_id',
                ],
            ]);

            // Add index and foreign key if table exists
            if ($this->db->tableExists('schedule_session_items')) {
                $this->db->query('ALTER TABLE `qna` ADD CONSTRAINT `fk_qna_session_item` FOREIGN KEY (`session_item_id`) REFERENCES `schedule_session_items`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('qna') && $this->db->fieldExists('session_item_id', 'qna')) {
            try {
                $this->db->query('ALTER TABLE `qna` DROP FOREIGN KEY `fk_qna_session_item`');
            } catch (\Throwable $e) {}
            $this->forge->dropColumn('qna', 'session_item_id');
        }
    }
}

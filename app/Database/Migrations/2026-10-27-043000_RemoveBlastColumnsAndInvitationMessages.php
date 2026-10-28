<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveBlastColumnsAndInvitationMessages extends Migration
{
    public function up()
    {
        // 1. Drop foreign keys or indexes if needed, then drop blast columns from invitation_lists
        if ($this->db->fieldExists('blast_by', 'invitation_lists')) {
            $this->forge->dropColumn('invitation_lists', 'blast_by');
        }
        if ($this->db->fieldExists('blast_error', 'invitation_lists')) {
            $this->forge->dropColumn('invitation_lists', 'blast_error');
        }
        if ($this->db->fieldExists('blast_at', 'invitation_lists')) {
            $this->forge->dropColumn('invitation_lists', 'blast_at');
        }
        if ($this->db->fieldExists('blast_status', 'invitation_lists')) {
            $this->forge->dropColumn('invitation_lists', 'blast_status');
        }

        // 2. Drop table invitation_messages
        $this->forge->dropTable('invitation_messages', true);
    }

    public function down()
    {
        // Re-add blast columns if rolled back
        if (!$this->db->fieldExists('blast_status', 'invitation_lists')) {
            $this->forge->addColumn('invitation_lists', [
                'blast_status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['unsend', 'sent', 'failed'],
                    'default'    => 'unsend',
                    'after'      => 'notes',
                ],
                'blast_at' => [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'blast_status',
                ],
                'blast_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'blast_at',
                ],
                'blast_error' => [
                    'type'  => 'TEXT',
                    'null'  => true,
                    'after' => 'blast_by',
                ],
            ]);
        }
    }
}

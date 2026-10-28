<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlastByToInvitationListsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('invitation_lists', [
            'blast_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'blast_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('invitation_lists', 'blast_by');
    }
}

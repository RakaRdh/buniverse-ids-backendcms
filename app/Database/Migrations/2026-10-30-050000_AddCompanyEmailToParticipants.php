<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyEmailToParticipants extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('participants')) {
            if (!$this->db->fieldExists('company_email', 'participants')) {
                $this->forge->addColumn('participants', [
                    'company_email' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 150,
                        'null'       => true,
                        'after'      => 'company_name',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('participants')) {
            if ($this->db->fieldExists('company_email', 'participants')) {
                $this->forge->dropColumn('participants', 'company_email');
            }
        }
    }
}

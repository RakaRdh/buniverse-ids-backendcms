<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Add2FaToAdminUsers extends Migration
{
    public function up()
    {
        $fields = [
            'auth_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'role',
            ],
            'auth_expired' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'auth_code',
            ],
            'next_verify' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'auth_expired',
            ],
        ];

        $this->forge->addColumn('admin_users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('admin_users', ['auth_code', 'auth_expired', 'next_verify']);
    }
}

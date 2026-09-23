<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToSponsorsPartnerships extends Migration
{
    public function up()
    {
        $fields = [
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'logo_url',
            ],
        ];

        $this->forge->addColumn('sponsors_partnerships', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sponsors_partnerships', 'is_active');
    }
}

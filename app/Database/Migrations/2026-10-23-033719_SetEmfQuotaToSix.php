<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetEmfQuotaToSix extends Migration
{
    public function up()
    {
        $this->db->table('events')->where('slug', 'emf-2026')->update(['quota' => 6]);
    }

    public function down()
    {
        $this->db->table('events')->where('slug', 'emf-2026')->update(['quota' => null]);
    }
}

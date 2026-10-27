<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMapAdminRoleToAdminUsers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('admin_users')) {
            $this->db->query("ALTER TABLE `admin_users` MODIFY COLUMN `role` ENUM('superadmin', 'editor', 'receptionist', 'map_admin') NOT NULL DEFAULT 'editor'");
        }
    }

    public function down()
    {
        if ($this->db->tableExists('admin_users')) {
            $this->db->query("ALTER TABLE `admin_users` MODIFY COLUMN `role` ENUM('superadmin', 'editor', 'receptionist') NOT NULL DEFAULT 'editor'");
        }
    }
}

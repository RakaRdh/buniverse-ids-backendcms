<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'          => 'Raka Herdika',
            'username'      => 'rakaherdika',
            'email'         => 'rakaherdika15@gmail.com',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'role'          => 'superadmin',
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $builder = $db->table('admin_users');

        $existing = $builder->where('email', $data['email'])->orWhere('username', $data['username'])->get()->getRow();
        if (!$existing) {
            $builder->insert($data);
            echo "Admin user {$data['username']} ({$data['email']}) created successfully.\n";
        } else {
            $builder->where('id', $existing->id)->update([
                'username'      => $data['username'],
                'password_hash' => $data['password_hash'],
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            echo "Admin user {$data['username']} ({$data['email']}) updated successfully.\n";
        }
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\AdminUserModel;

class UserController extends BaseAdminController
{
    public function index()
    {
        $userModel = new AdminUserModel();
        $this->data['users'] = $userModel->orderBy('id', 'ASC')->findAll();
        $this->data['pageTitle'] = 'Admin User Management';
        $this->data['activeMenu'] = 'users';

        return $this->render('admin/users/index', $this->data);
    }

    public function save()
    {
        $userModel = new AdminUserModel();
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');

        $existing = $userModel->where('email', $email)->orWhere('username', $username)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Username or Email already registered.');
        }

        $userModel->insert([
            'name' => $this->request->getPost('name'),
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role') ?: 'editor',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/users'))->with('success', 'Admin user created successfully.');
    }

    public function delete($id)
    {
        $userModel = new AdminUserModel();
        if ((int) $id === (int) $this->currentAdmin['id']) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userModel->delete($id);
        return redirect()->to(base_url('admin/users'))->with('success', 'User account deleted.');
    }
}

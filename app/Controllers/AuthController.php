<?php

namespace App\Controllers;

use App\Models\AdminUserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        $session = session();
        if ($session->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/events'));
        }

        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session();
        $login = trim((string)$this->request->getPost('login'));
        $password = (string)$this->request->getPost('password');

        if (empty($login) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Please enter your username and password.');
        }

        $userModel = new AdminUserModel();
        $user = $userModel->findByLogin($login);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }

        if ((int)$user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Your account has been deactivated.');
        }

        if (!password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }

        $session->set([
            'admin_logged_in' => true,
            'admin_id'        => (int)$user['id'],
            'admin_name'      => $user['name'],
            'admin_username'  => $user['username'] ?? '',
            'admin_email'     => $user['email'],
            'admin_role'      => $user['role'] ?? 'superadmin',
            'admin_user'      => [
                'id'       => (int)$user['id'],
                'name'     => $user['name'],
                'username' => $user['username'] ?? '',
                'email'    => $user['email'],
                'role'     => $user['role'] ?? 'superadmin',
            ],
        ]);

        return redirect()->to(base_url('admin/events'))->with('success', "Welcome back, {$user['name']}!");
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('auth/login'))->with('success', 'You have been logged out.');
    }
}

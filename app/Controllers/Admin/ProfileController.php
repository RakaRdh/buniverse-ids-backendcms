<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\AdminUserModel;

class ProfileController extends BaseAdminController
{
    public function index()
    {
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? null;
        if (!$userId) {
            return redirect()->to(base_url('auth/login'));
        }

        $userModel = new AdminUserModel();
        $user = $userModel->find($userId);

        $this->data['user']       = $user;
        $this->data['pageTitle']  = 'My Profile & Security';
        $this->data['activeMenu'] = 'profile';

        // Deteksi konteks portal dari session (tanpa query param)
        if (session()->get('cms_context') === 'portal') {
            $this->data['isPortalMode'] = true;
            $this->data['fromPortal'] = true;
        }

        return $this->render('admin/profile/index', $this->data);
    }

    public function updatePassword()
    {
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? null;
        if (!$userId) {
            return redirect()->to(base_url('auth/login'));
        }

        $currentPassword = (string)$this->request->getPost('current_password');
        $newPassword     = (string)$this->request->getPost('new_password');
        $confirmPassword = (string)$this->request->getPost('confirm_password');

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return redirect()->back()->with('error', 'All password fields are required.');
        }

        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'New password must be at least 6 characters long.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'New password and confirmation do not match.');
        }

        $userModel = new AdminUserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to(base_url('auth/login'));
        }

        if (!password_verify($currentPassword, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $userModel->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/profile'))->with('success', 'Password updated successfully!');
    }
}

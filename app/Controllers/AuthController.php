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
        $login = trim((string) $this->request->getPost('login'));
        $password = (string) $this->request->getPost('password');

        if (empty($login) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Please enter your username and password.');
        }

        $userModel = new AdminUserModel();
        $user = $userModel->findByLogin($login);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }

        if ((int) $user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Your account has been deactivated.');
        }

        if (!password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }

        // Check 2FA Grace Period: If verified within last 1 day, skip OTP
        $now = time();
        $nextVerifyTime = !empty($user['next_verify']) ? strtotime($user['next_verify']) : 0;

        if ($nextVerifyTime > $now) {
            $this->setAuthenticatedSession($user);
            return redirect()->to(base_url('admin/events'))->with('success', "Welcome back, {$user['name']}!");
        }

        // 2FA is required: Generate 6-digit OTP
        $authCode = random_int(100000, 999999);
        $authExpired = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $userModel->update($user['id'], [
            'auth_code'    => (string) $authCode,
            'auth_expired' => $authExpired,
        ]);

        // Send OTP email via Brevo SMTP
        $this->sendAuthCodeEmail($user, $authCode);

        // Set pending 2FA session
        $session->set([
            'pending_2fa_user_id' => (int) $user['id'],
            'pending_2fa_email'   => $user['email'] ?? '',
            'pending_2fa_name'    => $user['name'] ?: ($user['username'] ?: 'Admin'),
        ]);

        return redirect()->to(base_url('auth/verify-code'));
    }

    public function verifyCode()
    {
        $session = session();
        if ($session->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/events'));
        }

        $pendingUserId = $session->get('pending_2fa_user_id');
        if (!$pendingUserId) {
            return redirect()->to(base_url('auth/login'));
        }

        $email = (string) $session->get('pending_2fa_email');
        $maskedEmail = $this->maskEmail($email);

        return view('auth/verify_code', [
            'maskedEmail' => $maskedEmail,
        ]);
    }

    public function processVerifyCode()
    {
        $session = session();
        $pendingUserId = $session->get('pending_2fa_user_id');

        if (!$pendingUserId) {
            return redirect()->to(base_url('auth/login'))->with('error', 'Session expired. Please sign in again.');
        }

        $otpInput = trim((string) $this->request->getPost('otp_code'));
        if (empty($otpInput)) {
            return redirect()->back()->with('error', 'Please enter the 6-digit verification code.');
        }

        $userModel = new AdminUserModel();
        $user = $userModel->find($pendingUserId);

        if (!$user || empty($user['auth_code'])) {
            return redirect()->to(base_url('auth/login'))->with('error', 'Invalid verification session. Please sign in again.');
        }

        // Validate expiry
        $now = time();
        $expiredTime = !empty($user['auth_expired']) ? strtotime($user['auth_expired']) : 0;

        if ($expiredTime < $now) {
            return redirect()->back()->with('error', 'The verification code has expired. Please click "Resend Code".');
        }

        // Validate code
        if (trim($user['auth_code']) !== $otpInput) {
            return redirect()->back()->with('error', 'Invalid verification code. Please check and try again.');
        }

        // OTP Verified successfully: set 1-day grace period, clear temporary OTP
        $nextVerify = date('Y-m-d H:i:s', strtotime('+1 day'));

        $userModel->update($user['id'], [
            'auth_code'    => null,
            'auth_expired' => null,
            'next_verify'  => $nextVerify,
        ]);

        // Clean up pending session & authenticate
        $session->remove(['pending_2fa_user_id', 'pending_2fa_email', 'pending_2fa_name']);
        $this->setAuthenticatedSession($user);

        return redirect()->to(base_url('admin/events'))->with('success', "Welcome back, {$user['name']}!");
    }

    public function resendCode()
    {
        $session = session();
        $pendingUserId = $session->get('pending_2fa_user_id');

        if (!$pendingUserId) {
            return redirect()->to(base_url('auth/login'));
        }

        $userModel = new AdminUserModel();
        $user = $userModel->find($pendingUserId);

        if (!$user) {
            return redirect()->to(base_url('auth/login'));
        }

        // Generate new OTP & update expiration
        $authCode = random_int(100000, 999999);
        $authExpired = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $userModel->update($user['id'], [
            'auth_code'    => (string) $authCode,
            'auth_expired' => $authExpired,
        ]);

        // Send email via Brevo SMTP
        $this->sendAuthCodeEmail($user, $authCode);

        return redirect()->back()->with('success', 'A new verification code has been sent to your email.');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('auth/login'))->with('success', 'You have been logged out.');
    }

    /**
     * Helper to set authenticated admin session
     */
    protected function setAuthenticatedSession(array $user): void
    {
        $session = session();
        $session->regenerate(true);

        $session->set([
            'admin_logged_in' => true,
            'admin_id'        => (int) $user['id'],
            'admin_name'      => $user['name'],
            'admin_username'  => $user['username'] ?? '',
            'admin_email'     => $user['email'],
            'admin_role'      => $user['role'] ?? 'superadmin',
            'admin_user'      => [
                'id'       => (int) $user['id'],
                'name'     => $user['name'],
                'username' => $user['username'] ?? '',
                'email'    => $user['email'],
                'role'     => $user['role'] ?? 'superadmin',
            ],
        ]);
    }

    /**
     * Helper to send 2FA OTP Email via Brevo SMTP
     */
    protected function sendAuthCodeEmail(array $user, int $authCode): bool
    {
        try {
            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setSubject('Security Verification Code - IDS Executive CMS [' . $authCode . ']');

            $body  = '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; max-width: 520px; margin: 0 auto; padding: 32px 28px; border: 1px solid #e2e8f0; border-radius: 16px; background-color: #ffffff; color: #0b1f2e;">';
            $body .= '<div style="margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 18px;">';
            $body .= '<h2 style="color: #0b516c; margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.02em;">IDS Executive CMS</h2>';
            $body .= '<p style="color: #64748b; font-size: 12px; margin: 4px 0 0 0; font-weight: 500;">Investor Daily Summit — Two-Step Verification</p>';
            $body .= '</div>';
            $body .= '<p style="font-size: 14.5px; line-height: 1.6; margin: 0 0 12px 0;">Hello <strong>' . esc($user['name']) . '</strong>,</p>';
            $body .= '<p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 24px 0;">Use the following 6-digit security code to complete your login request to the IDS Executive CMS:</p>';
            $body .= '<div style="text-align: center; margin: 28px 0;">';
            $body .= '<span style="display: inline-block; font-size: 34px; font-weight: 800; font-family: \'JetBrains Mono\', Consolas, Monaco, monospace; letter-spacing: 8px; padding: 14px 32px; background-color: #f1f8f6; color: #0b516c; border-radius: 10px; border: 1.5px dashed #436d63;">' . $authCode . '</span>';
            $body .= '</div>';
            $body .= '<p style="color: #64748b; font-size: 13px; line-height: 1.5; margin: 0 0 8px 0;">This code is valid for <strong>10 minutes</strong>. Once verified, this device will be remembered for <strong>24 hours</strong>.</p>';
            $body .= '<p style="color: #94a3b8; font-size: 12px; line-height: 1.5; margin: 0 0 24px 0;">If you did not attempt to sign in, please ignore this email and verify your account security immediately.</p>';
            $body .= '<hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0 16px 0;">';
            $body .= '<p style="color: #94a3b8; font-size: 11px; margin: 0; text-align: center;">&copy; ' . date('Y') . ' Investor Daily Summit. All rights reserved.</p>';
            $body .= '</div>';

            $email->setMessage($body);
            $sent = $email->send();
            if (!$sent) {
                log_message('error', 'Failed sending 2FA Email via Brevo SMTP: ' . $email->printDebugger(['headers', 'subject']));
            }
            return $sent;
        } catch (\Throwable $e) {
            log_message('error', 'Error in sendAuthCodeEmail: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper to mask email address for privacy
     */
    protected function maskEmail(string $email): string
    {
        if (empty($email) || !str_contains($email, '@')) {
            return 'your registered email';
        }

        $parts = explode('@', $email, 2);
        $name = $parts[0];
        $domain = $parts[1];

        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } else {
            $maskedName = substr($name, 0, 1) . str_repeat('*', $len - 2) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }
}

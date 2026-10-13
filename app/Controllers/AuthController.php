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

        // 1. Google reCAPTCHA Verification
        $recaptchaToken = (string) $this->request->getPost('g-recaptcha-response');
        if (!$this->verifyRecaptcha($recaptchaToken)) {
            return redirect()->back()->withInput()->with('error', 'Captcha verification failed. Please complete the "I\'m not a robot" checkbox.');
        }

        // 2. Validate Credentials Input
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

        // SEC-04: Regenerate session ID upon authentication to prevent session fixation
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

        return redirect()->to(base_url('admin/events'))->with('success', "Welcome back, {$user['name']}!");
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('auth/login'))->with('success', 'You have been logged out.');
    }

    /**
     * Helper to verify Google reCAPTCHA response token
     */
    protected function verifyRecaptcha(?string $token): bool
    {
        $secretKey = env('RECAPTCHA_SECRET_KEY') ?: getenv('RECAPTCHA_SECRET_KEY');

        // If no secret key is configured, bypass check (e.g. offline dev environment)
        if (empty($secretKey)) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $client = \Config\Services::curlrequest([
                'timeout' => 5.0,
                'verify'  => false, // Avoid Windows cURL SSL CA bundle missing error
            ]);
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $secretKey,
                    'response' => $token,
                    'remoteip' => $this->request->getIPAddress(),
                ],
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                if (!empty($data['success']) && $data['success'] === true) {
                    return true;
                }
                log_message('error', 'Google reCAPTCHA verification rejected: ' . json_encode($data));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Google reCAPTCHA verification error: ' . $e->getMessage());
        }

        return false;
    }
}

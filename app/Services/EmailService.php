<?php

namespace App\Services;

class EmailService
{
    /**
     * Send rendered HTML email using CodeIgniter's Email Service with SMTP configuration
     */
    public static function send(string $to, string $subject, string $htmlMessage): array
    {
        $email = \Config\Services::email();

        $smtpHost = env('SMTP_HOST', 'smtp-relay.brevo.com');
        $smtpPort = (int) env('SMTP_PORT', 465);
        $smtpUser = env('SMTP_USER', '');
        $smtpPass = env('SMTP_PASS', '');
        $smtpCrypto = env('SMTP_CRYPTO', 'ssl');
        $fromEmail = env('SMTP_FROM', 'no-reply@b-universe.id');
        $fromName  = env('SMTP_FROM_NAME', 'Malam Apresiasi 2026');

        $config = [
            'protocol'   => 'smtp',
            'SMTPHost'   => $smtpHost,
            'SMTPPort'   => $smtpPort,
            'SMTPUser'   => $smtpUser,
            'SMTPPass'   => $smtpPass,
            'SMTPCrypto' => $smtpCrypto,
            'mailType'   => 'html',
            'charset'    => 'utf-8',
            'wordWrap'   => true,
            'newline'    => "\r\n",
            'CRLF'       => "\r\n",
            'timeout'    => 15,
        ];

        $email->initialize($config);
        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($htmlMessage);

        try {
            if ($email->send(false)) {
                return ['success' => true];
            } else {
                $debugger = $email->printDebugger(['headers', 'subject']);
                return ['success' => false, 'error' => strip_tags($debugger)];
            }
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

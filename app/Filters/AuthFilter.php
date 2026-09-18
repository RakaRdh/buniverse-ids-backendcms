<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('admin_logged_in')) {
            return redirect()->to(base_url('auth/login'))->with('error', 'Please log in to continue.');
        }

        if ($arguments && in_array('superadmin', $arguments, true)) {
            if ($session->get('admin_role') !== 'superadmin') {
                return redirect()->to(base_url('admin/dashboard'))->with('error', 'Unauthorized access.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action required after execution
    }
}

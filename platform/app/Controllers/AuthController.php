<?php

namespace App\Controllers;

use Core\Controller;
use App\Services\AuthService;
use App\Helpers\Flash;
use App\Helpers\Redirect;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    // ─── Login ──────────────────────────────────────────

    public function loginForm(): void
    {
        if ($this->session->isLoggedIn()) {
            $this->redirect($this->getDashboardUrl());
            return;
        }
        $this->view('auth.login', ['title' => 'Login']);
    }

    public function login(): void
    {
        $errors = $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            Redirect::withErrors($errors, '/login');
            return;
        }

        $result = $this->authService->login(
            $this->request->post('email'),
            $this->request->post('password')
        );

        if (!$result['success']) {
            Flash::error($result['error']);
            Flash::old($this->request->allPost());
            $this->redirect('/login');
            return;
        }

        Flash::success('Welcome back, ' . $result['user']['first_name'] . '!');

        // Redirect to intended URL or dashboard
        $intended = $this->session->getFlash('intended_url');
        $this->redirect($intended ?: $this->getDashboardUrl());
    }

    // ─── Register ───────────────────────────────────────

    public function registerForm(): void
    {
        if ($this->session->isLoggedIn()) {
            $this->redirect($this->getDashboardUrl());
            return;
        }
        $this->view('auth.register', ['title' => 'Create Account']);
    }

    public function register(): void
    {
        $errors = $this->validate([
            'first_name' => 'required|min:2|max:100',
            'last_name'  => 'required|min:2|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:8|confirmed',
            'phone'      => 'phone',
        ]);

        if (!empty($errors)) {
            Redirect::withErrors($errors, '/register');
            return;
        }

        $result = $this->authService->register([
            'first_name' => $this->request->post('first_name'),
            'last_name'  => $this->request->post('last_name'),
            'email'      => $this->request->post('email'),
            'password'   => $this->request->post('password'),
            'phone'      => $this->request->post('phone'),
        ]);

        if (!$result['success']) {
            Flash::error($result['error']);
            Flash::old($this->request->allPost());
            $this->redirect('/register');
            return;
        }

        Flash::success('Account created successfully! Welcome to Touristik.');
        $this->redirect('/account');
    }

    // ─── Forgot Password ────────────────────────────────

    public function forgotForm(): void
    {
        $this->view('auth.forgot-password', ['title' => 'Forgot Password']);
    }

    public function forgotPassword(): void
    {
        $errors = $this->validate(['email' => 'required|email']);

        if (!empty($errors)) {
            Redirect::withErrors($errors, '/forgot-password');
            return;
        }

        $result = $this->authService->requestPasswordReset(
            $this->request->post('email')
        );

        Flash::success($result['message']);
        $this->redirect('/forgot-password');
    }

    // ─── Reset Password ─────────────────────────────────

    public function resetForm(string $token): void
    {
        $this->view('auth.reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
        ]);
    }

    public function resetPassword(): void
    {
        $errors = $this->validate([
            'token'    => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!empty($errors)) {
            Redirect::withErrors($errors);
            return;
        }

        $result = $this->authService->resetPassword(
            $this->request->post('token'),
            $this->request->post('password')
        );

        if (!$result['success']) {
            Flash::error($result['error']);
            $this->redirect('/forgot-password');
            return;
        }

        Flash::success($result['message']);
        $this->redirect('/login');
    }

    // ─── Logout ─────────────────────────────────────────

    public function logout(): void
    {
        $this->authService->logout();
        session_start(); // Restart for flash message
        Flash::success('You have been logged out.');
        (new \Core\Response())->redirect('/login');
    }

    // ─── Agency Registration ────────────────────────────

    /**
     * GET /register/agency
     *
     * Show the B2B agency registration form.
     */
    public function agencyRegisterForm(): void
    {
        if ($this->session->isLoggedIn()) {
            $this->redirect($this->getDashboardUrl());
            return;
        }
        $this->view('auth.agency-register', ['title' => 'Apply for B2B Account — Touristik']);
    }

    /**
     * POST /register/agency
     *
     * Process agency registration: create agency (pending), create agent user, notify admin.
     */
    public function agencyRegister(): void
    {
        $errors = $this->validate([
            'company_name'  => 'required|min:2|max:255',
            'legal_name'    => 'max:255',
            'tax_id'        => 'max:50',
            'company_email' => 'required|email',
            'company_phone' => 'phone',
            'first_name'    => 'required|min:2|max:100',
            'last_name'     => 'required|min:2|max:100',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
        ]);

        if (!empty($errors)) {
            Redirect::withErrors($errors, '/register/agency');
            return;
        }

        $db = \Core\Database::getInstance();

        try {
            // Create the agency with pending status
            $db->query(
                "INSERT INTO agencies (name, legal_name, tax_id, email, phone, address, commission_rate, balance, payment_model, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, 10.00, 0.00, 'markup', 'pending', NOW(), NOW())",
                [
                    trim($this->request->post('company_name')),
                    trim($this->request->post('legal_name', '')),
                    trim($this->request->post('tax_id', '')),
                    trim($this->request->post('company_email')),
                    trim($this->request->post('company_phone', '')),
                    trim($this->request->post('address', '')),
                ]
            );
            $agencyId = (int) $db->lastInsertId();

            // Create the agent user linked to this agency
            $userId = \App\Models\User::register([
                'email'      => $this->request->post('email'),
                'password'   => $this->request->post('password'),
                'first_name' => $this->request->post('first_name'),
                'last_name'  => $this->request->post('last_name'),
                'phone'      => $this->request->post('company_phone', ''),
                'role'       => 'agent',
                'agency_id'  => $agencyId,
                'status'     => 'pending',
            ]);

            // Log activity
            \App\Services\ActivityService::log('agency.registered', 'agency', $agencyId, [
                'company_name' => $this->request->post('company_name'),
                'user_id'      => $userId,
            ]);

            // Notify admin users about the new application
            $admins = \App\Models\User::byRole('admin');
            foreach ($admins as $admin) {
                \App\Services\NotificationService::send(
                    (int) $admin['id'],
                    'system',
                    'New Agency Application',
                    trim($this->request->post('company_name')) . ' has applied for a B2B account. Review in Admin > Users.',
                    '/admin/users/' . $userId
                );
            }

            Flash::success('Your B2B application has been submitted! We will review it within 24-48 hours. You will receive an email once approved.');
            $this->redirect('/login');

        } catch (\Throwable $e) {
            error_log('Agency registration failed: ' . $e->getMessage());
            Flash::error('Registration failed. Please try again or contact support.');
            Flash::old($this->request->allPost());
            $this->redirect('/register/agency');
        }
    }

    // ─── Helpers ────────────────────────────────────────

    private function getDashboardUrl(): string
    {
        return match ($this->session->userRole()) {
            'admin', 'superadmin' => '/admin',
            'agent'               => '/agent',
            default               => '/account',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\SdUserModel;
use Throwable;

class SdAuth extends Security_Controller
{
    /**
     * @var SdUserModel
     */
    private $users;

    public function __construct()
    {
        $this->users = new SdUserModel();
    }

    /**
     * First screen of the SD Auth flow.
     */
    public function index()
    {
        return view('sd_auth/welcome', [
            'title' => 'Welcome to Efactor',
        ]);
    }
    /**
     * Signup
     */
    public function signup()
    {
        if (session()->get('isLoggedIn') === true) {
            return redirect()->to(site_url('sd-dashboard'));
        }

        if ($this->request->getMethod() === 'get') {
            return view('sd_auth/signup', [
                'title' => 'Create Account',
                'validation' => null,
                'extraErrors' => [],
            ]);
        }

        $payload = [
            'email' => strtolower(trim((string) $this->request->getPost('email'))),
            'phone' => preg_replace(
                '/\D+/',
                '',
                (string) $this->request->getPost('phone')
            ) ?? '',
            'password' => (string) $this->request->getPost('password'),
            'confirm_password' => (string) $this->request->getPost('confirm_password'),
            'terms' => (string) $this->request->getPost('terms'),
        ];

        $rules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|max_length[191]|valid_email|is_unique[sd_users.email]',
                'errors' => [
                    'required' => 'Email address is required.',
                    'valid_email' => 'Enter a valid email address.',
                    'is_unique' => 'This email address is already registered.',
                ],
            ],
            'phone' => [
                'label' => 'Mobile Number',
                'rules' => 'required|exact_length[10]|numeric|is_unique[sd_users.phone]',
                'errors' => [
                    'required' => 'Mobile number is required.',
                    'exact_length' => 'Mobile number must contain exactly 10 digits.',
                    'numeric' => 'Mobile number must contain digits only.',
                    'is_unique' => 'This mobile number is already registered.',
                ],
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]|max_length[72]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+/]',
                'errors' => [
                    'required' => 'Password is required.',
                    'min_length' => 'Password must be at least 8 characters.',
                    'max_length' => 'Password cannot exceed 72 characters.',
                    'regex_match' => 'Use uppercase, lowercase, number, and special character.',
                ],
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Please confirm your password.',
                    'matches' => 'Password confirmation does not match.',
                ],
            ],
            'terms' => [
                'label' => 'Terms & Conditions',
                'rules' => 'required|in_list[1]',
                'errors' => [
                    'required' => 'You must agree to the Terms & Conditions.',
                    'in_list' => 'You must agree to the Terms & Conditions.',
                ],
            ],
        ];

        if (! $this->validateData($payload, $rules)) {
            return $this->validationFailure($payload);
        }

        try {
            $created = $this->users->insert([
                'email' => $payload['email'],
                'phone' => $payload['phone'],
                'password' => password_hash($payload['password'], PASSWORD_DEFAULT),
                'status' => 1,
            ], true);

            if ($created === false) {
                $modelErrors = $this->users->errors();

                return $this->validationFailure(
                    $payload,
                    $modelErrors ?: [
                        'form' => 'Unable to create the account. Please try again.',
                    ]
                );
            }
        } catch (Throwable $exception) {
            log_message(
                'error',
                'Signup failed: {message}',
                ['message' => $exception->getMessage()]
            );

            return $this->validationFailure($payload, [
                'form' => 'Unable to create the account. The email or mobile number may already exist.',
            ]);
        }

        session()->setFlashdata('success', 'Account created successfully.');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Account created successfully.',
                'redirect' => site_url('sd-login'),
                'csrfHash' => csrf_hash(),
            ]);
        }

        return redirect()
            ->to(site_url('sd-login'))
            ->with('success', 'Account created successfully.');
    }

    /**
     * Login
     */
    public function login()
    {
        if (session()->get('isLoggedIn') === true) {
            return redirect()->to(site_url('sd-dashboard'));
        }

        if ($this->request->getMethod() === 'get') {
            return view('sd_auth/login', [
                'title' => 'Sign In',
                'validation' => null,
            ]);
        }

        $payload = [
            'identity' => trim((string) $this->request->getPost('identity')),
            'password' => (string) $this->request->getPost('password'),
        ];

        $rules = [
            'identity' => [
                'label' => 'Mobile number',
                'rules' => 'required|max_length[191]',
                'errors' => [
                    'required' => 'Mobile number is required.',
                ],
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|max_length[72]',
                'errors' => [
                    'required' => 'Password is required.',
                ],
            ],
        ];

        if (! $this->validateData($payload, $rules)) {
            return view('sd_auth/login', [
                'title' => 'Sign In',
                'validation' => $this->validator,
            ]);
        }

        $identity = strtolower($payload['identity']);

        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $user = $this->users
                ->where('email', $identity)
                ->first();
        } else {
            $phone = preg_replace('/\D+/', '', $identity);

            $user = $this->users
                ->where('phone', $phone)
                ->first();
        }

        if (
            ! $user
            || (int) $user['status'] !== 1
            || ! password_verify($payload['password'], $user['password'])
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Invalid email/mobile number or password.');
        }

        session()->regenerate(true);

        session()->set([
            'userId' => (int) $user['id'],
            'userEmail' => $user['email'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to(site_url('sd-dashboard'));
    }

    /**
     * Demo OTP sender used by the auth screens until the SMS gateway is connected.
     */
    public function sendOtp()
    {
        $phone = preg_replace('/\D+/', '', (string) $this->request->getPost('phone'));

        if (strlen($phone) !== 10) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Enter a valid 10-digit mobile number.',
                    'csrfHash' => csrf_hash(),
                ]);
        }

        session()->set('sd_demo_otp_' . $phone, '123456');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'OTP sent',
            'csrfHash' => csrf_hash(),
        ]);
    }

    /**
     * Placeholder social linking route for Google, Apple, and Facebook.
     */
    public function social($provider = '')
    {
        $provider = strtolower((string) $provider);
        $allowed = ['google', 'apple', 'facebook'];

        if (! in_array($provider, $allowed, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Unknown social provider.');
        }

        return redirect()
            ->to(site_url('sd-login'))
            ->with('success', ucfirst($provider) . ' linking can be connected here.');
    }
    /**
     * Dashboard
     */
    /**
     * Forgot password / reset password screen.
     */
    public function forgotPassword()
    {
        if ($this->request->getMethod() === 'get') {
            return view('sd_auth/forgot_password', [
                'title' => 'Reset Password',
                'validation' => null,
                'success' => null,
            ]);
        }

        $payload = [
            'phone' => preg_replace('/\D+/', '', (string) $this->request->getPost('phone')) ?? '',
            'otp' => preg_replace('/\D+/', '', (string) $this->request->getPost('otp')) ?? '',
            'password' => (string) $this->request->getPost('password'),
            'confirm_password' => (string) $this->request->getPost('confirm_password'),
        ];

        $rules = [
            'phone' => ['label' => 'Mobile Number', 'rules' => 'required|exact_length[10]|numeric'],
            'otp' => ['label' => 'OTP', 'rules' => 'permit_empty|exact_length[6]|numeric'],
            'password' => [
                'label' => 'New Password',
                'rules' => 'required|min_length[8]|max_length[72]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+/]',
            ],
            'confirm_password' => ['label' => 'Confirm Password', 'rules' => 'required|matches[password]'],
        ];

        if (! $this->validateData($payload, $rules)) {
            return view('sd_auth/forgot_password', [
                'title' => 'Reset Password',
                'validation' => $this->validator,
                'success' => null,
            ]);
        }

        return view('sd_auth/forgot_password', [
            'title' => 'Reset Password',
            'validation' => null,
            'success' => 'Password reset request received. OTP verification can now be connected to the SMS provider.',
        ]);
    }
    public function dashboard()
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()
                ->to(site_url('sd-login'))
                ->with('error', 'Please sign in to continue.');
        }

        return view('sd_auth/dashboard', [
            'title' => 'Dashboard',
            'email' => (string) session()->get('userEmail'),
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();

        return redirect()
            ->to(site_url('welcome'))
            ->with('success', 'You have been signed out.');
    }

    /**
     * Signup validation failure
     */
    private function validationFailure(
        array $payload,
        array $extraErrors = []
    ) {
        $errors = $this->validator !== null
            ? $this->validator->getErrors()
            : [];

        $errors = array_merge($errors, $extraErrors);

        if ($this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Please correct the highlighted fields.',
                    'errors' => $errors,
                    'csrfHash' => csrf_hash(),
                ]);
        }

        return view('sd_auth/signup', [
            'title' => 'Create Account',
            'validation' => $this->validator,
            'extraErrors' => $extraErrors,
        ]);
    }
}

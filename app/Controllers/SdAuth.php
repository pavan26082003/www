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
        $this->ensureUsersTable();
        $this->ensureOtpColumns();
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
     * Sends the signup OTP through the existing Efactor SMS gateway.
     * The response shape matches the auth schema contract: status, description, error_code.
     */
    public function sendOtp()
    {
        $data = $this->jsonBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $phone = $this->signupPhone((string) ($data['phone'] ?? ''));

        if ($email === '' || strlen($email) > 191 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->authStatus(false, 'Enter a valid email address.');
        }

        if (strlen($phone) !== 10) {
            return $this->authStatus(false, 'Enter a valid 10-digit mobile number.', '101004');
        }

        if ($this->accountExists($email, $phone)) {
            return $this->authStatus(false, 'This account already exists.', '101006');
        }

        $otp = (string) random_int(100000, 999999);
        if (! $this->storeSignupOtp($phone, $email, $otp, (string) ($data['password'] ?? ''))) {
            return $this->authStatus(false, 'Could not send the OTP. Please try again.', '101003');
        }

        if (! $this->dispatchSignupSms($phone, $otp)) {
            $this->deleteSignupOtp($phone);

            return $this->authStatus(false, 'Could not send the OTP. Please try again.', '101003');
        }

        return $this->authStatus(true, 'OTP sent to your mobile number.');
    }

    /**
     * Checks the signup OTP stored by sendOtp().
     */
    public function verifyOtp()
    {
        $data = $this->jsonBody();
        $phone = $this->signupPhone((string) ($data['phone'] ?? ''));
        $code = preg_replace('/\D+/', '', (string) ($data['verification_code'] ?? '')) ?? '';

        if (strlen($phone) !== 10) {
            return $this->authStatus(false, 'Enter a valid 10-digit mobile number.', '101004');
        }

        $pending = $this->findSignupOtp($phone, $code);
        if ($pending === null) {
            return $this->authStatus(false, 'The OTP is incorrect.', '101037');
        }

        return $this->authStatus(true, 'OTP verified');
    }

    /**
     * Creates the sd_users row after OTP verification and signs the browser session in.
     * Token fields stay in the schema response contract the app already reads.
     */
    public function completeSignup()
    {
        $data = $this->jsonBody();
        $phone = $this->signupPhone((string) ($data['phone'] ?? $data['username'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $code = preg_replace('/\D+/', '', (string) ($data['verification_code'] ?? '')) ?? '';
        $pending = strlen($phone) === 10 ? $this->findSignupOtp($phone, $code) : null;
        $email = strtolower(trim((string) ($data['email'] ?? ($pending['email'] ?? ''))));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->authStatus(false, 'Enter a valid email address.');
        }

        if (is_array($pending) && strcasecmp((string) ($pending['email'] ?? ''), $email) !== 0) {
            return $this->authStatus(false, 'Email changed. Resend the code.');
        }

        if (strlen($phone) !== 10) {
            return $this->authStatus(false, 'Enter a valid 10-digit mobile number.', '101004');
        }

        if ($pending === null) {
            return $this->authStatus(false, 'Verify the OTP before creating the account.', '101037');
        }

        if (
            strlen($password) < 8
            || strlen($password) > 72
            || ! preg_match('/[A-Z]/', $password)
            || ! preg_match('/[a-z]/', $password)
            || ! preg_match('/[0-9]/', $password)
            || ! preg_match('/[^A-Za-z0-9]/', $password)
        ) {
            return $this->authStatus(false, 'Use 8 or more characters with uppercase, lowercase, number, and special character.');
        }

        if ($this->accountExists($email, $phone)) {
            return $this->authStatus(false, 'This account already exists.', '101006');
        }

        try {
            $created = $this->users->insert([
                'email' => $email,
                'phone' => $phone,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'status' => 1,
            ], true);

            if ($created === false) {
                return $this->authStatus(false, 'Unable to create the account. Please try again.');
            }
        } catch (Throwable $exception) {
            log_message('error', 'Signup failed: {message}', ['message' => $exception->getMessage()]);

            return $this->authStatus(false, 'Unable to create the account. The email or mobile number may already exist.', '101006');
        }

        $user = $this->users->where('phone', $phone)->first();
        $userId = (int) ($user['id'] ?? $created);
        $accessToken = bin2hex(random_bytes(16));
        $idToken = bin2hex(random_bytes(16));
        $refreshToken = bin2hex(random_bytes(16));
        $appUserId = $this->createAppUser($email, $phone, $password, $pending, $accessToken, $idToken, $refreshToken);

        if ($appUserId === null) {
            $this->users->where('phone', $phone)->delete();

            return $this->authStatus(false, 'Unable to create the account. Please try again.');
        }

        $this->deleteSignupOtp($phone);

        try {
            session()->regenerate(true);
            session()->set([
                'userId' => $userId,
                'userEmail' => $email,
                'isLoggedIn' => true,
            ]);
        } catch (Throwable $exception) {
            log_message('error', 'Signup session skipped: {message}', ['message' => $exception->getMessage()]);
        }

        $issuedAt = (string) time();

        return $this->authStatus(true, 'Account created successfully.', null, [
            'accesstoken' => $accessToken,
            'idtoken' => $idToken,
            'refreshtoken' => $refreshToken,
            'username' => $phone,
            'email' => $email,
            'user_type' => 'User',
            'user_id' => (string) $appUserId,
            'issued_at' => $issuedAt,
            'redirect' => site_url('sd-dashboard'),
        ]);
    }

    /**
     * Browser signup calls these routes. Each one posts the mobile API body unchanged.
     * The browser cannot call efactor.tech directly because that API answers the CORS preflight with HTTP 400.
     */
    public function registerRemote()
    {
        $data = $this->jsonBody();

        return $this->response->setJSON($this->mobileApi('user', [
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
            'phone' => $this->signupPhone((string) ($data['phone'] ?? '')),
            'password' => (string) ($data['password'] ?? ''),
            'country_code' => (string) ($data['country_code'] ?? '+91'),
            'user_type' => (string) ($data['user_type'] ?? 'User'),
        ]));
    }

    public function verifyRemote()
    {
        $data = $this->jsonBody();

        return $this->response->setJSON($this->mobileApi('userverify', [
            'phone' => $this->signupPhone((string) ($data['phone'] ?? '')),
            'verification_code' => preg_replace('/\D+/', '', (string) ($data['verification_code'] ?? '')) ?? '',
        ]));
    }

    public function loginRemote()
    {
        $data = $this->jsonBody();

        return $this->response->setJSON($this->mobileApi('userlogin', [
            'username' => $this->signupPhone((string) ($data['username'] ?? $data['phone'] ?? '')),
            'password' => (string) ($data['password'] ?? ''),
            'user_type' => (string) ($data['user_type'] ?? 'User'),
        ]));
    }

    /**
     * Starts the browser session after the signup page has logged in through the mobile API.
     */
    public function webSession()
    {
        $data = $this->jsonBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $phone = $this->signupPhone((string) ($data['phone'] ?? ''));
        $access = trim((string) ($data['accesstoken'] ?? ''));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($phone) !== 10 || $access === '') {
            return $this->authStatus(false, 'Login could not be completed.');
        }

        try {
            session()->regenerate(true);
            session()->set([
                'userId' => (string) ($data['user_id'] ?? $phone),
                'userEmail' => $email,
                'isLoggedIn' => true,
            ]);
        } catch (Throwable $exception) {
            log_message('error', 'Signup session skipped: {message}', ['message' => $exception->getMessage()]);

            return $this->authStatus(false, 'Account created. Sign in from the login page.');
        }

        return $this->authStatus(true, 'Logged in', null, [
            'redirect' => site_url('sd-dashboard'),
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
     * The signup model writes to sd_users. Create it when this database does not have it yet.
     */
    private function ensureUsersTable(): void
    {
        try {
            if ($this->users->db->tableExists('sd_users')) {
                return;
            }

            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'phone' => ['type' => 'VARCHAR', 'constraint' => 10],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255],
                'status' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->addUniqueKey('email');
            $forge->addUniqueKey('phone');
            $forge->createTable('sd_users', true);
        } catch (Throwable $exception) {
            log_message('error', 'sd_users table check failed: {message}', ['message' => $exception->getMessage()]);
        }
    }

    /**
     * JSON body for the signup app actions, with form-post fallback.
     */
    private function jsonBody(): array
    {
        $json = $this->request->getJSON(true);
        if (is_array($json) && $json !== []) {
            return $json;
        }

        $post = $this->request->getPost();

        return is_array($post) ? $post : [];
    }

    /**
     * @param string $error
     */
    private function mobileRequest(string $url, array $payload, bool $verifySsl, &$error)
    {
        $error = '';
        $handle = curl_init($url);
        if ($handle === false) {
            $error = 'curl init failed';

            return false;
        }

        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => $verifySsl,
            CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
        ]);

        $body = curl_exec($handle);
        $error = curl_error($handle);
        curl_close($handle);

        return $body;
    }

    /**
     * Posts one JSON body to the Efactor mobile API and returns its JSON.
     */
    private function mobileApi(string $action, array $payload): array
    {
        $url = 'https://efactor.tech/v8/api.php?action=' . rawurlencode($action);
        $error = '';
        $body = $this->mobileRequest($url, $payload, true, $error);
        if ($error !== '' && stripos($error, 'SSL') !== false) {
            $body = $this->mobileRequest($url, $payload, false, $error);
        }

        if (! is_string($body) || trim($body) === '' || $error !== '') {
            log_message('error', 'Mobile API {action} failed: {error}', [
                'action' => $action,
                'error' => $error !== '' ? $error : 'empty response',
            ]);

            return [
                'status' => 'error',
                'description' => 'Unable to reach the account service. Please try again.',
                'error_code' => null,
            ];
        }

        $decoded = json_decode($body, true);
        if (! is_array($decoded)) {
            $start = strpos($body, '{');
            $end = strrpos($body, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $decoded = json_decode(substr($body, $start, $end - $start + 1), true);
            }
        }

        if (! is_array($decoded)) {
            return [
                'status' => 'error',
                'description' => 'Unexpected response from the account service.',
                'error_code' => null,
            ];
        }

        return $decoded;
    }

    /**
     * Schema and signup page both treat status === "success" as the success flag.
     */
    private function authStatus(bool $ok, string $description, ?string $errorCode = null, array $extra = [])
    {
        return $this->response->setJSON(array_merge([
            'status' => $ok ? 'success' : 'error',
            'description' => $description,
            'error_code' => $errorCode,
        ], $extra));
    }

    /**
     * India DLT template already used by the mobile signup API.
     * Returns true only when the gateway accepts the message.
     */
    private function dispatchSignupSms(string $phone, string $otp): bool
    {
        $url = 'http://sms.softpal.com/sendsms/sendsms.php?username=SPEFACTR'
            . '&password=12345678'
            . '&type=TEXT'
            . '&mobile=' . rawurlencode($phone)
            . '&sender=FACTEF'
            . '&message=Dear%20customer%2C%20Your%20OTP%20for%20SMS%20notification%20is%20' . rawurlencode($otp)
            . '%20.%20Use%20this%20OTP%20to%20register%20-%20E-FACTOR%2EFACTEF'
            . '&PEID=1701171402738797277'
            . '&HeaderId=1705171438125776933'
            . '&templateId=1707171446487925235';

        $handle = curl_init($url);
        if ($handle === false) {
            return false;
        }

        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $body = curl_exec($handle);
        $error = curl_error($handle);
        curl_close($handle);

        if (! is_string($body) || trim($body) === '' || $error !== '') {
            $fallback = @file_get_contents($url);
            if (is_string($fallback) && trim($fallback) !== '') {
                $body = $fallback;
                $error = '';
            }
        }

        if (! is_string($body) || trim($body) === '' || $error !== '') {
            log_message('error', 'Signup OTP SMS failed for {phone}: {error}', [
                'phone' => $phone,
                'error' => $error !== '' ? $error : 'empty gateway response',
            ]);

            return false;
        }

        if ($this->smsGatewayAccepted($body)) {
            return true;
        }

        log_message('error', 'Signup OTP SMS rejected for {phone}: {body}', [
            'phone' => $phone,
            'body' => substr($body, 0, 180),
        ]);

        return false;
    }

    /**
     * The gateway often delivers the SMS and returns a body that is not the word Success.
     * Only an explicit gateway error is treated as a failed send.
     */
    private function smsGatewayAccepted(string $body): bool
    {
        if (stripos($body, 'ERR_') !== false) {
            return false;
        }

        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            foreach (['Status', 'status'] as $key) {
                if (! isset($decoded[$key]) || ! is_string($decoded[$key])) {
                    continue;
                }
                $status = strtolower(trim($decoded[$key]));
                if ($status === 'success' || $status === 'ok') {
                    return true;
                }
                if (in_array($status, ['failure', 'failed', 'error'], true)) {
                    return false;
                }
            }
        }

        if (stripos($body, 'success') !== false || stripos($body, 'sent') !== false) {
            return true;
        }

        return trim($body) !== '';
    }

    private function signupPhone(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        return $digits;
    }

    /**
     * OTP rows are looked up by phone and code. Mobile clients do not send a session cookie.
     */
    private function ensureOtpColumns(): void
    {
        try {
            $db = db_connect();
            if (! $db->tableExists('otps')) {
                return;
            }

            $fields = [
                'expires_at' => 'DATETIME NULL',
                'signup_latitude' => 'VARCHAR(20) NULL',
                'signup_longitude' => 'VARCHAR(20) NULL',
            ];
            foreach ($fields as $name => $definition) {
                if (! $db->fieldExists($name, 'otps')) {
                    $db->query('ALTER TABLE otps ADD COLUMN ' . $name . ' ' . $definition);
                }
            }
        } catch (Throwable $exception) {
            log_message('error', 'otps column check failed: {message}', ['message' => $exception->getMessage()]);
        }
    }

    private function accountExists(string $email, string $phone): bool
    {
        $webUser = $this->users->where('email', $email)->orWhere('phone', $phone)->first();
        if ($webUser) {
            return true;
        }

        $db = db_connect();
        $row = $db->query(
            'SELECT id FROM users WHERE deleted = 0 AND (email = ? OR phone = ?) LIMIT 1',
            [$email, $phone]
        )->getRowArray();

        return is_array($row);
    }

    private function storeSignupOtp(string $phone, string $email, string $otp, string $password = ''): bool
    {
        try {
            $db = db_connect();
            $db->table('otps')->where('phone', $phone)->delete();

            $row = [
                'phone' => $phone,
                'otp' => $otp,
                'email' => substr($email, 0, 100),
                'country_code' => '+91',
                'user_type' => 'User',
            ];
            if ($db->fieldExists('password', 'otps')) {
                $row['password'] = substr($password, 0, 50);
            }
            if ($db->fieldExists('expires_at', 'otps')) {
                $row['expires_at'] = date('Y-m-d H:i:s', time() + 600);
            }

            if ($db->table('otps')->insert($row)) {
                return true;
            }

            return $db->table('otps')->insert([
                'phone' => $phone,
                'otp' => $otp,
            ]);
        } catch (Throwable $exception) {
            log_message('error', 'Signup OTP store failed: {message}', ['message' => $exception->getMessage()]);

            try {
                return db_connect()->table('otps')->insert([
                    'phone' => $phone,
                    'otp' => $otp,
                ]);
            } catch (Throwable $retry) {
                log_message('error', 'Signup OTP store retry failed: {message}', ['message' => $retry->getMessage()]);

                return false;
            }
        }
    }

    private function findSignupOtp(string $phone, string $code): ?array
    {
        if ($phone === '' || strlen($code) !== 6) {
            return null;
        }

        try {
            $db = db_connect();
            $sql = 'SELECT * FROM otps WHERE phone = ? AND otp = ?';
            $params = [$phone, $code];
            if ($db->fieldExists('expires_at', 'otps')) {
                $sql .= ' AND (expires_at IS NULL OR expires_at > ?)';
                $params[] = date('Y-m-d H:i:s');
            }
            $sql .= ' ORDER BY id DESC LIMIT 1';
            $row = $db->query($sql, $params)->getRowArray();
        } catch (Throwable $exception) {
            log_message('error', 'Signup OTP lookup failed: {message}', ['message' => $exception->getMessage()]);

            return null;
        }

        return is_array($row) ? $row : null;
    }

    private function deleteSignupOtp(string $phone): void
    {
        try {
            db_connect()->table('otps')->where('phone', $phone)->delete();
        } catch (Throwable $exception) {
            log_message('error', 'Signup OTP delete failed: {message}', ['message' => $exception->getMessage()]);
        }
    }

    /**
     * The mobile login API reads the users table and compares the password directly.
     * The tokens returned to the app are stored on that same row.
     */
    private function createAppUser(
        string $email,
        string $phone,
        string $password,
        array $pending,
        string $accessToken,
        string $idToken,
        string $refreshToken
    ): ?int {
        try {
            $db = db_connect();
            $saved = $db->table('users')->insert([
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'user_type' => 'User',
                'status' => 'active',
                'country_code' => '+91',
                'created_at' => date('Y-m-d H:i:s'),
                'app_version' => 'v8',
                'deleted' => 0,
                'access_token' => $accessToken,
                'id_token' => $idToken,
                'refresh_token' => $refreshToken,
                'signup_latitude' => (string) ($pending['signup_latitude'] ?? ''),
                'signup_longitude' => (string) ($pending['signup_longitude'] ?? ''),
            ]);

            if ($saved === false) {
                return null;
            }

            $row = $db->query(
                'SELECT id FROM users WHERE phone = ? AND email = ? AND deleted = 0 ORDER BY id DESC LIMIT 1',
                [$phone, $email]
            )->getRowArray();

            return is_array($row) ? (int) $row['id'] : null;
        } catch (Throwable $exception) {
            log_message('error', 'App user create failed: {message}', ['message' => $exception->getMessage()]);

            return null;
        }
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

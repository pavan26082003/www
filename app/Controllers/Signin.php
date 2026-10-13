<?php

namespace App\Controllers;

class Signin extends App_Controller {

    private $signin_validation_errors;

    function __construct() {
        parent::__construct();
        $this->signin_validation_errors = array();
        helper('email');
    }

    function index() {
        if (class_exists(\App\Libraries\GridWebSso::class)) {
            \App\Libraries\GridWebSso::consumeIncomingToken();
        }
	    if ($this->Users_model->login_user_id()) {
            $next = isset($_REQUEST['redirect']) ? (string) $_REQUEST['redirect'] : '';
            $returnPath = $this->resolveAppSsoReturnPath($next);
            if ($returnPath !== null) {
                // Relative path only — app_redirect() prepends site base.
                app_redirect($returnPath);
            }
            app_redirect('dashboard/view');
        } else {

            $view_data["redirect"] = "";
            if (isset($_REQUEST["redirect"])) {
                $view_data["redirect"] = $_REQUEST["redirect"];
            }

            return $this->template->view('signin/index', $view_data);
        }
    }

    /**
     * After Grid web SSO, return a relative grid/grid-chat path or null.
     * Security_Controller historically passed absolute get_uri() redirects;
     * those must still open Professional Space / Circle Connect, not dashboard.
     */
    private function resolveGridAppRedirectPath($redirect): ?string
    {
        $redirect = trim((string) $redirect);
        if ($redirect === '') {
            return null;
        }

        $path = $redirect;
        if (preg_match('#^(https?:)?//#i', $redirect)) {
            $parts = parse_url($redirect);
            if (!is_array($parts) || empty($parts['path'])) {
                return null;
            }
            $host = strtolower((string) ($parts['host'] ?? ''));
            if ($host !== '' && !$this->isTrustedGridRedirectHost($host)) {
                return null;
            }
            $path = (string) $parts['path'];
            if (!empty($parts['query'])) {
                $path .= '?' . $parts['query'];
            }
        }

        $path = preg_replace('#^/+#', '', $path) ?? $path;
        // Drop accidental site-absolute prefixes so app_redirect does not double base.
        if (preg_match('#^(?:https?:)?//#i', $path)) {
            return null;
        }

        // Sanitize repeated index.php prefixes (index.php/index.php/grid → grid).
        // app_redirect()/get_uri() always prepend indexPage once more.
        $path = $this->stripIndexPhpPrefixes($path);

        $check = strtolower(explode('?', $path, 2)[0]);
        if (!preg_match('#^(grid|grid-chat)(/|$)#', $check)) {
            return null;
        }

        // Strip SSO query from final hop; session is already established.
        if (strpos($path, 'grid_sso=') !== false) {
            $bits = parse_url('https://local/' . ltrim($path, '/'));
            $query = array();
            if (!empty($bits['query'])) {
                parse_str((string) $bits['query'], $query);
            }
            unset($query['grid_sso']);
            $cleanPath = $this->stripIndexPhpPrefixes(ltrim((string) ($bits['path'] ?? ''), '/'));
            if ($query !== array()) {
                $cleanPath .= '?' . http_build_query($query);
            }
            $path = $cleanPath;
        }

        return $path !== '' ? $path : null;
    }

    /**
     * Remove one or more leading index.php/ segments from a relative app path.
     */
    private function stripIndexPhpPrefixes(string $path): string
    {
        $path = ltrim($path, '/');
        while (preg_match('#^index\.php(/|$)#i', $path)) {
            $path = preg_replace('#^index\.php/?#i', '', $path, 1) ?? $path;
            $path = ltrim((string) $path, '/');
        }
        return $path;
    }

    private function isTrustedGridRedirectHost(string $host): bool
    {
        $host = strtolower($host);
        if (strpos($host, ':') !== false) {
            $host = substr($host, 0, (int) strpos($host, ':'));
        }
        $current = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if (strpos($current, ':') !== false) {
            $current = substr($current, 0, (int) strpos($current, ':'));
        }
        $allowed = array('efactor.tech', 'www.efactor.tech', 'localhost', '127.0.0.1');
        if ($current !== '') {
            $allowed[] = $current;
        }
        return in_array($host, $allowed, true);
    }

    /**
     * After a valid app SSO token, return to the module that was opened
     * (Grid, HRMS, Restaurant, Finance, and the other same-app pages).
     */
    private function resolveAppSsoReturnPath($redirect): ?string
    {
        $gridPath = $this->resolveGridAppRedirectPath($redirect);
        if ($gridPath !== null) {
            return $gridPath;
        }

        $redirect = trim((string) $redirect);
        if ($redirect === '') {
            return null;
        }

        if (preg_match('#^(https?:)?//#i', $redirect)) {
            $parts = parse_url($redirect);
            if (!is_array($parts)) {
                return null;
            }
            $host = strtolower((string) ($parts['host'] ?? ''));
            if ($host !== '' && !$this->isTrustedGridRedirectHost($host)) {
                return null;
            }
            $path = ltrim((string) ($parts['path'] ?? ''), '/');
            $query = array();
            if (!empty($parts['query'])) {
                parse_str((string) $parts['query'], $query);
            }
            unset($query['grid_sso']);
            $path = $this->stripIndexPhpPrefixes($path);
            if ($query !== array()) {
                $path .= '?' . http_build_query($query);
            }
            $redirect = $path;
        }

        $redirect = $this->stripIndexPhpPrefixes($redirect);
        if ($redirect === '' || strpos($redirect, '..') !== false) {
            return null;
        }
        $pathOnly = strtolower(explode('?', $redirect, 2)[0]);
        if ($pathOnly === '' || $pathOnly === 'signin' || strpos($pathOnly, 'signin/') === 0) {
            return null;
        }
        if (!preg_match('#^[a-z0-9][a-z0-9_./-]*$#', $pathOnly)) {
            return null;
        }

        return $redirect;
    }

    /** @deprecated Use resolveGridAppRedirectPath() */
    private function isGridAppRedirect($redirect) {
        return $this->resolveGridAppRedirectPath($redirect) !== null;
    }

    private function has_recaptcha_error() {
        $recaptcha_post_data = $this->request->getPost("g-recaptcha-response");
        $response = $this->is_valid_recaptcha($recaptcha_post_data);

        if ($response === true) {
            return true;
        } else {
            array_push($this->signin_validation_errors, app_lang("re_captcha_error-" . $response));
            return false;
        }
    }

    private function is_valid_recaptcha($recaptcha_post_data) {
        //load recaptcha lib
        require_once(APPPATH . "ThirdParty/recaptcha/autoload.php");
        $recaptcha = new \ReCaptcha\ReCaptcha(get_setting("re_captcha_secret_key"));
        $resp = $recaptcha->verify($recaptcha_post_data, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess()) {
            return true;
        } else {

            $error = "";
            foreach ($resp->getErrorCodes() as $code) {
                $error = $code;
            }

            return $error;
        }
    }
/*
    // check authentication
    function authenticate() {
        $validation = $this->validate_submitted_data(array(
            "email" => "required|valid_email",
            "password" => "required"
                ), true);

        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");
        $user_type = $this->request->getPost("user_type"); 
        if (!$email) {
            //loaded the page directly
            app_redirect('signin');
        }

        if (is_array($validation)) {
            //has validation errors
            $this->signin_validation_errors = $validation;
        }

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (get_setting("re_captcha_secret_key")) {
            //in this function, if any error found in recaptcha, that will be added
            $this->has_recaptcha_error();
        }

        //don't check password if there is any error
        if ($this->signin_validation_errors) {
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }


        if (!$this->Users_model->authenticate($email, $password, $user_type)) {
            //authentication failed
            array_push($this->signin_validation_errors, app_lang("authentication_failed"));
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }

        //authentication success
        $redirect = $this->request->getPost("redirect");
        if ($redirect) {
            return redirect()->to($redirect);
        } else {
            app_redirect('dashboard/view');
        }
    }


*/

/*--------------------------------------------------*/


function authenticate() {
    $validation = $this->validate_submitted_data(array(
        "email" => "required|valid_email",
        "password" => "required"
    ), true);

    $email = $this->request->getPost("email");
    $password = $this->request->getPost("password");
    $user_type = $this->request->getPost("user_type");
    $posted_redirect = trim((string) ($this->request->getPost("redirect") ?? ''));

    if (!$email) {
        app_redirect($this->buildSigninReturnUrl($posted_redirect));
    }

    if (is_array($validation)) {
        $this->signin_validation_errors = $validation;
    }

    if (get_setting("re_captcha_secret_key")) {
        $this->has_recaptcha_error();
    }

    // ✅ SET fallback user_type IF not selected
    if (!$user_type) {
        if (strtolower($email) === 'admin@efactor.io') {
            $user_type = 'lead'; // redirect admin as 'lead'
        } else {
            // 🟢 For any other user, auto fallback
            // ⚠️ Make sure this matches your DB exactly
            $user_type = $this->Users_model->get_user_type_by_email($email);
            if (!$user_type) {
                array_push($this->signin_validation_errors, "User type not found in database.");
                $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
                app_redirect($this->buildSigninReturnUrl($posted_redirect));
            }
        }
    }

    // ❌ If any errors, redirect
    if ($this->signin_validation_errors) {
        $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
        app_redirect($this->buildSigninReturnUrl($posted_redirect));
    }

    // ✅ Attempt login now
    if (!$this->Users_model->authenticate($email, $password, $user_type)) {
        array_push($this->signin_validation_errors, app_lang("authentication_failed"));
        $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
        app_redirect($this->buildSigninReturnUrl($posted_redirect));
    }

    // Grid / Grid-chat webview handoff: always prefer the safe relative app path.
    $gridPath = $this->resolveGridAppRedirectPath($posted_redirect);
    if ($gridPath === null && isset($_REQUEST['redirect'])) {
        $gridPath = $this->resolveGridAppRedirectPath((string) $_REQUEST['redirect']);
    }
    if ($gridPath !== null) {
        app_redirect($gridPath);
    }

    // Non-grid redirect (legacy): only allow same-app relative paths.
    if ($posted_redirect !== '' && $this->isSafeRelativeAppRedirect($posted_redirect)) {
        app_redirect($this->stripIndexPhpPrefixes($posted_redirect));
    }

    switch (strtolower((string) $user_type)) {
        case 'manufacturer':
            return redirect()->to(site_url('manufacturer-dashboard'));
        default:
            return redirect()->to(site_url('dashboard/view'));
    }
}

/**
 * Keep ?redirect= (and optional grid_sso) when bouncing back to the signin form.
 */
private function buildSigninReturnUrl(string $redirect = ''): string
{
    $redirect = trim($redirect);
    if ($redirect === '' && isset($_REQUEST['redirect'])) {
        $redirect = trim((string) $_REQUEST['redirect']);
    }

    $url = 'signin';
    $query = array();
    if ($redirect !== '') {
        $query['redirect'] = $redirect;
    }
    $sso = trim((string) ($this->request->getPost('grid_sso') ?? $_REQUEST['grid_sso'] ?? ''));
    if ($sso !== '') {
        $query['grid_sso'] = $sso;
    }
    if ($query !== array()) {
        $url .= '?' . http_build_query($query);
    }
    return $url;
}

/**
 * Allow only same-app relative paths for non-grid post-login redirects.
 */
private function isSafeRelativeAppRedirect(string $redirect): bool
{
    $redirect = trim($redirect);
    if ($redirect === '' || preg_match('#^(https?:)?//#i', $redirect) || strpos($redirect, '..') !== false) {
        return false;
    }
    $path = strtolower(explode('?', $this->stripIndexPhpPrefixes($redirect), 2)[0]);
    if ($path === '' || $path === 'signin' || strpos($path, 'signin/') === 0) {
        return false;
    }
    return (bool) preg_match('#^[a-z0-9][a-z0-9_/-]*$#', $path);
}










/*-------------------------------------------------*/

    function sign_out() {
        $this->Users_model->sign_out();
    }

    //send an email to users mail with reset password link
    function send_reset_password_mail() {
        $this->validate_submitted_data(array(
            "email" => "required|valid_email"
        ));


        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (get_setting("re_captcha_secret_key")) {

            $response = $this->is_valid_recaptcha($this->request->getPost("g-recaptcha-response"));

            if ($response !== true) {

                if ($response) {
                    echo json_encode(array('success' => false, 'message' => app_lang("re_captcha_error-" . $response)));
                } else {
                    echo json_encode(array('success' => false, 'message' => app_lang("re_captcha_expired")));
                }

                return false;
            }
        }



        $email = $this->request->getPost("email");

        $existing_user = $this->Users_model->is_email_exists($email);

        //send reset password email if found account with this email
        if ($existing_user) {
            $email_template = $this->Email_templates_model->get_final_template("reset_password");

            $parser_data["ACCOUNT_HOLDER_NAME"] = $existing_user->first_name . " " . $existing_user->last_name;
            $parser_data["SIGNATURE"] = $email_template->signature;
            $parser_data["LOGO_URL"] = get_logo_url();
            $parser_data["SITE_URL"] = get_uri();

            $verification_data = array(
                "type" => "reset_password",
                "code" => make_random_string(),
                "params" => serialize(array(
                    "email" => $existing_user->email,
                    "expire_time" => time() + (24 * 60 * 60)
                ))
            );

            $save_id = $this->Verification_model->ci_save($verification_data);

            $verification_info = $this->Verification_model->get_one($save_id);

            $parser_data['RESET_PASSWORD_URL'] = get_uri("signin/new_password/" . $verification_info->code);

              $view_data["form_type"] = "new_password";
       return $this->template->view('signin/index', $view_data);

            $message = $this->parser->setData($parser_data)->renderString($email_template->message);
            

            if (send_app_mail($email, $email_template->subject, $message)) {
                echo json_encode(array('success' => true, 'message' => app_lang("reset_info_send")));
            } else {
                echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
            }
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("no_acount_found_with_this_email")));
            return false;
        }
    }

    //show forgot password recovery form
    function request_reset_password() {
        $view_data["form_type"] = "request_reset_password";
        return $this->template->view('signin/index', $view_data);
    }

    //when user clicks to reset password link from his/her email, redirect to this url
    function new_password($key) {
        $valid_key = $this->is_valid_reset_password_key($key);


       

        if ($valid_key) {
            $email = get_array_value($valid_key, "email");

            if ($this->Users_model->is_email_exists($email)) {
                $view_data["key"] = $key;
                $view_data["form_type"] = "new_password";
                return $this->template->view('signin/index', $view_data);
                return false;
            }
        }

        //else show error
        $view_data["heading"] = "Invalid Request";
        $view_data["message"] = "The key has expaired or something went wrong!";
        return $this->template->view("errors/html/error_general", $view_data);
    }

    //finally reset the old password and save the new password
    function do_reset_password() {

        $this->validate_submitted_data(array(
            "key" => "required",
            "password" => "required"
        ));


        $key = $this->request->getPost("key");
        $password = $this->request->getPost("password");
        $valid_key = $this->is_valid_reset_password_key($key);

        if ($valid_key) {
            $email = get_array_value($valid_key, "email");
            $user = $this->Users_model->is_email_exists($email);
            $user_data = array("password" => password_hash($password, PASSWORD_DEFAULT));
            if ($user->id && $this->Users_model->ci_save($user_data, $user->id)) {
                //user can't reset password two times with the same code
                $options = array("code" => $key, "type" => "reset_password");
                $verification_info = $this->Verification_model->get_details($options)->getRow();
                if ($verification_info->id) {
                    $this->Verification_model->delete_permanently($verification_info->id);
                }

                echo json_encode(array("success" => true, 'message' => app_lang("password_reset_successfully") . " " . anchor("signin", app_lang("signin"))));
                return true;
            }
        }
        echo json_encode(array("success" => false, 'message' => app_lang("error_occurred")));
    }

    //check valid key
    private function is_valid_reset_password_key($verification_code = "") {

        if ($verification_code) {
            $options = array("code" => $verification_code, "type" => "reset_password");
            $verification_info = $this->Verification_model->get_details($options)->getRow();

            if ($verification_info && $verification_info->id) {
                $reset_password_info = unserialize($verification_info->params);

                $email = get_array_value($reset_password_info, "email");
                $expire_time = get_array_value($reset_password_info, "expire_time");

                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) && $expire_time && $expire_time > time()) {
                    return array("email" => $email);
                }
            }
        }
    }

}

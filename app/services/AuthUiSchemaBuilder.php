<?php

declare(strict_types=1);

namespace App\Services;

class AuthUiSchemaBuilder
{
    public function build(): array
    {
        helper('url');
 
        return [
            'schemaType' => 'auth-ui',
            'schemaVersion' => 7,
            'schemaId' => 'efactor-auth',
            'schemaStatus' => 'production',
            'contractVersion' => '2026-08-08.auth.v7',
            'minRendererVersion' => '1.0.0',
            'generatedAt' => date(DATE_ATOM),
            'generatedBy' => 'App\\Services\\AuthUiSchemaBuilder',

            // Fix #6: monotonic revision — incremented on every schema commit.
            // Clients compare this to their cached revision to detect staleness.
            'revision' => 3,

            // Fix #7: SHA-256 of the canonical schema JSON (computed at store time by api_auth_schema.php).
            // Placeholder here — api_auth_schema.php overwrites this before storing.
            'contentHash' => null,

            // Fix #12: sync_token for missed-event recovery.
            // Clients store this and pass it as ?since=<sync_token> to the delta endpoint.
            // api_auth_schema.php sets this to the row id on store.
            'syncToken' => null,

            // ── Responsive contract ──────────────────────────────────────────
            // Flutter reads breakpoints locally from LayoutBuilder — no extra request on rotation.
            // All values are logical pixels (dp). One schema serves every screen size.
            'responsive' => [
                'version'          => '1.0',
                'unit'             => 'logicalPx',
                'dimensionPolicy'  => 'logical',
                'scaleLegacyDimensions' => false,
                'safeArea'         => true,
                'breakpoints' => [
                    'narrow'   => ['minimumWidth' => 0,   'maximumWidth' => 359],
                    'compact'  => ['minimumWidth' => 360, 'maximumWidth' => 599],
                    'medium'   => ['minimumWidth' => 600, 'maximumWidth' => 839],
                    'expanded' => ['minimumWidth' => 840],
                ],
                // Root-level layout overrides per breakpoint. Pages and components inherit
                // from here and can declare their own responsiveLayout to override further.
                'layouts' => [
                    'narrow'   => [
                        'contentPadding'  => ['horizontal' => 16, 'vertical' => 20],
                        'maxContentWidth' => null,
                        'formMaxWidth'    => null,
                    ],
                    'compact'  => [
                        'contentPadding'  => ['horizontal' => 24, 'vertical' => 24],
                        'maxContentWidth' => null,
                        'formMaxWidth'    => null,
                    ],
                    'medium'   => [
                        'contentPadding'  => ['horizontal' => 40, 'vertical' => 32],
                        'maxContentWidth' => 560,
                        'formMaxWidth'    => 480,
                    ],
                    'expanded' => [
                        'contentPadding'  => ['horizontal' => 64, 'vertical' => 40],
                        'maxContentWidth' => 600,
                        'formMaxWidth'    => 520,
                    ],
                ],
            ],
            'meta' => $this->productionMeta(),
            'contract' => $this->schemaContract(),
            'compatibility' => $this->compatibilityMatrix(),
            'runtime' => $this->runtimeConfig(),
            'accessibility' => $this->accessibilityConfig(),
            'errorHandling' => $this->errorHandlingConfig(),
            'cachePolicy' => $this->cachePolicy(),

            // Fix #8: lightweight Auth metadata endpoint — clients call this first to check
            // if their cached revision is still current without downloading the full schema.
            'metadataEndpoint' => [
                'url'    => base_url('v8/api_auth_schema.php') . '?action=meta',
                'method' => 'GET',
                'response' => [
                    'revisionField'   => 'revision',
                    'hashField'       => 'contentHash',
                    'syncTokenField'  => 'syncToken',
                    'updatedAtField'  => 'updated_at',
                ],
            ],

            // Fix #9: global authenticated schema manifest — lists all auth schema revisions.
            'manifestEndpoint' => [
                'url'    => base_url('v8/api_auth_schema.php') . '?action=manifest',
                'method' => 'GET',
                'auth'   => 'bearer',
                'response' => [
                    'schemasField'   => 'schemas',
                    'deletedField'   => 'deleted',
                    'syncTokenField' => 'syncToken',
                ],
            ],

            // Fix #10: RFC-6902 JSON Patch delta endpoint.
            // Client passes from_revision + base_hash; server returns patch or full_required.
            'deltaEndpoint' => [
                'url'    => base_url('v8/api_auth_schema.php') . '?action=delta',
                'method' => 'GET',
                'params' => [
                    'from_revision' => 'integer — the client\'s currently cached revision',
                    'to_revision'   => 'integer — omit to get latest',
                    'base_hash'     => 'string — SHA-256 of the client\'s cached schema JSON',
                    'target_hash'   => 'string — SHA-256 of the target schema JSON (in response)',
                ],
                'response' => [
                    'patchField'        => 'patch',       // RFC-6902 JSON Patch array
                    'fromRevisionField' => 'from_revision',
                    'toRevisionField'   => 'to_revision',
                    'baseHashField'     => 'base_hash',
                    'targetHashField'   => 'target_hash',
                    'fullRequiredField' => 'full_required', // Fix #11
                ],
            ],

            // Fix #11: full_required fallback — server sets this true when a delta is not possible.
            // Client must fetch the full schema when full_required === true.
            'fullRequiredPolicy' => [
                'triggerConditions' => [
                    'revision gap > 10',
                    'base_hash mismatch',
                    'schema restructure',
                ],
                'fullSchemaUrl' => base_url('v8/api_auth_schema.php'),
            ],

            // Fix #13 + #14: schema_changed WebSocket notification contract.
            // Server publishes this event through the eFactor Cloud WebSocket ONLY after
            // a schema commit is fully persisted. Authoritative patch/full schema stays on HTTPS.
            'schemaChangedEvent' => [
                'eventName'    => 'schema_changed',
                'transport'    => 'websocket',
                'channel'      => 'efactor-cloud',
                'payloadShape' => [
                    'schema_id'    => 'string — efactor-auth',
                    'revision'     => 'integer — new revision',
                    'content_hash' => 'string — SHA-256',
                    'sync_token'   => 'string — for missed-event recovery',
                ],
                'clientBehavior' => [
                    'onEvent'            => 'call metadataEndpoint to confirm revision',
                    'ifRevisionChanged'  => 'call deltaEndpoint or fullSchemaUrl',
                    'ifEventMissed'      => 'pass last known syncToken to deltaEndpoint',
                ],
                'note' => 'WebSocket is notify-only. Authoritative patch/full schema is always fetched via HTTPS.',
            ],

            'app' => [
                'name' => 'Efactor',
                'initialPageId' => 'welcome',
                'safeArea' => true,
                // Fix #1: support both portrait and landscape — do not lock to portrait only.
                'orientation' => 'portrait|landscape',
                'keyboardAvoidance' => true,
                'scrollBehavior' => 'inside-screen',
                'postAuthDestination' => [
                    'type' => 'flutterAppHome',
                    'targetPageId' => 'home',
                    'exitAuthFlow' => true,
                ],
                'sourceViews' => [
                    'welcome' => 'Views/sd_auth/welcome.php',
                    'login' => 'Views/sd_auth/login.php',
                    'signup' => 'Views/sd_auth/signup.php',
                    'forgotPassword' => 'Views/sd_auth/forgot_password.php',
                ],
                // Responsive app-level overrides. Forms use fill width up to formMaxWidth.
                // No re-fetch needed on rotation — Flutter reads these locally.
                'responsiveLayout' => [
                    'narrow'   => ['formMaxWidth' => null, 'contentPadding' => 16, 'keyboardAvoidance' => true],
                    'compact'  => ['formMaxWidth' => null, 'contentPadding' => 24, 'keyboardAvoidance' => true],
                    'medium'   => ['formMaxWidth' => 480, 'contentPadding' => 40, 'keyboardAvoidance' => true],
                    'expanded' => ['formMaxWidth' => 520, 'contentPadding' => 64, 'keyboardAvoidance' => true],
                    // Fix #4: landscape height overrides so keyboard + short screens don't clip.
                    'all' => [
                        'landscape' => [
                            'scrollable'        => true,
                            'verticalOverflow'  => 'scroll',
                            'keyboardAvoidance' => true,
                        ],
                    ],
                ],
            ],

            'theme' => [
                'colors' => [
                    'primary' => '#202940',
                    'primaryDark' => '#1F150C',
                    'primaryLight' => '#E8EDF2',
                    'background' => '#F5F5F5',
                    'surface' => '#FFFFFF',
                    'textPrimary' => '#1E1E1E',
                    'textSecondary' => '#667085',
                    'border' => '#CDD5DF',
                    'success' => '#2C3947',
                    'danger' => '#B42318',
                ],
                'radius' => ['card' => 30, 'button' => 18, 'input' => 14, 'circle' => 999],
                'shadow' => [
                    'card' => ['offsetX'=>0,'offsetY'=>15,'blur'=>40,'color'=>'rgba(0,0,0,0.08)'],
                    'button' => ['offsetX'=>0,'offsetY'=>10,'blur'=>22,'color'=>'rgba(32,41,64,0.22)'],
                ],
                'typography' => [
                    'fontFamily' => 'Inter, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, sans-serif',
                    'welcomeHeading' => ['fontSize'=>34,'fontWeight'=>700,'lineHeight'=>1.12,'letterSpacing'=>0],
                    'pageTitle' => ['fontSize'=>27,'fontWeight'=>700,'lineHeight'=>1.15,'letterSpacing'=>0],
                    'subtitle' => ['fontSize'=>12,'fontWeight'=>400,'letterSpacing'=>0],
                    'input' => ['fontSize'=>14,'fontWeight'=>400,'letterSpacing'=>0],
                    'button' => ['fontSize'=>14,'fontWeight'=>600,'letterSpacing'=>0],
                    'caption' => ['fontSize'=>11,'fontWeight'=>400,'letterSpacing'=>0],
                ],
            ],

            'icons' => $this->authIcons(),

            'assets' => [
                'welcomeBackground' => [
                    'type' => 'networkImage',
                    'url' => base_url('assets/images/efactor-ecosystem.jpg'),
                    'fit' => 'cover',
                    'alignment' => 'center',
                ],
                'authHeroBackground' => [
                    'type' => 'networkImage',
                    'url' => base_url('assets/images/efactor-ecosystem.jpg'),
                    'fit' => 'cover',
                    'alignment' => 'center',
                ],
                'logo' => [
                    'type' => 'networkImage',
                    'url' => base_url('assets/images/efactor-logo.jpg'),
                    'fit' => 'contain',
                ],
            ],

            'state' => [
                'auth' => [
                    'isLoading'=>false,
                    'isAuthenticated'=>false,
                    'error'=>null,
                    'errorCode'=>null,
                    'lastAction'=>null,
                    'accesstoken'=>null,
                    'idtoken'=>null,
                    'refreshtoken'=>null,
                    'username'=>null,
                    'email'=>null,
                    'user_type'=>null,
                    'user_id'=>null,
                ],
                'ui' => ['activePage'=>'welcome','keyboardVisible'=>false,'reducedMotion'=>false],
                'network' => ['isOnline'=>true,'lastError'=>null,'retryCount'=>0],
                'loginForm' => ['identity'=>'','password'=>'','remember_ui'=>false,'user_type'=>'User'],
                'signupForm' => [
                    'email'=>'',
                    'phone'=>'',
                    'otp'=>'',
                    'otpVisible'=>false,
                    'otpVerified'=>false,
                    'password'=>'',
                    'confirm_password'=>'',
                    'terms'=>false,
                    'country_code'=>'+91',
                    'user_type'=>'User',
                    'persistSession'=>true,
                ],
                'forgotPasswordForm' => [
                    'phone'=>'',
                    'otp'=>'',
                    'otpVisible'=>false,
                    'password'=>'',
                    'confirm_password'=>'',
                ],
            ],

            'forms' => $this->formContracts(),
            'validationRules' => $this->validationRulesFromCatalog(),
            'actions' => [
                'goWelcome' => ['type'=>'navigate','targetPageId'=>'welcome'],
                'goLogin' => ['type'=>'navigate','targetPageId'=>'login'],
                'goSignup' => ['type'=>'navigate','targetPageId'=>'signup'],
                'goForgotPassword' => ['type'=>'navigate','targetPageId'=>'forgotPassword','fallbackUrl'=>site_url('sd-forgot-password')],
                'openDocumentation' => ['type'=>'url','url'=>'https://efactor.tech/index.php/knowledge_base','presentation'=>'externalBrowser'],
                'openPrivacyPolicy' => ['type'=>'url','url'=>'https://efactor.tech/privacy_policy.html','presentation'=>'externalBrowser'],
                'openTerms' => ['type'=>'url','url'=>'https://efactor.tech/user_agreement.html','presentation'=>'externalBrowser'],
                'openSocialGoogle' => ['type'=>'url','url'=>site_url('sd-social/google'),'presentation'=>'externalBrowser'],
                'openSocialApple' => ['type'=>'url','url'=>site_url('sd-social/apple'),'presentation'=>'externalBrowser'],
                'openSocialFacebook' => ['type'=>'url','url'=>site_url('sd-social/facebook'),'presentation'=>'externalBrowser'],
                'sendSignupOtp' => [
                    'type'=>'http',
                    'method'=>'POST',
                    'url'=>site_url('sd-signup/otp'),
                    'contentType'=>'application/json',
                    'csrf'=>false,
                    'payload'=>[
                        'email'=>'$state.signupForm.email',
                        'phone'=>'$state.signupForm.phone',
                        'password'=>'$state.signupForm.password',
                        'country_code'=>'$state.signupForm.country_code',
                        'user_type'=>'$state.signupForm.user_type',
                    ],
                    'validationGroup'=>'signupOtpRequest',
                    'loadingStateKey'=>'auth.isLoading',
                    'success'=>[
                        'type'=>'setState',
                        'targetStateKey'=>'signupForm.otpVisible',
                        'value'=>true,
                        'nextFocusComponentId'=>'signup_otp_digit_1',
                    ],
                    'failure'=>$this->authHttpFailure(),
                    'responseType'=>'json',
                    'responseContract'=>$this->authJsonResponseContract(),
                    'timeoutMs'=>30000,
                    'retry'=>['enabled'=>false],
                ],
                'verifySignupOtp' => [
                    'type'=>'http',
                    'method'=>'POST',
                    'url'=>site_url('sd-signup/verify'),
                    'contentType'=>'application/json',
                    'csrf'=>false,
                    'payload'=>[
                        'phone'=>'$state.signupForm.phone',
                        'verification_code'=>'$state.signupForm.otp',
                    ],
                    'validationGroup'=>'signupOtpVerification',
                    'loadingStateKey'=>'auth.isLoading',
                    'success'=>[
                        'type'=>'setState',
                        'targetStateKey'=>'signupForm.otpVerified',
                        'value'=>true,
                    ],
                    'failure'=>$this->authHttpFailure(),
                    'responseType'=>'json',
                    'responseContract'=>$this->authJsonResponseContract(),
                    'timeoutMs'=>30000,
                    'retry'=>['enabled'=>false],
                ],
                'sendForgotPasswordOtp' => [
                    'type'=>'http',
                    'method'=>'POST',
                    'url'=>$this->mobileAuthUrl('forgotpassword'),
                    'contentType'=>'application/json',
                    'csrf'=>false,
                    'payload'=>[
                        'phone'=>'$state.forgotPasswordForm.phone',
                    ],
                    'validationGroup'=>'forgotOtpRequest',
                    'loadingStateKey'=>'auth.isLoading',
                    'success'=>[
                        'type'=>'setState',
                        'targetStateKey'=>'forgotPasswordForm.otpVisible',
                        'value'=>true,
                        'nextFocusComponentId'=>'forgot_otp_digit_1',
                    ],
                    'failure'=>$this->authHttpFailure(),
                    'responseType'=>'json',
                    'responseContract'=>$this->authJsonResponseContract(),
                    'timeoutMs'=>30000,
                    'retry'=>['enabled'=>false],
                ],
                'submitLogin' => [
                    'type'=>'http',
                    'method'=>'POST',
                    'url'=>$this->mobileAuthUrl('userlogin'),
                    'contentType'=>'application/json',
                    'csrf'=>false,
                    'payload'=>[
                        'username'=>'$state.loginForm.identity',
                        'password'=>'$state.loginForm.password',
                        'user_type'=>'$state.loginForm.user_type',
                    ],
                    'validationGroup'=>'login',
                    'loadingStateKey'=>'auth.isLoading',
                    'success'=>$this->postAuthSuccess($this->loginSessionContract()),
                    'failure'=>$this->authHttpFailure(),
                    'responseType'=>'json',
                    'responseContract'=>$this->authJsonResponseContract($this->loginTokenResponseFields()),
                    'timeoutMs'=>30000,
                    'retry'=>['enabled'=>false],
                ],
                'submitSignup' => [
                    'type'=>'http',
                    'method'=>'POST',
                    'url'=>site_url('sd-signup/complete'),
                    'contentType'=>'application/json',
                    'csrf'=>false,
                    'payload'=>[
                        'email'=>'$state.signupForm.email',
                        'phone'=>'$state.signupForm.phone',
                        'verification_code'=>'$state.signupForm.otp',
                        'password'=>'$state.signupForm.password',
                    ],
                    'validationGroup'=>'signup',
                    'loadingStateKey'=>'auth.isLoading',
                    'requires'=>['stateKey'=>'signupForm.otpVerified','equals'=>true],
                    'success'=>$this->postAuthSuccess($this->signupSessionContract()),
                    'failure'=>$this->authHttpFailure(),
                    'responseType'=>'json',
                    'responseContract'=>$this->authJsonResponseContract($this->loginTokenResponseFields()),
                    'timeoutMs'=>30000,
                    'retry'=>['enabled'=>false],
                ],
                'submitForgotPassword' => [
                    'type'=>'http',
                    'method'=>'POST',
                    'url'=>$this->mobileAuthUrl('resetpassword'),
                    'contentType'=>'application/json',
                    'csrf'=>false,
                    'payload'=>[
                        'phone'=>'$state.forgotPasswordForm.phone',
                        'password'=>'$state.forgotPasswordForm.password',
                        'verification_code'=>'$state.forgotPasswordForm.otp',
                    ],
                    'validationGroup'=>'forgotPassword',
                    'loadingStateKey'=>'auth.isLoading',
                    'success'=>['type'=>'navigate','targetPageId'=>'login','fallbackUrl'=>site_url('sd-login')],
                    'failure'=>$this->authHttpFailure(),
                    'responseType'=>'json',
                    'responseContract'=>$this->authJsonResponseContract(),
                    'timeoutMs'=>30000,
                    'retry'=>['enabled'=>false],
                ],
            ],
            'pages' => [
                $this->welcomePage(),
                $this->loginPage(),
                $this->signupPage(),
                $this->forgotPasswordPage(),
            ],
        ];
    }
    private function productionMeta(): array
    {
        return [
            'name' => 'Efactor Auth UI Schema',
            'environment' => 'production',
            'owner' => 'efactor',
            'locale' => 'en-IN',
            'timezone' => 'Asia/Kolkata',
            'description' => 'Production-ready schema for welcome, login, signup, and forgot-password auth screens.',
            'sourceOfTruth' => 'https://efactor.tech/v8/api_auth_schema.php',
        ];
    }

    private function schemaContract(): array
    {
        return [
            'format' => 'efactor-auth-ui',
            'version' => 7,
            'requiredTopLevelKeys' => [
                'schemaType', 'schemaVersion', 'schemaId', 'schemaStatus', 'app', 'theme',
                'icons', 'assets', 'state', 'forms', 'validationRules', 'actions', 'pages',
            ],
            'componentDefaults' => [
                'visibility' => true,
                'enabled' => true,
                'testIdPrefix' => 'auth',
                'errorPresentation' => 'inline',
            ],
            'iconContract' => [
                'mode' => 'library-ref-with-inline-svg-fallback',
                'viewBox' => '0 0 24 24',
                'stroke' => 'currentColor',
                'strokeWidth' => 2,
                'strokeLinecap' => 'round',
                'strokeLinejoin' => 'round',
            ],
        ];
    }

    private function compatibilityMatrix(): array
    {
        return [
            'php' => '>=7.4',
            'codeIgniter' => '4.x',
            'mysql' => '5.x',
            'renderers' => ['flutter', 'react-native', 'web'],
            'requires' => ['inline-svg-icons', 'network-images', 'form-validation', 'keyboard-avoidance', 'otp-input', 'external-link-actions'],
        ];
    }

    private function runtimeConfig(): array
    {
        return [
            'baseUrl' => rtrim(site_url('/'), '/'),
            'apiUrl' => base_url('v8/api_auth_schema.php'),
            'methodOverride' => false,
            'csrf' => [
                'enabled' => true,
                'source' => 'server-rendered-form-or-cookie',
                'fieldName' => $this->csrfFieldName(),
                'headerName' => 'X-CSRF-TOKEN',
                'refreshStrategy' => 'reload-page-on-token-expiry',
            ],
            'network' => [
                'timeoutMs' => 30000,
                'retry' => ['enabled' => false, 'maxAttempts' => 0],
                'credentials' => 'same-origin',
            ],
            'mobileAuth' => [
                'baseUrl' => base_url('v8/api.php'),
                'contentType' => 'application/json',
                'successStatusValue' => 'success',
                'messagePath' => 'description',
                'errorCodePath' => 'error_code',
            ],
        ];
    }

    private function csrfFieldName(): string
    {
        return function_exists('csrf_token') ? csrf_token() : 'csrf_token';
    }
    private function accessibilityConfig(): array
    {
        return [
            'minTouchTarget' => ['width' => 44, 'height' => 44],
            'supportsDynamicType' => true,
            'reducedMotion' => true,
            'screenReader' => true,
            'focusOrder' => [
                'welcome' => ['welcome_logo', 'welcome_heading', 'welcome_description', 'welcome_signin', 'welcome_create_account', 'welcome_legal_links'],
                'login' => ['login_back', 'login_identity', 'login_password', 'login_remember', 'login_forgot', 'login_submit', 'login_footer'],
                'signup' => ['signup_back', 'signup_email', 'signup_phone', 'signup_password', 'signup_confirm_password', 'signup_send_otp', 'signup_otp_boxes', 'signup_verify_otp', 'signup_terms', 'signup_submit', 'signup_footer'],
                'forgotPassword' => ['forgot_back', 'forgot_phone', 'forgot_send_otp', 'forgot_otp_boxes', 'forgot_password', 'forgot_confirm_password', 'forgot_submit', 'forgot_footer'],
            ],
        ];
    }

    private function errorHandlingConfig(): array
    {
        return [
            'display' => 'inline-and-page-alert',
            'fieldErrorState' => 'has-error',
            'messagePath' => 'description',
            'errorCodePath' => 'error_code',
            'messages' => [
                'network' => 'Unable to connect. Please check your internet and try again.',
                'server' => 'Something went wrong. Please try again.',
                'validation' => 'Please correct the highlighted fields.',
                'unauthorized' => 'Invalid email/mobile number or password.',
            ],
            'errorCodes' => [
                'ACCOUNT_NOT_FOUND' => ['message'=>'This account is not registered.','legacyCodes'=>['101111']],
                'INVALID_PASSWORD' => ['message'=>'The password is incorrect.'],
                'INVALID_CREDENTIALS' => ['message'=>'Invalid email/mobile number or password.'],
                'ACCOUNT_EXISTS' => ['message'=>'This account already exists.','legacyCodes'=>['101006']],
                'INVALID_OTP' => ['message'=>'The OTP is incorrect.','legacyCodes'=>['101037']],
                'OTP_EXPIRED' => ['message'=>'The OTP has expired.'],
                'INVALID_PHONE' => ['message'=>'Enter a valid 10-digit mobile number.','legacyCodes'=>['101004']],
                'TOO_MANY_ATTEMPTS' => ['message'=>'Too many attempts. Please try again later.'],
            ],
        ];
    }

    private function cachePolicy(): array
    {
        return [
            // Fix #5: replace no-store-for-generator with a mobile persistent/versioned policy.
            // Mobile clients cache the schema by revision and revalidate via the delta endpoint.
            'schema' => [
                'strategy'      => 'stale-while-revalidate',
                'maxAgeSeconds' => 3600,       // serve cached for 1 hour before revalidating
                'staleMaxAge'   => 86400,      // serve stale for up to 24 h while background refresh runs
                'revalidateOn'  => ['app_foreground', 'schema_changed_event'],
                'keyField'      => 'revision',  // cached copy is keyed by revision number
                'invalidateOn'  => ['revision_increment', 'schema_changed'],
            ],
            'assets' => ['strategy' => 'cache-first', 'maxAgeSeconds' => 86400],
        ];
    }

    private function formContracts(): array
    {
        $catalog = $this->canonicalAuthFields();

        return [
            'login' => [
                'id' => 'loginForm',
                'method' => 'POST',
                'actionRef' => 'submitLogin',
                'submitComponentId' => 'login_submit',
                'validationGroup' => 'login',
                'fields' => $this->formFieldsFromCatalog('login', $catalog),
            ],
            'signup' => [
                'id' => 'signupForm',
                'method' => 'POST',
                'actionRef' => 'submitSignup',
                'submitComponentId' => 'signup_submit',
                'validationGroup' => 'signup',
                'fields' => $this->formFieldsFromCatalog('signup', $catalog),
            ],
            'forgotPassword' => [
                'id' => 'forgotPasswordForm',
                'method' => 'POST',
                'actionRef' => 'submitForgotPassword',
                'submitComponentId' => 'forgot_submit',
                'validationGroup' => 'forgotPassword',
                'fields' => $this->formFieldsFromCatalog('forgotPassword', $catalog),
            ],
        ];
    }

    private function validationRulesFromCatalog(): array
    {
        $rules = [];
        foreach ($this->canonicalAuthFields() as $group => $fields) {
            $rules[$group] = [];
            foreach ($fields as $name => $field) {
                $rules[$group][$name] = $field['validation'];
            }
        }

        return $rules;
    }

    private function formFieldsFromCatalog(string $group, array $catalog): array
    {
        $fields = [];
        foreach ($catalog[$group] ?? [] as $field) {
            if (($field['includeInForm'] ?? true) === false) {
                continue;
            }
            $fields[] = $field['form'];
        }

        return $fields;
    }

    private function canonicalAuthFields(): array
    {
        $signupEmail = $this->catalogEmail('signupForm.email');
        $signupPhone = $this->catalogPhone('signupForm.phone');
        $signupPassword = $this->catalogPassword('signupForm.password', true);
        $signupOtp = $this->catalogOtp('signupForm.otp', true, 'signupForm.otpVisible');
        $forgotPhone = $this->catalogPhone('forgotPasswordForm.phone');
        $forgotOtp = $this->catalogOtp('forgotPasswordForm.otp', true, 'forgotPasswordForm.otpVisible');
        $forgotPassword = $this->catalogPassword('forgotPasswordForm.password', true);

        return [
            'login' => [
                'identity' => [
                    'form' => ['name'=>'identity','stateKey'=>'loginForm.identity','type'=>'tel','required'=>true,'maxLength'=>191,'autocomplete'=>'tel'],
                    'validation' => ['required'=>true,'maxLength'=>191,'stateKey'=>'loginForm.identity'],
                ],
                'password' => [
                    'form' => ['name'=>'password','stateKey'=>'loginForm.password','type'=>'password','required'=>true,'maxLength'=>72,'autocomplete'=>'current-password'],
                    'validation' => ['required'=>true,'maxLength'=>72,'stateKey'=>'loginForm.password'],
                ],
                'remember_ui' => [
                    'form' => ['name'=>'remember_ui','stateKey'=>'loginForm.remember_ui','type'=>'checkbox','required'=>false],
                    'validation' => ['required'=>false,'stateKey'=>'loginForm.remember_ui'],
                ],
            ],
            'signup' => [
                'email' => $signupEmail,
                'phone' => $signupPhone,
                'otp' => $signupOtp,
                'otpVerified' => [
                    'includeInForm' => false,
                    'form' => ['name'=>'otpVerified','stateKey'=>'signupForm.otpVerified','type'=>'boolean','required'=>true],
                    'validation' => ['required'=>true,'acceptedValue'=>true,'stateKey'=>'signupForm.otpVerified'],
                ],
                'password' => $signupPassword,
                'confirm_password' => [
                    'form' => ['name'=>'confirm_password','stateKey'=>'signupForm.confirm_password','type'=>'password','required'=>true,'matchesStateKey'=>'signupForm.password','autocomplete'=>'new-password'],
                    'validation' => ['required'=>true,'matchesStateKey'=>'signupForm.password','stateKey'=>'signupForm.confirm_password'],
                ],
                'terms' => [
                    'form' => ['name'=>'terms','stateKey'=>'signupForm.terms','type'=>'checkbox','required'=>true,'acceptedValue'=>true],
                    'validation' => ['required'=>true,'acceptedValue'=>true,'stateKey'=>'signupForm.terms'],
                ],
            ],
            'signupOtpRequest' => [
                'email' => $signupEmail,
                'phone' => $signupPhone,
                'password' => $signupPassword,
                'confirm_password' => [
                    'form' => ['name'=>'confirm_password','stateKey'=>'signupForm.confirm_password','type'=>'password','required'=>true,'matchesStateKey'=>'signupForm.password','autocomplete'=>'new-password'],
                    'validation' => ['required'=>true,'matchesStateKey'=>'signupForm.password','stateKey'=>'signupForm.confirm_password'],
                ],
            ],
            'signupOtpVerification' => [
                'phone' => $signupPhone,
                'otp' => $signupOtp,
            ],
            'forgotPassword' => [
                'phone' => $forgotPhone,
                'otp' => $forgotOtp,
                'password' => $forgotPassword,
                'confirm_password' => [
                    'form' => ['name'=>'confirm_password','stateKey'=>'forgotPasswordForm.confirm_password','type'=>'password','required'=>true,'matchesStateKey'=>'forgotPasswordForm.password','autocomplete'=>'new-password'],
                    'validation' => ['required'=>true,'matchesStateKey'=>'forgotPasswordForm.password','stateKey'=>'forgotPasswordForm.confirm_password'],
                ],
            ],
            'forgotOtpRequest' => [
                'phone' => $forgotPhone,
            ],
        ];
    }

    private function catalogEmail(string $stateKey): array
    {
        return [
            'form' => ['name'=>'email','stateKey'=>$stateKey,'type'=>'email','required'=>true,'maxLength'=>191,'autocomplete'=>'email','inputMode'=>'email'],
            'validation' => ['required'=>true,'maxLength'=>191,'format'=>'email','stateKey'=>$stateKey],
        ];
    }

    private function catalogPhone(string $stateKey): array
    {
        return [
            'form' => ['name'=>'phone','stateKey'=>$stateKey,'type'=>'tel','required'=>true,'minLength'=>10,'maxLength'=>10,'inputMode'=>'numeric','pattern'=>'^[0-9]{10}$'],
            'validation' => ['required'=>true,'length'=>10,'format'=>'numeric','stateKey'=>$stateKey],
        ];
    }

    private function catalogPassword(string $stateKey, bool $strong = false, ?string $matchKey = null): array
    {
        $form = ['name'=>'password','stateKey'=>$stateKey,'type'=>'password','required'=>true,'maxLength'=>72,'autocomplete'=>'new-password'];
        $validation = ['required'=>true,'maxLength'=>72,'stateKey'=>$stateKey];
        if ($strong) {
            $form['minLength'] = 8;
            $validation['minLength'] = 8;
            $validation['mustContain'] = ['uppercase','lowercase','number','special'];
        }
        if ($matchKey !== null) {
            $form['matchesStateKey'] = $matchKey;
            $validation['matchesStateKey'] = $matchKey;
        }

        return ['form'=>$form,'validation'=>$validation];
    }

    private function catalogOtp(string $stateKey, bool $required, string $visibilityStateKey): array
    {
        return [
            'form' => ['name'=>'otp','stateKey'=>$stateKey,'type'=>'otp','required'=>$required,'length'=>6,'inputMode'=>'numeric','display'=>'six-square-boxes'],
            'validation' => ['required'=>$required,'length'=>6,'format'=>'numeric','visibilityStateKey'=>$visibilityStateKey,'stateKey'=>$stateKey],
        ];
    }
    private function welcomePage(): array
    {
        return [
            'id'=>'welcome',
            'route'=>'/welcome',
            'type'=>'screen',
            'scrollable'=>false,
            'safeArea'=>true,
            'sourcePage'=>$this->sourcePageMeta('welcome'),
            'pageLayout'=>$this->pageLayoutSpec('welcome'),
            // Responsive overrides for the welcome page.
            // Flutter deep-merges the selected breakpoint into the base page.
            'responsiveLayout' => [
                'narrow'   => ['layout' => ['padding' => ['top' => 20, 'right' => 16, 'bottom' => 16, 'left' => 16]]],
                'compact'  => ['layout' => ['padding' => ['top' => 28, 'right' => 24, 'bottom' => 24, 'left' => 24]]],
                'medium'   => ['layout' => ['padding' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]], 'maxContentWidth' => 560],
                'expanded' => ['layout' => ['padding' => ['top' => 48, 'right' => 64, 'bottom' => 48, 'left' => 64]], 'maxContentWidth' => 600],
            ],
            'background'=>[
                'asset'=>'welcomeBackground',
                'overlay'=>[
                    'type'=>'linearGradient',
                    'begin'=>'topCenter',
                    'end'=>'bottomCenter',
                    'colors'=>[
                        'rgba(13,18,28,0.08)',
                        'rgba(13,18,28,0.48)',
                        'rgba(13,18,28,0.84)',
                    ],
                    'stops'=>[0,0.58,1],
                ],
            ],
            'layout'=>[
                'type'=>'stack',
                'padding'=>['top'=>28,'right'=>24,'bottom'=>24,'left'=>24],
            ],
            'animation'=>['type'=>'fade','durationMs'=>450,'curve'=>'easeOut'],
            'components'=>[
                [
                    'id'=>'welcome_logo','type'=>'image','asset'=>'logo',
                    'position'=>['top'=>30,'left'=>24],
                    'size'=>['width'=>132,'height'=>46],
                ],
                [
                    'id'=>'welcome_heading','type'=>'text',
                    'text'=>'EFACTOR GRID - One Platform. Infinite Possibilities',
                    'position'=>['left'=>28,'right'=>28,'bottom'=>245],
                    'className'=>'ef-title',
                    'sourceClass'=>'ef-title',
                    'style'=>[
                        'fontSize'=>34,'fontWeight'=>700,'lineHeight'=>1.12,'letterSpacing'=>0,
                        'color'=>'#FFFFFF','textAlign'=>'left',
                    ],
                    'animation'=>['type'=>'slideUpFade','durationMs'=>500,'delayMs'=>100],
                ],
                [
                    'id'=>'welcome_description','type'=>'text',
                    'text'=>'Secure device access, account management, and support resources in one professional workspace.',
                    'position'=>['left'=>28,'right'=>28,'bottom'=>198],
                    'className'=>'ef-description',
                    'sourceClass'=>'ef-description',
                    'style'=>[
                        'fontSize'=>12,'fontWeight'=>400,'lineHeight'=>1.55,'letterSpacing'=>0,
                        'color'=>'rgba(255,255,255,0.86)','textAlign'=>'left',
                    ],
                ],
                [
                    'id'=>'welcome_signin','type'=>'button','text'=>'Log-in','variant'=>'primary',
                    'position'=>['left'=>20,'right'=>20,'bottom'=>70],
                    'size'=>['height'=>52,'width'=>'fill'],
                    'style'=>[
                        'backgroundColor'=>'linear-gradient(135deg,rgba(32,41,64,0.92),rgba(31,21,12,0.88))',
                        'borderColor'=>'rgba(255,255,255,0.22)',
                        'borderWidth'=>1,'borderRadius'=>18,'textColor'=>'#FFFFFF',
                        'backdropBlur'=>18,
                    ],
                    'actionRef'=>'goLogin',
                ],
                [
                    'id'=>'welcome_create_account','type'=>'textButton',
                    'text'=>'Create an account',
                    'position'=>['bottom'=>48,'horizontalCenter'=>true],
                    'style'=>['fontSize'=>12,'fontWeight'=>500,'letterSpacing'=>0,'color'=>'#FFFFFF'],
                    'actionRef'=>'goSignup',
                ],
                [
                    'id'=>'welcome_legal_links','type'=>'linkRow','className'=>'ef-legal-links','sourceClass'=>'ef-legal-links',
                    'position'=>['left'=>20,'right'=>20,'bottom'=>'calc(14px + safeAreaBottom)'],
                    'style'=>['fontSize'=>10,'fontWeight'=>700,'letterSpacing'=>0,'gap'=>14,'color'=>'rgba(255,255,255,0.82)'],
                    'links'=>[
                        ['id'=>'welcome_documentation','text'=>'Documentation','actionRef'=>'openDocumentation','url'=>'https://efactor.tech/index.php/knowledge_base'],
                        ['id'=>'welcome_privacy_policy','text'=>'Privacy Policy','actionRef'=>'openPrivacyPolicy','url'=>'https://efactor.tech/privacy_policy.html'],
                        ['id'=>'welcome_term_of_use','text'=>'Term of Use','actionRef'=>'openTerms','url'=>'https://efactor.tech/user_agreement.html'],
                    ],
                ],
            ],
        ];
    }
    private function loginPage(): array
    {
        return [
            'id'=>'login','route'=>'/sd-login','type'=>'screen','title'=>'Sign In','analyticsName'=>'auth_login',
            // Keep the login screen fixed when the keypad opens. The glass card
            // stays put and scrolls inside, the same way signup does.
            'scrollable'=>true,'safeArea'=>true,'keyboardAvoidance'=>false,
            'verticalOverflow'=>'scroll',
            'sourcePage'=>$this->sourcePageMeta('login'),
            'pageLayout'=>$this->pageLayoutSpec('login'),
            'presentation'=>'phoneFrameGlassmorphism',
            // Responsive: glass card expands to fill on phones, max-width capped on tablets.
            // Phone breakpoints explicitly keep keyboard avoidance off so the app-level
            // true value cannot push this screen up.
            'responsiveLayout' => [
                'narrow'   => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 'fill', 'padding' => ['left' => 16, 'right' => 16]]],
                'compact'  => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 'fill', 'padding' => ['left' => 24, 'right' => 24]]],
                'medium'   => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 480, 'padding' => ['left' => 32, 'right' => 32]], 'maxContentWidth' => 560],
                'expanded' => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 520, 'padding' => ['left' => 40, 'right' => 40]], 'maxContentWidth' => 600],
                'all' => [
                    'landscape' => [
                        'scrollable'        => true,
                        'verticalOverflow'  => 'scroll',
                        'keyboardAvoidance' => false,
                        'glassPanel'        => ['maxWidth' => 460],
                    ],
                ],
            ],
            'background'=>[
                'asset'=>'authHeroBackground',
                'fit'=>'cover',
                'alignment'=>'bottomCenter',
                'overlay'=>[
                    'type'=>'layered',
                    'colors'=>['rgba(32,41,64,0.78)','rgba(31,21,12,0.72)','rgba(0,0,0,0.34)'],
                ],
            ],
            'components'=>[
                [
                    'id'=>'login_back','type'=>'iconButton','icon'=>'arrow-left','iconRef'=>'arrow-left','iconAsset'=>$this->svgIcon('arrow-left'),
                    'position'=>['top'=>22,'left'=>22],
                    'size'=>['width'=>42,'height'=>42],
                    'style'=>$this->glassIconButtonStyle(),
                    'actionRef'=>'goWelcome',
                ],
                ['id'=>'login_logo','type'=>'image','asset'=>'logo','position'=>['top'=>34,'right'=>24],'size'=>['width'=>108,'height'=>38]],
                [
                    'id'=>'login_glass_panel','type'=>'glassPanel','className'=>'ef-card','sourceClass'=>'ef-card',
                    'position'=>['left'=>24,'right'=>24,'top'=>88],
                    'mainAxisSize'=>'min',
                    'style'=>array_merge($this->glassPanelStyle(), ['height'=>'wrap','mainAxisSize'=>'min']),
                    'children'=>[
                        ['id'=>'login_title','type'=>'text','text'=>'Welcome Back','style'=>$this->glassTitleStyle()],
                        ['id'=>'login_subtitle','type'=>'text','text'=>'Login to your account','style'=>$this->glassSubtitleStyle(),'margin'=>['top'=>8,'bottom'=>22]],
                        $this->input('login_identity','textInput','identity','loginForm.identity','Enter Mobile Number','phone_android','login.identity',['inputType'=>'phone','keyboardType'=>'number','autocomplete'=>'tel','maxLength'=>191]),
                        $this->input('login_password','passwordInput','password','loginForm.password','Password','lock_outline','login.password',['autocomplete'=>'current-password','maxLength'=>72,'trailingAction'=>'togglePasswordVisibility']),
                        [
                            'id'=>'login_options_row','type'=>'row','mainAxisAlignment'=>'spaceBetween',
                            'children'=>[
                                ['id'=>'login_remember','type'=>'checkbox','name'=>'remember_ui','stateKey'=>'loginForm.remember_ui','text'=>'Remember me','style'=>['activeColor'=>'#E8EDF2','textColor'=>'rgba(248,250,252,0.72)','fontSize'=>11,'letterSpacing'=>0]],
                                ['id'=>'login_forgot','type'=>'textButton','text'=>'Forgot Password?','enabled'=>true,'style'=>['color'=>'#FFFFFF','fontSize'=>11,'fontWeight'=>800,'letterSpacing'=>0],'actionRef'=>'goForgotPassword'],
                            ],
                        ],
                        ['id'=>'login_submit','type'=>'button','text'=>'Login','loadingText'=>'Signing in...','variant'=>'primary','size'=>['height'=>50,'width'=>'fill'],'style'=>$this->glassPrimaryButtonStyle(),'validationGroup'=>'login','actionRef'=>'submitLogin'],
                        ['id'=>'login_footer','type'=>'inlineText','alignment'=>'center','margin'=>['top'=>16],'spans'=>[
                            ['text'=>"Don't have an account? ",'style'=>['color'=>'rgba(248,250,252,0.72)','fontSize'=>11,'letterSpacing'=>0]],
                            ['text'=>'Sign up','style'=>['color'=>'#FFFFFF','fontSize'=>11,'fontWeight'=>800,'letterSpacing'=>0],'actionRef'=>'goSignup'],
                        ]],
                    ],
                ],
            ],
        ];
    }
    private function signupPage(): array
    {
        return [
            'id'=>'signup','route'=>'/sd-signup','type'=>'screen','title'=>'Register','analyticsName'=>'auth_signup',
            // Keep the signup screen fixed when the keypad opens. Lifting or
            // recentering the glass card covers the logo and drops taps.
            // The panel scrolls internally; action payloads are unchanged.
            'scrollable'=>true,'safeArea'=>true,'keyboardAvoidance'=>false,
            'verticalOverflow'=>'scroll',
            'sourcePage'=>$this->sourcePageMeta('signup'),
            'pageLayout'=>$this->pageLayoutSpec('signup'),
            'presentation'=>'phoneFrameGlassmorphism',
            // Phone breakpoints explicitly keep keyboard avoidance off so the
            // app-level true value cannot push this screen up.
            'responsiveLayout' => [
                'narrow'   => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 'fill', 'padding' => ['left' => 16, 'right' => 16]]],
                'compact'  => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 'fill', 'padding' => ['left' => 24, 'right' => 24]]],
                'medium'   => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 480, 'padding' => ['left' => 32, 'right' => 32]], 'maxContentWidth' => 560],
                'expanded' => ['scrollable' => true, 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 520, 'padding' => ['left' => 40, 'right' => 40]], 'maxContentWidth' => 600],
                'all'      => ['landscape' => ['scrollable' => true, 'verticalOverflow' => 'scroll', 'keyboardAvoidance' => false, 'glassPanel' => ['maxWidth' => 460]]],
            ],
            'background'=>[
                'asset'=>'authHeroBackground',
                'fit'=>'cover',
                'alignment'=>'bottomCenter',
                'overlay'=>['type'=>'layered','colors'=>['rgba(32,41,64,0.78)','rgba(31,21,12,0.72)','rgba(0,0,0,0.36)']],
            ],
            'components'=>[
                ['id'=>'signup_back','type'=>'iconButton','icon'=>'arrow-left','iconRef'=>'arrow-left','iconAsset'=>$this->svgIcon('arrow-left'),'position'=>['top'=>18,'left'=>16],'size'=>['width'=>42,'height'=>42],'style'=>$this->glassIconButtonStyle(),'actionRef'=>'goWelcome'],
                [
                    'id'=>'signup_glass_panel','type'=>'glassPanel','className'=>'ef-card','sourceClass'=>'ef-card',
                    'position'=>['left'=>16,'right'=>16,'top'=>72,'bottom'=>16],
                    'scrollInside'=>true,
                    'hideScrollbar'=>true,
                    'style'=>array_merge($this->glassPanelStyle(), ['padding'=>['top'=>20,'right'=>18,'bottom'=>18,'left'=>18],'borderRadius'=>26]),
                    'children'=>[
                        ['id'=>'signup_logo','type'=>'image','asset'=>'logo','margin'=>['bottom'=>14],'size'=>['width'=>148,'height'=>48],'style'=>['fit'=>'contain','backgroundColor'=>'#FFFFFF','borderRadius'=>10,'alignment'=>'centerLeft']],
                        ['id'=>'signup_title','type'=>'text','text'=>'Get Started','style'=>array_merge($this->glassTitleStyle(), ['fontSize'=>28])],
                        ['id'=>'signup_subtitle','type'=>'text','text'=>'Create your secure Efactor account','style'=>$this->glassSubtitleStyle(),'margin'=>['top'=>6,'bottom'=>16]],
                        $this->input('signup_email','textInput','email','signupForm.email','Email address','mail_outline','signup.email',['inputType'=>'email','autocomplete'=>'email','maxLength'=>191,'style'=>$this->glassInputStyle()]),
                        $this->input('signup_phone','textInput','phone','signupForm.phone','Enter phone number','phone_android','signup.phone',[
                            'style'=>$this->glassInputStyle(),
                            'inputType'=>'phone',
                            'keyboardType'=>'number',
                            'autocomplete'=>'tel',
                            'minLength'=>10,
                            'maxLength'=>10,
                        ]),
                        $this->input('signup_password','passwordInput','password','signupForm.password','Password','lock_outline','signup.password',['style'=>$this->glassInputStyle(),'autocomplete'=>'new-password','maxLength'=>72,'trailingAction'=>'togglePasswordVisibility']),
                        [
                            'id'=>'signup_password_strength','type'=>'passwordStrength','sourceStateKey'=>'signupForm.password',
                            'rules'=>['minLength8','uppercase','lowercase','number','special'],
                            'caption'=>'Upper / lower / number / special',
                            'labels'=>['Password strength','Very weak','Weak','Fair','Strong','Excellent'],
                            'levels'=>[
                                ['score'=>0,'label'=>'Password strength','color'=>'rgba(255,255,255,0.18)','minPassedRules'=>0],
                                ['score'=>1,'label'=>'Very weak','color'=>'#EF4444','minPassedRules'=>1],
                                ['score'=>2,'label'=>'Weak','color'=>'#F97316','minPassedRules'=>2],
                                ['score'=>3,'label'=>'Fair','color'=>'#F59E0B','minPassedRules'=>3],
                                ['score'=>4,'label'=>'Strong','color'=>'#22C55E','minPassedRules'=>4],
                                ['score'=>5,'label'=>'Excellent','color'=>'#16A34A','minPassedRules'=>5],
                            ],
                            'style'=>['height'=>4,'trackColor'=>'rgba(255,255,255,0.16)','borderRadius'=>999,'marginTop'=>5,'labelColor'=>'rgba(248,250,252,0.72)','labelFontSize'=>9,'captionColor'=>'rgba(248,250,252,0.72)','captionFontSize'=>9,'gap'=>9],
                        ],
                        $this->input('signup_confirm_password','passwordInput','confirm_password','signupForm.confirm_password','Confirm password','verified_user_outlined','signup.confirm_password',['style'=>$this->glassInputStyle(),'autocomplete'=>'new-password','maxLength'=>72,'trailingAction'=>'togglePasswordVisibility']),
                        ['id'=>'signup_send_otp','type'=>'button','text'=>'Send OTP','resendText'=>'Resend OTP','loadingText'=>'Sending OTP...','variant'=>'primary','cooldownSeconds'=>30,'size'=>['height'=>50,'width'=>'fill'],'style'=>array_merge($this->glassPrimaryButtonStyle(), ['backgroundColor'=>'#243044','borderRadius'=>14,'disabledOpacity'=>1]),'actionRef'=>'sendSignupOtp','validationGroup'=>'signupOtpRequest','margin'=>['top'=>8,'bottom'=>12]],
                        [
                            'id'=>'signup_otp_boxes','type'=>'otpInput','name'=>'otp','stateKey'=>'signupForm.otp',
                            'label'=>'Enter OTP',
                            'digitCount'=>6,'boxIds'=>['signup_otp_digit_1','signup_otp_digit_2','signup_otp_digit_3','signup_otp_digit_4','signup_otp_digit_5','signup_otp_digit_6'],
                            'inputMode'=>'numeric','visibleStateKey'=>'signupForm.otpVisible','visibility'=>['dependsOn'=>'signupForm.otpVisible','condition'=>'equals','value'=>true],'visibleWhen'=>['stateKey'=>'signupForm.otpVisible','condition'=>'equals','value'=>true],
                            'hiddenByDefault'=>true,'validationRef'=>'signup.otp',
                            'labelStyle'=>['color'=>'rgba(248,250,252,0.72)','fontSize'=>10,'fontWeight'=>800,'letterSpacing'=>0],
                            'style'=>['boxSize'=>40,'boxHeight'=>46,'gap'=>8,'borderRadius'=>10,'backgroundColor'=>'rgba(255,255,255,0.12)','borderColor'=>'rgba(255,255,255,0.22)','focusBorderColor'=>'rgba(232,237,242,0.68)','textColor'=>'#FFFFFF','fontSize'=>18,'fontWeight'=>700,'letterSpacing'=>0],
                        ],
                        [
                            'id'=>'signup_otp_actions','type'=>'row','mainAxisAlignment'=>'center','gap'=>8,
                            'visibility'=>['dependsOn'=>'signupForm.otpVisible','condition'=>'equals','value'=>true],
                            'visibleWhen'=>['stateKey'=>'signupForm.otpVisible','condition'=>'equals','value'=>true],
                            'hiddenByDefault'=>true,
                            'margin'=>['top'=>10],
                            'children'=>[
                                ['id'=>'signup_verify_otp','type'=>'button','text'=>'Verify','verifiedText'=>'Verified','variant'=>'glassPill','size'=>['height'=>44,'width'=>'fill'],'verifiedStateKey'=>'signupForm.otpVerified','enabledWhen'=>['validationGroup'=>'signupOtpVerification'],'visibility'=>['dependsOn'=>'signupForm.otpVisible','condition'=>'equals','value'=>true],'visibleWhen'=>['stateKey'=>'signupForm.otpVisible','condition'=>'equals','value'=>true],'hiddenByDefault'=>true,'style'=>['backgroundColor'=>'rgba(255,255,255,0.18)','verifiedBackgroundColor'=>'rgba(22,101,52,0.25)','borderColor'=>'rgba(255,255,255,0.55)','verifiedBorderColor'=>'rgba(134,239,172,0.45)','textColor'=>'#FFFFFF','verifiedTextColor'=>'#DCFCE7','borderWidth'=>1,'borderRadius'=>14,'fontSize'=>13,'fontWeight'=>850,'letterSpacing'=>0],'actionRef'=>'verifySignupOtp','validationGroup'=>'signupOtpVerification'],
                            ],
                        ],
                        ['id'=>'signup_terms','type'=>'checkboxWithInlineLink','name'=>'terms','stateKey'=>'signupForm.terms','valueWhenChecked'=>true,'text'=>'I agree to the ','linkText'=>'Terms & Conditions','validationRef'=>'signup.terms','linkActionRef'=>'openTerms','margin'=>['top'=>4,'bottom'=>12],'style'=>['activeColor'=>'#FFFFFF','textColor'=>'rgba(248,250,252,0.84)','linkColor'=>'#FFFFFF','fontSize'=>12,'letterSpacing'=>0]],
                        ['id'=>'signup_submit','type'=>'button','text'=>'Create account','loadingText'=>'Creating account...','size'=>['height'=>50,'width'=>'fill'],'style'=>array_merge($this->glassPrimaryButtonStyle(), ['backgroundColor'=>'#243044','disabledOpacity'=>0.55,'borderRadius'=>14]),'validationGroup'=>'signup','enabledWhen'=>['validationGroup'=>'signup'],'actionRef'=>'submitSignup'],
                        ['id'=>'signup_footer','type'=>'inlineText','alignment'=>'center','margin'=>['top'=>14],'spans'=>[
                            ['text'=>'Already have an account? ','style'=>['color'=>'rgba(248,250,252,0.72)','fontSize'=>11,'letterSpacing'=>0]],
                            ['text'=>'Login','style'=>['color'=>'#FFFFFF','fontSize'=>11,'fontWeight'=>800,'letterSpacing'=>0],'actionRef'=>'goLogin'],
                        ]],
                    ],
                ],
            ],
        ];
    }
    private function forgotPasswordPage(): array
    {
        return [
            'id'=>'forgotPassword','route'=>'/sd-forgot-password','type'=>'screen','title'=>'Reset Password','analyticsName'=>'auth_forgot_password',
            'scrollable'=>true,'safeArea'=>true,'keyboardAvoidance'=>true,
            'sourcePage'=>$this->sourcePageMeta('forgotPassword'),
            'pageLayout'=>$this->pageLayoutSpec('forgotPassword'),
            'presentation'=>'phoneFrameGlassmorphism',
            // Responsive: same tablet-cap pattern as login/signup.
            'responsiveLayout' => [
                'narrow'   => ['glassPanel' => ['maxWidth' => 'fill', 'padding' => ['left' => 16, 'right' => 16]]],
                'compact'  => ['glassPanel' => ['maxWidth' => 'fill', 'padding' => ['left' => 24, 'right' => 24]]],
                'medium'   => ['glassPanel' => ['maxWidth' => 480, 'padding' => ['left' => 32, 'right' => 32]], 'maxContentWidth' => 560],
                'expanded' => ['glassPanel' => ['maxWidth' => 520, 'padding' => ['left' => 40, 'right' => 40]], 'maxContentWidth' => 600],
                'all'      => ['landscape' => ['scrollable' => true, 'verticalOverflow' => 'scroll', 'glassPanel' => ['maxWidth' => 460]]],
            ],
            'background'=>[
                'asset'=>'authHeroBackground',
                'fit'=>'cover',
                'alignment'=>'bottomCenter',
                'overlay'=>['type'=>'layered','colors'=>['rgba(32,41,64,0.72)','rgba(31,21,12,0.78)','rgba(0,0,0,0.35)']],
            ],
            'components'=>[
                ['id'=>'forgot_back','type'=>'iconButton','icon'=>'arrow-left','iconRef'=>'arrow-left','iconAsset'=>$this->svgIcon('arrow-left'),'position'=>['top'=>30,'left'=>22],'size'=>['width'=>42,'height'=>42],'style'=>$this->glassIconButtonStyle(),'actionRef'=>'goLogin'],
                ['id'=>'forgot_logo','type'=>'image','asset'=>'logo','position'=>['top'=>36,'right'=>24],'size'=>['width'=>108,'height'=>38]],
                [
                    'id'=>'forgot_glass_panel','type'=>'glassPanel','className'=>'ef-card','sourceClass'=>'ef-card',
                    'position'=>['left'=>24,'right'=>24,'verticalCenter'=>true],
                    'scrollInside'=>true,
                    'hideScrollbar'=>true,
                    'style'=>$this->glassPanelStyle(),
                    'children'=>[
                        ['id'=>'forgot_title','type'=>'text','text'=>'Reset Password','style'=>$this->glassTitleStyle()],
                        ['id'=>'forgot_subtitle','type'=>'text','text'=>'Enter your mobile number and OTP to set a new password.','style'=>$this->glassSubtitleStyle(),'margin'=>['top'=>8,'bottom'=>20]],
                        $this->input('forgot_phone','textInput','phone','forgotPasswordForm.phone','10-digit mobile number','phone_android','forgotPassword.phone',[
                            'inputType'=>'phone',
                            'keyboardType'=>'number',
                            'autocomplete'=>'tel',
                            'minLength'=>10,
                            'maxLength'=>10,
                            'events'=>[
                                'onLengthReached'=>[
                                    'length'=>10,
                                    'actionRef'=>'sendForgotPasswordOtp',
                                    'once'=>true,
                                    'skipIfStateKey'=>'forgotPasswordForm.otpVisible',
                                ],
                            ],
                        ]),
                        ['id'=>'forgot_send_otp','type'=>'textButton','text'=>'Resend OTP','resendText'=>'Resend OTP','style'=>['color'=>'#FFFFFF','fontSize'=>11,'fontWeight'=>850,'letterSpacing'=>0,'alignment'=>'right'],'actionRef'=>'sendForgotPasswordOtp','validationGroup'=>'forgotOtpRequest','visibility'=>['dependsOn'=>'forgotPasswordForm.otpVisible','condition'=>'equals','value'=>true]],
                        ['id'=>'forgot_otp_boxes','type'=>'otpInput','name'=>'otp','stateKey'=>'forgotPasswordForm.otp','digitCount'=>6,'boxIds'=>['forgot_otp_digit_1','forgot_otp_digit_2','forgot_otp_digit_3','forgot_otp_digit_4','forgot_otp_digit_5','forgot_otp_digit_6'],'inputMode'=>'numeric','visibleStateKey'=>'forgotPasswordForm.otpVisible','visibility'=>['dependsOn'=>'forgotPasswordForm.otpVisible','condition'=>'equals','value'=>true],'hiddenByDefault'=>true,'validationRef'=>'forgotPassword.otp','style'=>['boxSize'=>42,'gap'=>7,'borderRadius'=>9,'backgroundColor'=>'rgba(255,255,255,0.12)','borderColor'=>'rgba(255,255,255,0.22)','focusBorderColor'=>'rgba(232,237,242,0.68)','textColor'=>'#FFFFFF','fontSize'=>16,'fontWeight'=>850,'letterSpacing'=>0]],
                        $this->input('forgot_password','passwordInput','password','forgotPasswordForm.password','New password','lock_outline','forgotPassword.password',['autocomplete'=>'new-password','maxLength'=>72,'trailingAction'=>'togglePasswordVisibility']),
                        $this->input('forgot_confirm_password','passwordInput','confirm_password','forgotPasswordForm.confirm_password','Confirm password','verified_user_outlined','forgotPassword.confirm_password',['autocomplete'=>'new-password','maxLength'=>72,'trailingAction'=>'togglePasswordVisibility']),
                        ['id'=>'forgot_submit','type'=>'button','text'=>'Reset password','loadingText'=>'Resetting password...','size'=>['height'=>49,'width'=>'fill'],'style'=>$this->glassPrimaryButtonStyle(),'validationGroup'=>'forgotPassword','actionRef'=>'submitForgotPassword'],
                        ['id'=>'forgot_footer','type'=>'inlineText','alignment'=>'center','spans'=>[
                            ['text'=>'Remembered it? ','style'=>['color'=>'rgba(248,250,252,0.72)','fontSize'=>11,'letterSpacing'=>0]],
                            ['text'=>'Sign in','style'=>['color'=>'#FFFFFF','fontSize'=>11,'fontWeight'=>800,'letterSpacing'=>0],'actionRef'=>'goLogin'],
                        ]],
                    ],
                ],
            ],
        ];
    }
    private function sourcePageMeta(string $pageId): array
    {
        $sourcePageMap = ['forgotPassword' => 'forgot_password'];
        $sourcePageId = $sourcePageMap[$pageId] ?? $pageId;
        $relativePath = 'Views/sd_auth/' . $sourcePageId . '.php';
        $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $source = is_file($absolutePath) ? (string) file_get_contents($absolutePath) : '';
        $source = $this->stripWebOnlySchemaTools($source);
        $style = $this->extractStyleBlock($source);
        $desktopStyle = preg_split('/@media/i', $style, 2)[0] ?? $style;
        $domMap = $this->extractDomMap($source, $pageId);

        return [
            'view' => $relativePath,
            'generatedFromView' => $source !== '',
            'contentHash' => $source !== '' ? hash('sha256', $source) : null,
            'cssVariables' => $this->extractCssVariables($style),
            'classStyles' => $this->extractClassStyles($desktopStyle),
            'mobileOverrides' => $this->extractMobileOverrides($style),
            'assetUrls' => $this->extractAssetUrls($source),
            'domMap' => $domMap['active'],
            'obsoleteDomMap' => $domMap['obsolete'],
        ];
    }
    private function stripWebOnlySchemaTools(string $source): string
    {
        return (string) preg_replace(
            '/\s*<div\s+class="ef-web-schema-tools"\b.*?<\/div>\s*<div\s+class="ef-phone"/is',
            "\n  <div class=\"ef-phone\"",
            $source
        );
    }
    private function pageLayoutSpec(string $pageId): array
    {
        $isWelcome = $pageId === 'welcome';
        $heroHeight = $pageId === 'signup' ? 230 : 292;
        $mobileHeroHeight = $pageId === 'signup' ? 220 : 280;

        return [
            'layoutEngine' => 'auth-phone-preview',
            'sourceDriven' => true,
            'desktopViewport' => [
                'page' => ['minHeight' => '100vh', 'display' => 'grid', 'placeItems' => 'center', 'padding' => 24],
                'phone' => [
                    'position' => 'relative',
                    'width' => ['type' => 'min', 'values' => ['100%', 390]],
                    'height' => ['type' => 'min', 'values' => [820, 'calc(100vh - 48px)']],
                    'minHeight' => 620,
                    'padding' => 8,
                    'borderRadius' => 42,
                    'backgroundColor' => '#101813',
                    'clip' => true,
                    'notch' => ['visible' => true, 'width' => 112, 'height' => 27, 'top' => 8],
                ],
                'screen' => [
                    'position' => 'relative',
                    'height' => '100%',
                    'borderRadius' => 34,
                    'overflowX' => 'hidden',
                    'overflowY' => ($isWelcome || $pageId === 'signup' || $pageId === 'login') ? 'hidden' : 'auto',
                    'backgroundColor' => ($isWelcome || $pageId === 'signup') ? null : '#FFFFFF',
                    'backgroundAsset' => $isWelcome ? 'welcomeBackground' : ($pageId === 'signup' ? 'authHeroBackground' : null),
                ],
            ],
            'mobileViewport' => [
                // Fix #2: align with responsive.breakpoints.compact (360–599).
                // 430 was an old hardcoded phone width — use the schema breakpoint instead.
                'breakpointMaxWidth' => 599,
                'page' => ['display' => 'block', 'minHeight' => '100dvh', 'padding' => 0],
                'phone' => [
                    'width' => '100%',
                    'height' => '100dvh',
                    'minHeight' => '100dvh',
                    'padding' => 0,
                    'borderRadius' => 0,
                    'boxShadow' => 'none',
                    'notch' => ['visible' => false],
                ],
                'screen' => ['borderRadius' => 0],
                'hero' => $isWelcome ? null : ['minHeight' => $pageId === 'signup' ? 0 : $mobileHeroHeight],
            ],
            // Responsive breakpoint overrides for the page layout container.
            // Flutter deep-merges the selected entry into mobileViewport above.
            'responsiveLayout' => [
                'narrow'   => [
                    'contentPadding'  => ['horizontal' => 16, 'vertical' => 20],
                    'formMaxWidth'    => null,
                    'phoneMaxWidth'   => null,
                ],
                'compact'  => [
                    'contentPadding'  => ['horizontal' => 24, 'vertical' => 24],
                    'formMaxWidth'    => null,
                    'phoneMaxWidth'   => 'fill',
                ],
                'medium'   => [
                    'contentPadding'  => ['horizontal' => 40, 'vertical' => 32],
                    'formMaxWidth'    => 480,
                    'phoneMaxWidth'   => 560,
                    'centerContent'   => true,
                ],
                'expanded' => [
                    'contentPadding'  => ['horizontal' => 64, 'vertical' => 40],
                    'formMaxWidth'    => 520,
                    'phoneMaxWidth'   => 600,
                    'centerContent'   => true,
                ],
            ],
            'regions' => $isWelcome ? $this->welcomeRegions() : $this->authFormRegions($pageId, $heroHeight),
            'renderOrder' => $isWelcome
                ? ['phone', 'screen', 'brand', 'copy', 'actions', 'legalLinks']
                : ['phone', 'screen', 'backButton', 'hero', 'brand', 'card', 'form', 'footer'],
        ];
    }

    private function welcomeRegions(): array
    {
        return [
            'brand' => ['position' => 'absolute', 'top' => 38, 'left' => 24, 'zIndex' => 4, 'logoSize' => ['width' => 132, 'maxHeight' => 46]],
            'copy' => ['position' => 'absolute', 'left' => 27, 'right' => 27, 'bottom' => 182, 'zIndex' => 4],
            'heading' => ['maxWidth' => 305, 'fontSizeRem' => 2.14, 'lineHeight' => 1.08, 'fontWeight' => 700, 'letterSpacingEm' => -0.045],
            'description' => ['maxWidth' => 315, 'marginTop' => 13, 'fontSizeRem' => 0.77, 'lineHeight' => 1.55],
            'actions' => ['position' => 'absolute', 'left' => 20, 'right' => 20, 'bottom' => 44, 'zIndex' => 5],
            'legalLinks' => ['position' => 'absolute', 'left' => 20, 'right' => 20, 'bottom' => 4, 'zIndex' => 6, 'gap' => 14],
        ];
    }

    private function authFormRegions(string $pageId, int $heroHeight): array
    {
        $isSignup = $pageId === 'signup';

        return [
            'backButton' => ['position' => 'absolute', 'top' => $isSignup ? 30 : 29, 'left' => $isSignup ? 22 : 19, 'width' => $isSignup ? 42 : 36, 'height' => $isSignup ? 42 : 36, 'zIndex' => 25],
            'hero' => [
                'position' => 'relative',
                'minHeight' => $isSignup ? 0 : $heroHeight,
                'backgroundAsset' => 'authHeroBackground',
                'backgroundPosition' => $isSignup ? 'center bottom' : 'center 32%',
                'curve' => ['bottom' => $isSignup ? 0 : -78, 'height' => $isSignup ? 0 : 136, 'color' => $isSignup ? 'transparent' : '#FFFFFF'],
            ],
            'brand' => ['position' => 'absolute', 'top' => $isSignup ? 34 : 35, 'right' => 24, 'zIndex' => $pageId === 'login' ? 20 : 4, 'logoSize' => ['maxWidth' => $isSignup ? 108 : 104, 'maxHeight' => 38]],
            'card' => ['position' => 'relative', 'zIndex' => 7, 'marginTop' => $isSignup ? 0 : -61, 'padding' => $isSignup ? ['top' => 24, 'right' => 22, 'bottom' => 21, 'left' => 22] : ['top' => 0, 'right' => 28, 'bottom' => 27, 'left' => 28], 'backgroundColor' => $isSignup ? 'rgba(18,24,38,0.58)' : '#FFFFFF'],
            'heading' => ['textAlign' => $isSignup ? 'left' : 'center', 'marginBottom' => $isSignup ? 18 : 21],
            'form' => ['fieldGap' => $isSignup ? 10 : 12, 'inputHeight' => $isSignup ? 46 : 50, 'inputRadius' => $isSignup ? 12 : 14],
            'footer' => ['padding' => ['top' => 4, 'right' => 24, 'bottom' => $isSignup ? 24 : 25, 'left' => 24], 'backgroundColor' => $isSignup ? 'transparent' : '#FFFFFF'],
        ];
    }

    private function extractStyleBlock(string $source): string
    {
        if (preg_match('/<style[^>]*>(.*?)<\/style>/is', $source, $match)) {
            return trim((string) $match[1]);
        }

        return '';
    }

    private function extractCssVariables(string $style): array
    {
        preg_match_all('/--([a-zA-Z0-9_-]+)\s*:\s*([^;]+);/', $style, $matches, PREG_SET_ORDER);
        $vars = [];
        foreach ($matches as $match) {
            $vars[$match[1]] = trim($match[2]);
        }

        return $vars;
    }

    private function extractClassStyles(string $style): array
    {
        preg_match_all('/(^|\})\s*(\.[a-zA-Z0-9_-]+)\s*\{([^{}]*)\}/m', $style, $matches, PREG_SET_ORDER);
        $classes = [];
        foreach ($matches as $match) {
            $className = ltrim($match[2], '.');
            if (strpos($className, 'ef-web-schema') === 0) {
                continue;
            }
            $classes[$className] = $this->parseCssDeclarations((string) $match[3]);
        }

        return $classes;
    }

    private function extractMobileOverrides(string $style): array
    {
        if (! preg_match('/@media\(max-width:430px\)\s*\{(.*?)\}\s*@media/is', $style . '@media', $match)) {
            return [];
        }

        return $this->extractClassStyles((string) $match[1]);
    }

    private function parseCssDeclarations(string $block): array
    {
        $declarations = [];
        foreach (explode(';', $block) as $part) {
            $pieces = explode(':', $part, 2);
            if (count($pieces) !== 2) {
                continue;
            }
            $key = trim($pieces[0]);
            $value = trim($pieces[1]);
            if ($key !== '' && $value !== '') {
                $declarations[$key] = $value;
            }
        }

        return $declarations;
    }

    private function extractAssetUrls(string $source): array
    {
        preg_match_all('/base_url\(\'([^\']+)\'\)/', $source, $matches);
        $assets = [];
        foreach ($matches[1] ?? [] as $assetPath) {
            $assets[] = base_url($assetPath);
        }

        return array_values(array_unique($assets));
    }

    private function extractDomMap(string $source, string $pageId = ''): array
    {
        preg_match_all('/<(div|main|section|form|a|button|input|img|h1|p|span)\b([^>]*)>/i', $source, $matches, PREG_SET_ORDER);
        $active = [];
        $obsolete = [];
        $activeOrder = 0;
        foreach ($matches as $index => $match) {
            $attrs = (string) $match[2];
            if (strpos($attrs, 'ef-web-schema') !== false) {
                continue;
            }
            preg_match('/\bclass="([^"]+)"/i', $attrs, $classMatch);
            preg_match('/\bid="([^"]+)"/i', $attrs, $idMatch);
            preg_match('/\bname="([^"]+)"/i', $attrs, $nameMatch);
            preg_match('/\btype="([^"]+)"/i', $attrs, $typeMatch);
            preg_match('/\bplaceholder="([^"]+)"/i', $attrs, $placeholderMatch);

            $node = [
                'order' => $index + 1,
                'tag' => strtolower($match[1]),
                'id' => $idMatch[1] ?? null,
                'classes' => isset($classMatch[1]) ? preg_split('/\s+/', trim($classMatch[1])) : [],
                'name' => $nameMatch[1] ?? null,
                'inputType' => $typeMatch[1] ?? null,
                'placeholder' => $placeholderMatch[1] ?? null,
            ];

            if ($pageId === 'login' && $this->isObsoleteLoginOtpNode($node)) {
                $node['obsolete'] = true;
                $node['reason'] = 'Login OTP is not part of the mobile password-login contract.';
                $obsolete[] = $node;
                continue;
            }

            $activeOrder++;
            $node['order'] = $activeOrder;
            $active[] = $node;
        }

        return ['active' => $active, 'obsolete' => $obsolete];
    }

    private function isObsoleteLoginOtpNode(array $node): bool
    {
        $obsoleteIds = ['loginSendOtpButton', 'loginOtpBadge', 'loginOtpSection', 'login_otp'];
        if (isset($node['id']) && in_array($node['id'], $obsoleteIds, true)) {
            return true;
        }
        if (($node['name'] ?? null) === 'otp') {
            return true;
        }
        $obsoleteClasses = ['ef-send-otp', 'ef-otp', 'ef-otp-badge', 'ef-otp-head', 'ef-otp-boxes', 'ef-otp-box'];
        foreach ($node['classes'] ?? [] as $className) {
            if (in_array($className, $obsoleteClasses, true)) {
                return true;
            }
        }

        return false;
    }
    private function authIcons(): array
    {
        $paths = $this->iconPaths();

        return [
            'library' => [
                'source' => 'inline-svg',
                'style' => 'lucide-feather',
                'count' => count($paths),
                'icons' => array_keys($paths),
                'paths' => $paths,
            ],
            'default' => 'lock',
            'size' => 18,
            'color' => '#202940',
        ];
    }

    private function iconPaths(): array
    {
        return [
            'arrow-left' => '<path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path>',
            'mail' => '<rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>',
            'lock' => '<rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>',
            'phone' => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path>',
            'shield-check' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path>',
            'eye' => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle>',
            'eye-off' => '<path d="M10.733 5.076A10.744 10.744 0 0 1 12 5c5.105 0 9.232 3.254 10.936 7.652a1 1 0 0 1-.059.808 10.7 10.7 0 0 1-1.444 2.022"></path><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path><path d="M17.479 17.499A10.75 10.75 0 0 1 12 19c-5.105 0-9.232-3.254-10.936-7.652a1 1 0 0 1 .059-.808 10.7 10.7 0 0 1 4.158-4.641"></path><path d="m2 2 20 20"></path>',
            'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>',
            'google' => '<path d="M21.8 12.2c0-.7-.1-1.4-.2-2H12v3.8h5.5a4.7 4.7 0 0 1-2 3.1v2.6h3.2c1.9-1.7 3.1-4.3 3.1-7.5z"></path><path d="M12 22c2.7 0 5-0.9 6.7-2.4l-3.2-2.6c-.9.6-2 .9-3.5.9-2.7 0-5-1.8-5.8-4.2H2.9v2.6A10 10 0 0 0 12 22z"></path><path d="M6.2 13.7A6 6 0 0 1 6.2 10.3V7.7H2.9a10 10 0 0 0 0 8.6z"></path><path d="M12 6.1c1.5 0 2.8.5 3.8 1.5l2.8-2.8A9.6 9.6 0 0 0 12 2a10 10 0 0 0-9.1 5.7l3.3 2.6C7 7.9 9.3 6.1 12 6.1z"></path>',
            'apple' => '<path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.53 4.08zM12.03 7.25C11.88 5.02 13.69 3.18 15.77 3c.29 2.58-2.34 4.5-3.74 4.25z"></path>',
        ];
    }

    private function legacyIconName(string $name): string
    {
        $map = [
            'arrow_back_ios_new' => 'arrow-left',
            'mail_outline' => 'mail',
            'lock_outline' => 'lock',
            'phone_android' => 'phone',
            'verified_user_outlined' => 'shield-check',
            'visibility' => 'eye',
            'visibility_off' => 'eye-off',
        ];

        return $map[$name] ?? $name;
    }

    private function svgIcon(string $name): array
    {
        $iconName = $this->legacyIconName($name);
        $paths = $this->iconPaths();

        if ($iconName === 'google') {
            $svg = '<svg viewBox="0 0 24 24" fill="none"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"></path><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"></path><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"></path><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"></path></svg>';

            return [
                'type' => 'inlineSvg',
                'source' => 'inline-svg',
                'style' => 'brand-multicolor',
                'name' => 'google',
                'viewBox' => '0 0 24 24',
                'stroke' => 'none',
                'strokeWidth' => 0,
                'fill' => 'multiColor',
                'size' => 18,
                'path' => $paths[$iconName] ?? '',
                'svg' => $svg,
            ];
        }

        $brandIcon = in_array($iconName, ['apple', 'facebook'], true);

        return [
            'type' => 'inlineSvg',
            'source' => 'inline-svg',
            'style' => $brandIcon ? 'brand-filled' : 'lucide-feather',
            'name' => $iconName,
            'viewBox' => '0 0 24 24',
            'stroke' => $brandIcon ? 'none' : 'currentColor',
            'strokeWidth' => $brandIcon ? 0 : 2,
            'strokeLinecap' => 'round',
            'strokeLinejoin' => 'round',
            'fill' => $brandIcon ? 'currentColor' : 'none',
            'size' => 18,
            'path' => $paths[$iconName] ?? '',
            'svg' => $brandIcon
                ? '<svg viewBox="0 0 24 24" fill="currentColor" stroke="none">' . ($paths[$iconName] ?? '') . '</svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . ($paths[$iconName] ?? '') . '</svg>',
        ];
    }    private function input(
        string $id,
        string $type,
        string $name,
        string $stateKey,
        string $placeholder,
        string $leadingIcon,
        string $validationRef,
        array $extra = []
    ): array {
        return array_merge([
            'id'=>$id,
            'type'=>$type,
            'name'=>$name,
            'stateKey'=>$stateKey,
            'placeholder'=>$placeholder,
            'leadingIcon'=>$this->legacyIconName($leadingIcon),
            'leadingIconRef'=>$this->legacyIconName($leadingIcon),
            'leadingIconAsset'=>$this->svgIcon($leadingIcon),
            'trailingIconRefs'=>['eye','eye-off'],
            'trailingIconAssets'=>[
                'eye'=>$this->svgIcon('eye'),
                'eye-off'=>$this->svgIcon('eye-off'),
                'visibility'=>$this->svgIcon('visibility'),
                'visibility_off'=>$this->svgIcon('visibility_off'),
            ],
            'validationRef'=>$validationRef,
            'style'=>$this->inputStyle(),
        ], $extra);
    }

    private function glassIconButtonStyle(): array
    {
        return [
            'shape'=>'circle',
            'backgroundColor'=>'rgba(255,255,255,0.12)',
            'borderColor'=>'rgba(255,255,255,0.18)',
            'borderWidth'=>1,
            'iconColor'=>'#FFFFFF',
            'iconSize'=>18,
            'backdropBlur'=>16,
            'shadow'=>['offsetX'=>0,'offsetY'=>12,'blur'=>30,'color'=>'rgba(0,0,0,0.18)'],
        ];
    }

    private function glassPanelStyle(): array
    {
        return [
            'backgroundColor'=>'rgba(18,24,38,0.58)',
            'borderColor'=>'rgba(255,255,255,0.18)',
            'borderWidth'=>1,
            'borderRadius'=>28,
            'backdropBlur'=>22,
            'padding'=>['top'=>24,'right'=>22,'bottom'=>21,'left'=>22],
            'shadow'=>['offsetX'=>0,'offsetY'=>24,'blur'=>70,'color'=>'rgba(0,0,0,0.32)'],
            'hideScrollbar'=>true,
        ];
    }

    private function glassTitleStyle(): array
    {
        return ['fontSize'=>31,'fontWeight'=>850,'lineHeight'=>1.05,'letterSpacing'=>0,'color'=>'#FFFFFF','textAlign'=>'left'];
    }

    private function glassSubtitleStyle(): array
    {
        return ['fontSize'=>12,'fontWeight'=>400,'lineHeight'=>1.45,'letterSpacing'=>0,'color'=>'rgba(248,250,252,0.72)','textAlign'=>'left'];
    }

    private function glassPrimaryButtonStyle(): array
    {
        return [
            'backgroundColor'=>'linear-gradient(135deg,#202940,#1F150C)',
            'textColor'=>'#FFFFFF',
            'borderColor'=>'rgba(255,255,255,0.20)',
            'borderWidth'=>1,
            'borderRadius'=>14,
            'fontSize'=>13,
            'fontWeight'=>850,
            'letterSpacing'=>0,
            'shadow'=>['offsetX'=>0,'offsetY'=>16,'blur'=>34,'color'=>'rgba(0,0,0,0.24)'],
        ];
    }

    private function mobileAuthUrl(string $action): string
    {
        return base_url('v8/api.php') . '?action=' . rawurlencode($action);
    }

    private function authJsonResponseContract(array $extraFields = []): array
    {
        return [
            'contentType' => 'application/json',
            'successWhen' => ['path' => 'status', 'equals' => 'success'],
            'fields' => array_merge([
                'status' => ['path' => 'status', 'type' => 'string', 'required' => true],
                'description' => ['path' => 'description', 'type' => 'string', 'required' => false],
                'error_code' => ['path' => 'error_code', 'type' => 'string', 'required' => false],
            ], $extraFields),
        ];
    }

    private function authHttpFailure(): array
    {
        return [
            'type' => 'setState',
            'targetStateKey' => 'auth.error',
            'messagePath' => 'description',
            'errorCodePath' => 'error_code',
            'errorCodeStateKey' => 'auth.errorCode',
        ];
    }

    private function loginTokenResponseFields(): array
    {
        return [
            'accesstoken' => ['path' => 'accesstoken', 'type' => 'string', 'required' => true],
            'idtoken' => ['path' => 'idtoken', 'type' => 'string', 'required' => true],
            'refreshtoken' => ['path' => 'refreshtoken', 'type' => 'string', 'required' => true],
            'username' => ['path' => 'username', 'type' => 'string', 'required' => false],
            'email' => ['path' => 'email', 'type' => 'string', 'required' => false],
            'user_type' => ['path' => 'user_type', 'type' => 'string', 'required' => false],
            'user_id' => ['path' => 'user_id', 'type' => 'string', 'required' => false],
        ];
    }

    private function postAuthSuccess(array $session): array
    {
        return [
            'type' => 'navigate',
            'targetPageId' => 'home',
            'destination' => 'appHome',
            'exitAuthFlow' => true,
            'fallbackUrl' => site_url('sd-dashboard'),
            'setAuthenticated' => true,
            'session' => $session,
        ];
    }

    private function sessionFields(): array
    {
        return [
            'accesstoken' => ['path' => 'accesstoken', 'stateKey' => 'auth.accesstoken'],
            'idtoken' => ['path' => 'idtoken', 'stateKey' => 'auth.idtoken'],
            'refreshtoken' => ['path' => 'refreshtoken', 'stateKey' => 'auth.refreshtoken'],
            'username' => ['path' => 'username', 'stateKey' => 'auth.username'],
            'email' => ['path' => 'email', 'stateKey' => 'auth.email'],
            'user_type' => ['path' => 'user_type', 'stateKey' => 'auth.user_type'],
            'user_id' => ['path' => 'user_id', 'stateKey' => 'auth.user_id'],
        ];
    }

    private function loginSessionContract(): array
    {
        return [
            'persist' => '$state.loginForm.remember_ui',
            'fields' => $this->sessionFields(),
        ];
    }

    private function signupSessionContract(): array
    {
        return [
            'persist' => '$state.signupForm.persistSession',
            'fields' => $this->sessionFields(),
        ];
    }

    private function glassInputStyle(): array
    {
        return [
            'height'=>46,
            'backgroundColor'=>'rgba(255,255,255,0.10)',
            'borderRadius'=>12,
            'borderWidth'=>1,
            'borderColor'=>'rgba(255,255,255,0.16)',
            'focusBorderColor'=>'rgba(232,237,242,0.56)',
            'focusRingColor'=>'rgba(232,237,242,0.10)',
            'textColor'=>'#FFFFFF',
            'placeholderColor'=>'rgba(248,250,252,0.62)',
            'iconColor'=>'#E8EDF2',
            'fontSize'=>13,
            'horizontalPadding'=>13,
            'leadingIconSize'=>18,
            'trailingIconSize'=>17,
            'margin'=>['bottom'=>10],
            'errorBorderColor'=>'rgba(252,165,165,0.70)',
            'errorBackgroundColor'=>'rgba(127,29,29,0.20)',
        ];
    }

    private function inputStyle(): array
    {
        return [
            'height'=>50,
            'backgroundColor'=>'#E8EDF2',
            'borderRadius'=>14,
            'borderWidth'=>1,
            'borderColor'=>'#CDD5DF',
            'focusBorderColor'=>'#202940',
            'focusRingColor'=>'rgba(32,41,64,0.10)',
            'textColor'=>'#1E1E1E',
            'placeholderColor'=>'#667085',
            'iconColor'=>'#202940',
            'horizontalPadding'=>14,
            'leadingIconSize'=>18,
            'trailingIconSize'=>18,
        ];
    }

    private function primaryButtonStyle(): array
    {
        return [
            'backgroundColor'=>'#202940',
            'textColor'=>'#FFFFFF',
            'borderRadius'=>18,
            'shadow'=>[
                'offsetX'=>0,'offsetY'=>10,'blur'=>22,
                'color'=>'rgba(32,41,64,0.22)',
            ],
            'hover'=>['translateY'=>-2],
            'press'=>['scale'=>0.98],
        ];
    }
}


<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array
     */
    public $aliases = [
        'csrf'     => CSRF::class,
        'toolbar'  => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'mobilepush' => \App\Filters\MobilePushRequestFilter::class,
        'gridwebsso' => \App\Filters\GridWebSsoFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array
     */
public $globals = [
    'before' => [
        // 'honeypot',
        'csrf' => [
            'except' => [
                'items/specificationsapi',
                'items/test',
                'items/*',

                // TEMPORARY DEBUG TEST
                'finance/*',
                'dms/*',
                'hrms/*',
                'procurement/*',
                'asset-management/*',
                'inventory/*',
                'grid',
                'grid/*',
                'grid-chat',
                'grid-chat/*',
                'api/register_device_token',
                'api/call_action',
                'api/call/decline',
                'grid_chat/api/call/decline',
                'sd-signup/otp',
                'sd-signup/verify',
                'sd-signup/complete',
                'sd-signup/register',
                'sd-signup/verify-remote',
                'sd-signup/login-remote',
                'sd-signup/session',
                'app-home-page/api',
                'app-home-page/api/*',
                'app-web-entry/open',
                
            ]
        ],
    ],
    'after' => [
        'toolbar',
        // 'honeypot',
    ],
];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['csrf', 'throttle']
     *
     * @var array
     */
    public $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array
     */
    public $filters = [
        'gridwebsso' => [
            'before' => [
                'grid',
                'grid/*',
                'grid-chat',
                'grid-chat/*',
                'grid_chat',
                'grid_chat/*',
                'app-web-entry/open',
            ],
        ],
        'mobilepush' => [
            'before' => [
                'api/register_device_token',
                'api/call_action',
                'grid-chat/api/chat/send',
                'grid-chat/api/chat/read',
                'grid-chat/api/chat/call_invite',
                'grid_chat/api/chat/send',
                'grid_chat/api/chat/read',
                'grid_chat/api/chat/call_invite',
                'app-home-page/api',
                'app-home-page/api/*',
            ],
        ],
    ];
}

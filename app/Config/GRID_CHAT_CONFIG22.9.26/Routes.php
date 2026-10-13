<?php

/**
 * -----------------------------------------------------------------------------
 * GRID CHAT Routes
 * Location: app/Config/GRID_CHAT_CONFIG/Routes.php
 * -----------------------------------------------------------------------------
 */

if (!isset($routes)) {
    
    $routes = \Config\Services::routes();
}

$routes->group('', ['namespace' => 'App\Controllers\GRID_CHAT_CONTROLLERS'], static function ($routes) {
    // Web UI entry routes (supports kebab-case and snake_case)
    $routes->get('grid-chat', 'GridChatController::index');
    $routes->get('grid_chat', 'GridChatController::index');

    // Media, Links & Documents dedicated direct routes
    $routes->get('grid-chat/media', 'GridChatController::index');
    $routes->get('grid_chat/media', 'GridChatController::index');
    $routes->get('grid-chat/media/(:any)', 'GridChatController::index/$1');
    $routes->get('grid_chat/media/(:any)', 'GridChatController::index/$1');

    // Assets
    $routes->get('grid-chat/assets/(:any)', 'GridChatController::asset/$1');
    $routes->get('grid_chat/assets/(:any)', 'GridChatController::asset/$1');

    // Chat API (grid_chat_db) — explicit routes so only these actions are callable
    foreach (['grid-chat', 'grid_chat'] as $base) {
        $routes->get("{$base}/api/chat/conversations", 'ChatController::conversations');
        $routes->get("{$base}/api/chat/messages", 'ChatController::messages');
        $routes->get("{$base}/api/chat/sync", 'ChatController::sync');
        $routes->get("{$base}/api/chat/contacts", 'ChatController::contacts');
        $routes->post("{$base}/api/chat/open_direct", 'ChatController::open_direct');
        $routes->post("{$base}/api/chat/send", 'ChatController::send');
        $routes->post("{$base}/api/chat/read", 'ChatController::read');
        $routes->post("{$base}/api/chat/favourite", 'ChatController::favourite');
        $routes->post("{$base}/api/chat/mute", 'ChatController::mute');
        $routes->post("{$base}/api/chat/clear", 'ChatController::clear');
        $routes->post("{$base}/api/chat/delete_chat", 'ChatController::delete_chat');
        $routes->post("{$base}/api/chat/delete_message", 'ChatController::delete_message');
        $routes->post("{$base}/api/chat/vote_poll", 'ChatController::vote_poll');

        // Groups
        $routes->post("{$base}/api/group/create", 'GroupController::create');
        $routes->get("{$base}/api/group/members", 'GroupController::members');
        $routes->post("{$base}/api/group/add_members", 'GroupController::add_members');
        $routes->post("{$base}/api/group/remove_member", 'GroupController::remove_member');
        $routes->post("{$base}/api/group/leave", 'GroupController::leave');
        $routes->post("{$base}/api/group/set_admin", 'GroupController::set_admin');
        $routes->post("{$base}/api/group/rename", 'GroupController::rename');
        $routes->post("{$base}/api/group/photo", 'GroupController::photo');

        // Media (files/grid_chat/media) — members only
        $routes->post("{$base}/api/media/upload", 'MediaController::upload');
        $routes->get("{$base}/api/media/(:num)", 'MediaController::show/$1');

        // Moments / Status (files/grid_chat/moments) — audience checked per status
        $routes->get("{$base}/api/moments", 'MomentController::index');
        $routes->get("{$base}/api/moments/privacy", 'MomentController::privacy');
        $routes->get("{$base}/api/moments/media/(:num)", 'MomentController::media/$1');
        $routes->get("{$base}/api/moments/viewers", 'MomentController::viewers');
        $routes->post("{$base}/api/moments/like", 'MomentController::like');
        $routes->post("{$base}/api/moments/text", 'MomentController::createText');
        $routes->post("{$base}/api/moments/media", 'MomentController::createMedia');
        $routes->post("{$base}/api/moments/view", 'MomentController::view');
        $routes->post("{$base}/api/moments/delete", 'MomentController::deleteMoment');
        $routes->post("{$base}/api/moments/privacy", 'MomentController::savePrivacy');
    }

    // API endpoints
    $routes->match(['get', 'post'], 'grid-chat/api/(:segment)', 'GridChatController::api/$1');
    $routes->match(['get', 'post'], 'grid_chat/api/(:segment)', 'GridChatController::api/$1');
});

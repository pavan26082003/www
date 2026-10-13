<?php

/**
 * --------------------------------------------------------------------
 * GRID Professional Community - Modular Route Definitions
 * --------------------------------------------------------------------
 * This file contains all web view routing and REST API endpoints
 * for the GRID module.
 * 
 * To activate this module in any CodeIgniter 4 installation, simply
 * include this file in Config/Routes.php:
 * 
 *   $gridRoutesFile = APPPATH . 'Config/GRID_Config/Routes.php';
 *   if (is_file($gridRoutesFile)) {
 *       require $gridRoutesFile;
 *   }
 */

// Web View Routes
$routes->get('grid', 'GRID_Controllers\GridController::index');
$routes->get('grid/profile', 'GRID_Controllers\GridController::profile');
$routes->get('grid/messaging', 'GRID_Controllers\GridController::messaging');
$routes->get('grid/team', 'GRID_Controllers\GridController::team');
$routes->get('grid/demo_loader', 'GRID_Controllers\GridController::demo_loader');
$routes->get('grid/network', 'GRID_Controllers\GridController::network');
$routes->get('grid/jobs', 'GRID_Controllers\GridController::jobs');

// Optional top-level shortcuts
$routes->get('messaging', 'GRID_Controllers\GridController::messaging');
$routes->get('profile', 'GRID_Controllers\GridController::profile');

// Grid REST API Endpoints (High Performance, O(1) Lookups)
$routes->group('grid/api', ['namespace' => 'App\Controllers\GRID_Controllers'], static function ($routes) {
    // Skills Autocomplete
    $routes->get('skills/suggest', 'GridApiController::suggestSkills');

    // Header Search Bar (People Search)
    $routes->get('search/users', 'GridApiController::searchUsers');
    
    // Profile & Identity
    $routes->get('profile', 'GridApiController::getProfile');
    $routes->get('profile/(:any)', 'GridApiController::getProfile/$1');
    $routes->post('profile/save', 'GridApiController::saveProfile');
    $routes->post('profile/upload-avatar', 'GridApiController::uploadAvatar');
    $routes->post('profile/upload-banner', 'GridApiController::uploadBanner');
    $routes->post('profile/wizard-setup', 'GridApiController::wizardSetup');
    $routes->post('profile/provision', 'GridApiController::provisionGridId');
    $routes->post('profile/section/save', 'GridApiController::saveSectionItem');
    $routes->post('profile/section/delete', 'GridApiController::deleteSectionItem');

    // Network Graph & Connections
    $routes->get('connections', 'GridApiController::getConnections');
    $routes->post('connections/request', 'GridApiController::requestConnection');
    $routes->post('connections/respond', 'GridApiController::respondConnection');
    
    // Jobs Hub & Application Tracker
    $routes->get('jobs', 'GridApiController::getJobs');
    $routes->post('jobs/apply', 'GridApiController::applyJob');
    $routes->get('jobs/tracker', 'GridApiController::getApplicationTracker');
    $routes->post('jobs/create', 'GridApiController::createJob');

    // Recruiter Mode & Job Posting Pipeline (ATS)
    $routes->get('recruiter/jobs', 'GridApiController::getRecruiterJobs');
    $routes->get('recruiter/jobs/(:num)/applicants', 'GridApiController::getJobApplicants/$1');
    $routes->post('recruiter/applications/status', 'GridApiController::updateApplicationStatus');

    // Circle Connect & Direct Messaging (Universal Handle / grid_id)
    $routes->get('messages/conversations', 'GridApiController::getConversations');
    $routes->get('messages/thread/(:num)', 'GridApiController::getThread/$1');
    $routes->post('messages/send', 'GridApiController::sendMessage');
    $routes->get('messages/start/(:any)', 'GridApiController::startConversation/$1');

    // Home Feed Posts & Interactions
    $routes->post('posts/create', 'GridApiController::createPost');
    $routes->get('posts', 'GridApiController::getPosts');
    $routes->get('posts/user', 'GridApiController::getUserPosts');
    $routes->get('posts/user/(:num)', 'GridApiController::getUserPosts/$1');
    $routes->post('posts/delete', 'GridApiController::deletePost');
    $routes->delete('posts/(:segment)', 'GridApiController::deletePost/$1');
    $routes->post('posts/like', 'GridApiController::toggleLike');
    $routes->post('posts/(:num)/like', 'GridApiController::toggleLike/$1');
    $routes->post('posts/repost', 'GridApiController::toggleRepost');
    $routes->post('posts/(:num)/repost', 'GridApiController::toggleRepost/$1');
    $routes->get('posts/(:num)/comments', 'GridApiController::getComments/$1');
    $routes->post('posts/comment', 'GridApiController::addComment');
    $routes->post('posts/(:num)/comments', 'GridApiController::addComment/$1');
    $routes->post('posts/comment/delete', 'GridApiController::deleteComment');
    $routes->delete('posts/comment/(:num)', 'GridApiController::deleteComment/$1');

    // Universal File & Media Upload
    $routes->post('upload', 'GridApiController::upload');

    // Company-Sponsored Community Challenges & Events
    $routes->get('challenges/top', 'GridApiController::getTopChallenges');
    $routes->post('challenges/create', 'GridApiController::createChallenge');
});
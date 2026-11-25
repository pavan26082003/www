<?php

namespace Config;

/**
 * --------------------------------------------------------------------
 * MAIN SERVER ROUTES (Routesmain.php)
 * --------------------------------------------------------------------
 * Deploy / sync this file to the live server's app/Config/Routes.php.
 *
 * Rules:
 * 1. Keep MAIN-SERVER-ONLY blocks (existing projects on production).
 * 2. Keep LOCAL-SYNCED blocks in sync with local app/Config/Routes.php.
 * 3. Do not remove existing main-server routes when updating from local.
 * 4. Prefer $routes->group() so new endpoints are easy to add.
 * --------------------------------------------------------------------
 */

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
|--------------------------------------------------------------------------
| Public legal pages (no login). Used by the Artix Fox Meta app listing.
|--------------------------------------------------------------------------
*/
$routes->get('privacy-policy', 'PublicLegal::privacy');
$routes->get('terms-and-conditions', 'PublicLegal::terms');
$routes->get('data-deletion', 'PublicLegal::deletion');

// Circle Connect share links must be the first application routes.
// CodeIgniter 4.1 keeps the first matching/duplicate route, so registering
// these before every module prevents Professional Space from claiming them.
$routes->get(
    'grid-chat/connect',
    '\\App\\Controllers\\GRID_CHAT_CONTROLLERS\\GridChatController::connect'
);
$routes->get(
    'grid_chat/connect',
    '\\App\\Controllers\\GRID_CHAT_CONTROLLERS\\GridChatController::connect'
);



// App Home Page module
if (file_exists(APPPATH . 'Config/AppHomePageRoutes.php')) {
    require APPPATH . 'Config/AppHomePageRoutes.php';
}

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

/* ==================================================================
 | MAIN-SERVER-ONLY — do not remove when syncing from local
 | ================================================================== */

/*
|--------------------------------------------------------------------------
| Auth / Sign-in  [MAIN]
|--------------------------------------------------------------------------
*/
$routes->get('qr_login', 'Signin::qr_login'); // 18-06-26

/*
|--------------------------------------------------------------------------
| Device UI Schema API (/api/*)  [MAIN]
|--------------------------------------------------------------------------
| Catch-all notFound must stay last inside this group.
*/
/*$routes->group('api', static function ($routes) {
	$routes->options('(:any)', 'ApiErrors::preflight');
	$routes->get('device-ui-schema/(:any)/parameters', 'DeviceUiSchemaApi::parameters/$1');
	$routes->get('device-ui-schema/(:any)', 'DeviceUiSchemaApi::show/$1');
	$routes->get('device-ui-info/(:any)', 'DeviceUiSchemaApi::info/$1');
	$routes->match(['post', 'put', 'patch', 'delete'], 'device-ui-schema/(:any)', 'ApiErrors::methodNotAllowed');
	$routes->match(['post', 'put', 'patch', 'delete'], 'device-ui-info/(:any)', 'ApiErrors::methodNotAllowed');
	$routes->add('(:any)', 'ApiErrors::notFound');
});*/



// SCAN ROUTES - Mobile App Scanning API
// POST - Send scan data from mobile app
/*$routes->post('device-ui-scan/(:segment)/(:segment)/scan', 'DeviceUiScanApi::scan/$1/$2');

// GET - Check if user has scanned
$routes->get('device-ui-scan/(:segment)/(:segment)/status', 'DeviceUiScanApi::checkStatus/$1/$2');

// GET - Also allow GET for testing purposes (but will return "method not allowed" for scanning)
$routes->get('device-ui-scan/(:segment)/(:segment)/scan', 'DeviceUiScanApi::scan/$1/$2');*/

/*



/*
|--------------------------------------------------------------------------
| IOD / Legacy Attendance  [MAIN]
|--------------------------------------------------------------------------
*/
$routes->get('IOD', 'Iod::index');
$routes->get('attendance/admin', 'Attendance_admin_controller::admin');

/*
|--------------------------------------------------------------------------
| AttendTrack Attendance  [MAIN]
|--------------------------------------------------------------------------
| Optional WorkHive attend aliases at the bottom may share some paths.
*/
$routes->group('attend', static function ($routes) {
	$routes->get('/', 'AttendAttendanceController::index');
	$routes->get('attendance', 'AttendAttendanceController::index');
	$routes->match(['get', 'post'], 'api/(:segment)', 'AttendAttendanceController::api/$1');
	$routes->get('logout', 'AttendAttendanceController::logout');
});

/* ==================================================================
 | LOCAL-SYNCED — keep aligned with app/Config/Routes.php
 | ================================================================== */

/*
|--------------------------------------------------------------------------
| Orders  [SYNC]
|--------------------------------------------------------------------------
*/
$routes->group('orders', static function ($routes) {
	$routes->post('save', 'OrderController::save');
	$routes->post('status/(:num)', 'OrderController::updateStatus/$1');
	$routes->get('/', 'OrderController::getOrders');
	$routes->get('stream', 'OrderController::streamOrders');
	$routes->get('stream-status/(:num)', 'OrderController::streamOrderStatus/$1');
});

/*
|--------------------------------------------------------------------------
| Dashboard  [SYNC]
|--------------------------------------------------------------------------
*/
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard/notifications', 'Dashboard::notifications');
$routes->get('dashboard/applications', 'Dashboard::applications');


/****24-10-26 */

/*
$routes->get('items/view_page_ui/(:num)', 'View_page_controller::view_page_ui/$1');
$routes->post('items/save_view_page_ui_theme', 'View_page_controller::save_view_page_ui_theme');
$routes->get('items/api_page_linking/(:num)', 'View_page_controller::api_page_linking/$1');
$routes->post('items/submit_page_params', 'View_page_controller::submit_page_params');
$routes->post('items/save_page_linking_row', 'View_page_controller::save_page_linking_row');


*/

/**26-10-26
 * 
 */
if (is_file(APPPATH . 'Config/Sales_Routes.php')) {
    require APPPATH . 'Config/Sales_Routes.php';
}
if (is_file(APPPATH . 'Config/Crm_Routes.php')) {
    require APPPATH . 'Config/Crm_Routes.php';
}
if (is_file(APPPATH . 'Config/Home_Routes.php')) {
    require APPPATH . 'Config/Home_Routes.php';
}


// Add these TWO lines to app/Config/Routes.php (keep your existing routes):
$routes->get('efactorproduct/uid', 'EfactorProductUidController::index');
$routes->post('efactorproduct/uid/save', 'EfactorProductUidController::save');



$routes->match(
    ['get', 'post'],
    'efactorproduct/uid',
    'EfactorProductUidController::index',
    ['filter' => 'csrf']
);


/*
|--------------------------------------------------------------------------
| Items (non-sticker only — sticker/taglink live in sticker_stu_routes.php)
|--------------------------------------------------------------------------
*/
$routes->post('items/update_linking_data', 'Items::update_linking_data');
$routes->post('items/delete_param', 'Items::delete_param');
$routes->post('items/add_param', 'Items::add_param');
$routes->post('items/update_page_data', 'Items::update_page_data');

$routes->group('items', static function ($routes) {
	$routes->get('getDeviceTypes', 'Items::getDeviceTypes');
	$routes->get('product_description/(:any)', 'Items::product_description/$1');
	$routes->get('product_description_form/(:num)', 'Items::product_description_form/$1');
	$routes->post('save_product_description', 'Items::save_product_description');
	$routes->get('printer_list', 'Items::listPrinters');
	$routes->post('printer_info', 'Items::getPrinterInfo');
	$routes->get('printer_page', 'ItemsController::printerPage');
});
$routes->get('generated-form', 'Items::generatedForm');


/*******22-10-26
 * 
 */
if(is_file(APPPATH . 'Config/CustomerSupportRoutes.php')){
    require APPPATH . 'Config/CustomerSupportRoutes.php';
}

/**27-10-26 */



$routes->match(
    ['get', 'post', 'head'],
    'grid/connect',
    'GRID_Controllers\GridController::connect'
);


/**19-11-26****** */


// // GPS Tracking Phase 0 — /index.php/api/track/* (Bearer auth, no CSRF).
// $routes->group('api/track', static function ($routes) {
// 	$routes->put('policies/(:num)', 'TrackApiController::updatePolicy/$1');
// 	$routes->get('effective-policy', 'TrackApiController::effectivePolicy');
// 	$routes->post('breadcrumbs/batch', 'TrackApiController::breadcrumbsBatch');
// 	$routes->post('events', 'TrackApiController::events');
// 	$routes->get('live', 'TrackApiController::live');
// 	$routes->get('history', 'TrackApiController::history');
// });

// // GPS Tracking Phase 0 — /index.php/api/track/* (Bearer auth, no CSRF).
// $routes->group('api/track', static function ($routes) {
// 	$routes->get('me', 'TrackApiController::me');
// 	$routes->put('policies/(:num)', 'TrackApiController::updatePolicy/$1');
// 	$routes->get('effective-policy', 'TrackApiController::effectivePolicy');
// 	$routes->post('breadcrumbs/batch', 'TrackApiController::breadcrumbsBatch');
// 	$routes->post('events', 'TrackApiController::events');
// 	$routes->get('live', 'TrackApiController::live');
// 	$routes->get('history', 'TrackApiController::history');
// });

// GPS Tracking Phase 0 — /index.php/api/track/* (Bearer auth, no CSRF).
// $routes->group('api/track', static function ($routes) {
// 	$routes->get('me', 'TrackApiController::me');
// 	$routes->post('command', 'TrackApiController::command');
// 	$routes->put('policies/(:num)', 'TrackApiController::updatePolicy/$1');
// 	$routes->get('effective-policy', 'TrackApiController::effectivePolicy');
// 	$routes->post('breadcrumbs/batch', 'TrackApiController::breadcrumbsBatch');
// 	$routes->post('events', 'TrackApiController::events');
// 	$routes->get('live', 'TrackApiController::live');
// 	$routes->get('history', 'TrackApiController::history');
// });
// GPS Tracking Phase 0 — /index.php/api/track/* (Bearer auth, no CSRF).
$routes->group('api/track', static function ($routes) {
	$routes->get('me', 'TrackApiController::me');
	$routes->post('command', 'TrackApiController::command');
	$routes->put('policies/(:num)', 'TrackApiController::updatePolicy/$1');
	$routes->get('effective-policy', 'TrackApiController::effectivePolicy');
	$routes->post('breadcrumbs/batch', 'TrackApiController::breadcrumbsBatch');
	$routes->post('events', 'TrackApiController::events');
	$routes->get('live', 'TrackApiController::live');
	$routes->get('history', 'TrackApiController::history');
});

$routes->match(['get', 'post', 'head'], 'grid-chat/connect', 'GRID_Controllers\GridController::connect');
$routes->match(['get', 'post', 'head'], 'grid_chat/connect', 'GRID_Controllers\GridController::connect');
/**************************************************************/
$routes->post('items/delete_saved_page', 'Items::delete_saved_page');
$routes->post('items/reorder_saved_pages', 'Items::reorder_saved_pages');


// Mobile push contract. Session cookie or Bearer token, no CSRF.

$routes->post('grid-chat/api/push/native-token', 'DevicePushController::registerDeviceToken');
$routes->post('grid_chat/api/push/native-token', 'DevicePushController::registerDeviceToken');
$routes->post('api/call_action', 'DevicePushController::callAction');
$routes->post('api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid-chat/api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid_chat/api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid-chat/api/call/notify', 'DevicePushController::notifyCall');
$routes->post('grid_chat/api/call/notify', 'DevicePushController::notifyCall');



/***************************************************************/
/*******22-10-26******** */
// Mobile push contract. Session cookie or Bearer token, no CSRF.
$routes->post('api/register_device_token', 'DevicePushController::registerDeviceToken');
$routes->post('grid-chat/api/push/native-token', 'DevicePushController::registerDeviceToken');
$routes->post('grid_chat/api/push/native-token', 'DevicePushController::registerDeviceToken');
$routes->post('api/call_action', 'DevicePushController::callAction');
$routes->post('api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid-chat/api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid_chat/api/call/decline', 'DevicePushController::declineCall');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('dashboard/notifications', 'Dashboard::notifications');
$routes->get('dashboard/applications', 'Dashboard::applications');
$routes->get('dashboard/client_default_dashboard', 'Dashboard::client_default_dashboard');
$routes->get('dashboard/restore_to_default_client_dashboard', 'Dashboard::restore_to_default_client_dashboard');
$routes->get('dashboard/edit_client_default_dashboard', 'Dashboard::edit_client_default_dashboard');
$routes->post('dashboard/save_client_default_dashboard', 'Dashboard::save_client_default_dashboard');









/*
|--------------------------------------------------------------------------
| Forms / Device  [SYNC]
|--------------------------------------------------------------------------
*/
$routes->get('/forms/device/select_mode/167', 'Forms::selectModel');
//$routes->get('/device/select_model/(:num)', 'DeviceController::selectModel/$1');
$routes->get('/device/page/(:num)/(:num)', 'DeviceController::display/$1/$2');
$routes->get('/view-ui-type', 'Forms::index');

// Custom routing for custom pages
$routes->add('about/(:any)', 'about/$1');

/*
|--------------------------------------------------------------------------
| Restaurant System  [SYNC]
|--------------------------------------------------------------------------
*/
$routes->group('restaurant-system', static function ($routes) {
	$routes->get('/', 'RestMainController::index');
	$routes->get('main', 'RestMainController::index');

	$routes->get('brand', 'RestBrandController::index');
	$routes->match(['get', 'post'], 'brand/api', 'RestBrandController::api');

	$routes->get('customer', 'RestCustomerController::index');
	$routes->match(['get', 'post'], 'customer/api', 'RestCustomerController::api');

	$routes->get('waiter', 'RestWaiterController::index');
	$routes->match(['get', 'post'], 'waiter/api', 'RestWaiterController::api');

	$routes->get('kitchen', 'RestKitchenController::index');
	$routes->match(['get', 'post'], 'kitchen/api', 'RestKitchenController::api');

	$routes->get('manager', 'RestManagerController::index');
	$routes->match(['get', 'post'], 'manager/api', 'RestManagerController::api');
});

/*
|--------------------------------------------------------------------------
| Production Management (UI + AJAX)
|--------------------------------------------------------------------------
| IOP / Production data is served ONLY from the physical API/Production folder.
| Do not add production-management/api or API/Production CodeIgniter routes here.
*/
/*$routes->group('production-management', static function ($routes) {
	$routes->get('/', 'ProductionController::production');
	$routes->get('main', 'ProductionController::main');
	$routes->get('production', 'ProductionController::production');
	$routes->post('save', 'ProductionController::save');

	// Backward-compatible AJAX routes used by the current APITRY/index.php view
	$routes->group('ajax', static function ($routes) {
		$routes->get('product-types', 'ProductionController::ajax_product_types');
		$routes->get('categories', 'ProductionController::ajax_categories');
		$routes->get('products', 'ProductionController::ajax_products');
		$routes->get('models', 'ProductionController::ajax_models');
		$routes->get('versions', 'ProductionController::ajax_versions');
		$routes->get('field-defs', 'ProductionController::ajax_field_defs');
		$routes->get('lines', 'ProductionController::ajax_lines');
		$routes->get('units', 'ProductionController::ajax_units');
		$routes->get('quality-statuses', 'ProductionController::ajax_quality_statuses');
	});
});*/

/***23-10-26
 * 
 */
// Mobile push contract. Session cookie or Bearer token, no CSRF.
$routes->post('api/register_device_token', 'DevicePushController::registerDeviceToken');
$routes->post('grid-chat/api/push/native-token', 'DevicePushController::registerDeviceToken');
$routes->post('grid_chat/api/push/native-token', 'DevicePushController::registerDeviceToken');
$routes->post('api/call_action', 'DevicePushController::callAction');
$routes->post('api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid-chat/api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid_chat/api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid-chat/api/call/notify', 'DevicePushController::notifyCall');
$routes->post('grid_chat/api/call/notify', 'DevicePushController::notifyCall');


/**27_10-26
 * 
 */


$routes->get('grid-dashboard', 'GridDashboard::index');
$routes->get('grid-dashboard/scanner', 'GridDashboard::scanner');
$routes->match(['get', 'post'], 'grid-dashboard/api/(:segment)', 'GridDashboard::api/$1');
/*
|--------------------------------------------------------------------------
| Production Management (UI + AJAX)
|--------------------------------------------------------------------------
| IOP / Production data is served ONLY from the physical API/Production folder.
| Do not add production-management/api or API/Production CodeIgniter routes here.
*/
$routes->group('production-management', static function ($routes) {
	$routes->get('/', 'ProductionController::production');
	$routes->get('main', 'ProductionController::main');
	$routes->get('production', 'ProductionController::production');
	$routes->post('save', 'ProductionController::save');

	// Backward-compatible AJAX routes used by the current APITRY/index.php view
	$routes->group('ajax', static function ($routes) {
		$routes->get('product-types', 'ProductionController::ajax_product_types');
		$routes->get('categories', 'ProductionController::ajax_categories');
		$routes->get('products', 'ProductionController::ajax_products');
		$routes->get('models', 'ProductionController::ajax_models');
		$routes->get('versions', 'ProductionController::ajax_versions');
		$routes->get('field-defs', 'ProductionController::ajax_field_defs');
		$routes->get('lines', 'ProductionController::ajax_lines');
		$routes->get('units', 'ProductionController::ajax_units');
		$routes->get('quality-statuses', 'ProductionController::ajax_quality_statuses');
	});
});

/*
|--------------------------------------------------------------------------
| Public Production QR / Generated page (NO LOGIN)
|--------------------------------------------------------------------------
| Served by physical API files (no Security_Controller / no signin):
|   GET  /API/Production/public-page.php?node_id=...
|   GET  /API/Production/public-page.php?batch_id=...&format=json
|   POST /API/Production/public-claim.php
*/


/*
|--------------------------------------------------------------------------
| Production API (api/v1)
|--------------------------------------------------------------------------
| Later you can move only these API files to your main server and point
| PRODUCTION_API_BASE_URL to that server URL.
*/
$routes->group('api/v1', ['namespace' => 'App\\Controllers\\Api\\V1'], static function ($routes) {
	$routes->group('production', static function ($routes) {
		$routes->get('health', 'ProductionApiController::health');
		$routes->get('page-data', 'ProductionApiController::pageData');
		$routes->post('actions', 'ProductionApiController::saveAction');

		$routes->get('products', 'ProductionApiController::products');
		$routes->get('models', 'ProductionApiController::models');
		$routes->get('versions', 'ProductionApiController::versions');

		$routes->get('product-types', 'ProductionApiController::productTypes');
		$routes->post('product-types', 'ProductionApiController::createProductType');

		$routes->get('categories', 'ProductionApiController::categories');
		$routes->post('categories', 'ProductionApiController::createCategory');

		$routes->get('units', 'ProductionApiController::units');
		$routes->get('quality-statuses', 'ProductionApiController::qualityStatuses');
		$routes->get('lines', 'ProductionApiController::lines');
		$routes->get('field-defs', 'ProductionApiController::fieldDefs');
	});
});

/*
|--------------------------------------------------------------------------
| Production Updates
|--------------------------------------------------------------------------
*/
$routes->group('production-updates', static function ($routes) {
	$routes->get('/', 'ProductionUpdateController::index');
	$routes->get('ping', 'ProductionUpdateController::ping');
	$routes->get('api/(:segment)', 'ProductionUpdateController::api/$1');
	$routes->post('api/(:segment)', 'ProductionUpdateController::api/$1');
});


/******** 15-10-26******* */
$routes->match(['get', 'post'], 'sd-signup', 'SdAuth::signup');
$routes->post('sd-signup/otp', 'SdAuth::sendOtp');
$routes->post('sd-signup/verify', 'SdAuth::verifyOtp');
$routes->post('sd-signup/complete', 'SdAuth::completeSignup');
$routes->get('sd-social/(:segment)', 'SdAuth::social/$1');



// -----------------------------------------------------------------------------
// SD Login / Signup / Welcome
// -----------------------------------------------------------------------------
// SD Login / Signup / Welcome
// -----------------------------------------------------------------------------
/*$routes->get('welcome', 'SdAuth::index');
$routes->get('sd-auth', 'SdAuth::index');
$routes->match(['get', 'post'], 'sd-signup', 'SdAuth::signup');
$routes->match(['get', 'post'], 'sd-login', 'SdAuth::login');
$routes->post('sd-logout', 'SdAuth::logout');
$routes->get('sd-dashboard', 'SdAuth::dashboard');*/


$routes->get('welcome', 'SdAuth::index');
$routes->get('sd-auth', 'SdAuth::index');
$routes->match(['get', 'post'], 'sd-signup', 'SdAuth::signup');
$routes->post('sd-signup/otp', 'SdAuth::sendOtp');
$routes->post('sd-signup/verify', 'SdAuth::verifyOtp');
$routes->post('sd-signup/complete', 'SdAuth::completeSignup');
$routes->post('sd-signup/register', 'SdAuth::registerRemote');
$routes->post('sd-signup/verify-remote', 'SdAuth::verifyRemote');
$routes->post('sd-signup/login-remote', 'SdAuth::loginRemote');
$routes->post('sd-signup/session', 'SdAuth::webSession');
$routes->get('sd-social/(:segment)', 'SdAuth::social/$1');
$routes->match(['get', 'post'], 'sd-login', 'SdAuth::login');
$routes->match(['get', 'post'], 'sd-forgot-password', 'SdAuth::forgotPassword');
$routes->post('sd-logout', 'SdAuth::logout');
$routes->get('sd-dashboard', 'SdAuth::dashboard');



// Mobile push contract. Session cookie or Bearer token, no CSRF.
$routes->post('api/register_device_token', 'DevicePushController::registerDeviceToken');
$routes->post('api/call_action', 'DevicePushController::callAction');
$routes->post('api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid-chat/api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid_chat/api/call/decline', 'DevicePushController::declineCall');








/*

$routes->get('items/modal_pages/(:num)', 'Modal_pages_controller::modal_pages/$1');
$routes->get('items/modal_pages_data/(:num)', 'Modal_pages_controller::modal_pages_data/$1');
$routes->post('items/save_pages', 'Modal_pages_controller::save_pages');
$routes->post('items/save_page_seq', 'Modal_pages_controller::save_page_seq');
$routes->post('items/delete_saved_page', 'Modal_pages_controller::delete_saved_page');
$routes->post('items/reorder_saved_pages', 'Modal_pages_controller::reorder_saved_pages');

*/


// modal pages
// -----------------------------------------------------------------------------
$routes->match(['get', 'post'], 'items/modal_pages/(:num)', 'Modal_pages_controller::modal_pages/$1');
$routes->match(['get', 'post'], 'items/modal_pages_data/(:num)', 'Modal_pages_controller::modal_pages_data/$1');
$routes->match(['get', 'post'], 'items/save_pages', 'Modal_pages_controller::save_pages');
$routes->match(['get', 'post'], 'items/save_page_seq', 'Modal_pages_controller::save_page_seq');
$routes->match(['get', 'post'], 'items/delete_saved_page', 'Modal_pages_controller::delete_saved_page');
$routes->match(['get', 'post'], 'items/reorder_saved_pages', 'Modal_pages_controller::reorder_saved_pages');
$routes->add('items/delete_saved_page', 'Modal_pages_controller::delete_saved_page');
$routes->add('items/reorder_saved_pages', 'Modal_pages_controller::reorder_saved_pages');
$routes->add('items/save_pages', 'Modal_pages_controller::save_pages');
$routes->match(['get', 'post'], 'modal_pages_controller/delete_saved_page', 'Modal_pages_controller::delete_saved_page');
$routes->match(['get', 'post'], 'modal_pages_controller/reorder_saved_pages', 'Modal_pages_controller::reorder_saved_pages');
$routes->match(['get', 'post'], 'modal_pages_controller/save_pages', 'Modal_pages_controller::save_pages');




/****24-10-26
 * 
 */

$routes->get('items/api_page_linking/(:num)', 'Modal_api_page_linking_controller::api_page_linking/$1');
$routes->post('items/submit_page_params', 'Modal_api_page_linking_controller::submit_page_params');
$routes->post('items/save_page_linking_row', 'Modal_api_page_linking_controller::save_page_linking_row');

/*******user deatiles  ******/
$routes->post(
    'clients/users_list_data',
    'Clients::users_list_data'
);

$routes->get('items/view_page_ui/(:num)', 'View_page_controller::view_page_ui/$1');
$routes->post('items/save_view_page_ui_theme', 'View_page_controller::save_view_page_ui_theme');
$routes->post('items/remove_device_node', 'View_page_controller::remove_device_node');


$routes->get('items/modal_pages/(:num)', 'Modal_pages_controller::modal_pages/$1');
$routes->get('items/modal_pages_data/(:num)', 'Modal_pages_controller::modal_pages_data/$1');
$routes->post('items/save_pages', 'Modal_pages_controller::save_pages');
$routes->post('items/delete_saved_page', 'Modal_pages_controller::delete_saved_page');
$routes->post('items/reorder_saved_pages', 'Modal_pages_controller::reorder_saved_pages');
/*************add modal_14-10-26*/


$routes->get('items/vendor', 'vendor_controller::vendor');

$routes->match(['get', 'post'], 'items/modal_vendorproduct', 'vendor_controller::modal_vendorproduct');
$routes->match(['get', 'post'], 'items/list_data1vendor', 'vendor_controller::list_data1vendor');
$routes->post('items/savevendorproduct', 'vendor_controller::savevendorproduct');
$routes->post('items/deletevendorproduct', 'vendor_controller::deletevendorproduct');

/*************************************************************************/
/****19-11-26brand and product
 * 
 */

// $routes->get('items/brand', 'Brand_controller::brand');
// $routes->match(['get', 'post'], 'items/list_data30', 'Brand_controller::list_data30');
// $routes->match(['get', 'post'], 'items/modal_formbrand', 'Brand_controller::modal_formbrand');
// $routes->match(['get', 'post'], 'items/modal_formbrand/(:num)', 'Brand_controller::modal_formbrand');
// $routes->match(['get', 'post'], 'items/modal_formbrand_edit/(:num)', 'Brand_controller::modal_formbrand_edit/$1');
// $routes->post('items/savebrand', 'Brand_controller::savebrand');
// $routes->post('items/savebrand_edit', 'Brand_controller::savebrand_edit');
// $routes->post('items/deletebrand', 'Brand_controller::deletebrand');

// $routes->get('items/product', 'Product_controller::product');
// $routes->match(['get', 'post'], 'items/list_data20', 'Product_controller::list_data20');
// $routes->match(['get', 'post'], 'items/modal_formcat', 'Product_controller::modal_formcat');
// $routes->match(['get', 'post'], 'items/modal_formcat_edit/(:num)', 'Product_controller::modal_formcat_edit/$1');
// $routes->post('items/savecat', 'Product_controller::savecat');
// $routes->post('items/updatecat', 'Product_controller::updatecat');
// $routes->post('items/deletecat', 'Product_controller::deletecat');


// -----------------------------------------------------------------------------
// SD Login / Signup / Welcome
// -----------------------------------------------------------------------------
// SD Login / Signup / Welcome
// -----------------------------------------------------------------------------
/*$routes->get('welcome', 'SdAuth::index');
$routes->get('sd-auth', 'SdAuth::index');
$routes->match(['get', 'post'], 'sd-signup', 'SdAuth::signup');
$routes->match(['get', 'post'], 'sd-login', 'SdAuth::login');
$routes->post('sd-logout', 'SdAuth::logout');
$routes->get('sd-dashboard', 'SdAuth::dashboard');*/


$routes->get('welcome', 'SdAuth::index');
$routes->get('sd-auth', 'SdAuth::index');
$routes->match(['get', 'post'], 'sd-signup', 'SdAuth::signup');
$routes->match(['get', 'post'], 'sd-login', 'SdAuth::login');
$routes->match(['get', 'post'], 'sd-forgot-password', 'SdAuth::forgotPassword');
$routes->post('sd-logout', 'SdAuth::logout');
$routes->get('sd-dashboard', 'SdAuth::dashboard');


/**********17-11-26 table converter  */
$routes->get('items/model-table-converter', 'Model_table_converter::index');
$routes->get('items/model-table-converter/lookup', 'Model_table_converter::lookup');
$routes->get('items/model-table-converter/preview', 'Model_table_converter::preview');
$routes->post('items/model-table-converter/convert', 'Model_table_converter::convert');



/*
|--------------------------------------------------------------------------
| Production API (api/v1)
|--------------------------------------------------------------------------
| Later you can move only these API files to your main server and point
| PRODUCTION_API_BASE_URL to that server URL.
*/
$routes->group('api/v1', ['namespace' => 'App\\Controllers\\Api\\V1'], static function ($routes) {
	$routes->group('production', static function ($routes) {
		$routes->get('health', 'ProductionApiController::health');
		$routes->get('page-data', 'ProductionApiController::pageData');
		$routes->post('actions', 'ProductionApiController::saveAction');

		$routes->get('products', 'ProductionApiController::products');
		$routes->get('models', 'ProductionApiController::models');
		$routes->get('versions', 'ProductionApiController::versions');

		$routes->get('product-types', 'ProductionApiController::productTypes');
		$routes->post('product-types', 'ProductionApiController::createProductType');

		$routes->get('categories', 'ProductionApiController::categories');
		$routes->post('categories', 'ProductionApiController::createCategory');

		$routes->get('units', 'ProductionApiController::units');
		$routes->get('quality-statuses', 'ProductionApiController::qualityStatuses');
		$routes->get('lines', 'ProductionApiController::lines');
		$routes->get('field-defs', 'ProductionApiController::fieldDefs');
	});
});

/*
|--------------------------------------------------------------------------
| Production Updates
|--------------------------------------------------------------------------
*/
$routes->group('production-updates', static function ($routes) {
	$routes->get('/', 'ProductionUpdateController::index');
	$routes->get('ping', 'ProductionUpdateController::ping');
	$routes->get('api/(:segment)', 'ProductionUpdateController::api/$1');
	$routes->post('api/(:segment)', 'ProductionUpdateController::api/$1');
});
/*
|--------------------------------------------------------------------------
| WorkHive  [SYNC + MAIN extras]
|--------------------------------------------------------------------------
| Local role/API routes + main-server index/ping/catch-all API.
*/

/*
$routes->group('work-hive', static function ($routes) {
	// Pages
	$routes->get('/', 'WorkHiveController::index'); // [MAIN]
	$routes->get('access', 'WorkHiveController::access');
	$routes->get('admin', 'WorkHiveController::admin');
	$routes->get('manager', 'WorkHiveController::manager');
	$routes->get('lead', 'WorkHiveController::lead');
	$routes->get('hr', 'WorkHiveController::hr'); // [SYNC]
	$routes->get('employee', 'WorkHiveController::employee');
	$routes->get('ping', 'WorkHiveController::ping'); // [MAIN]
	$routes->get('logout', 'WorkHiveController::logout');

	// Sidebar / page navigation
	$routes->get('page', 'WorkHiveController::page');
	$routes->get('page/(:segment)', 'WorkHiveController::page/$1');

	// Local-only role/page audit (127.0.0.1) — safe on main if controller exists
	$routes->get('audit/impersonate/(:num)', 'WorkHiveAuditController::impersonate/$1');
	$routes->get('audit/report', 'WorkHiveAuditController::report');

	// APIs — specific routes first, catch-all last
	$routes->group('api', static function ($routes) {
		$routes->post('punch', 'WorkHiveController::punch');
		$routes->post('punch/in', 'WorkHiveController::punchIn');
		$routes->post('punch/out', 'WorkHiveController::punchOut');

		$routes->get('detail/(:segment)/(:num)', 'WorkHiveController::detail/$1/$2');
		$routes->get('main-user/(:num)', 'WorkHiveController::mainUser/$1'); // [SYNC]

		$routes->post('task/comment/(:num)', 'WorkHiveController::taskComment/$1'); // [SYNC]
		$routes->post('task/(:segment)', 'WorkHiveController::task/$1');
		$routes->post('task/(:segment)/(:num)', 'WorkHiveController::task/$1/$2');

		$routes->post('project/(:segment)', 'WorkHiveController::project/$1');
		$routes->post('project/(:segment)/(:num)', 'WorkHiveController::project/$1/$2');

		$routes->post('work-log/(:segment)', 'WorkHiveController::workLog/$1');
		$routes->post('work-log/(:segment)/(:num)', 'WorkHiveController::workLog/$1/$2');

		$routes->post('attendance-request/(:segment)', 'WorkHiveController::attendanceRequest/$1');
		$routes->post('attendance-request/(:segment)/(:num)', 'WorkHiveController::attendanceRequest/$1/$2');

		$routes->post('attendance/(:segment)', 'WorkHiveController::attendanceRecord/$1'); // [SYNC]
		$routes->post('attendance/(:segment)/(:num)', 'WorkHiveController::attendanceRecord/$1/$2'); // [SYNC]

		$routes->post('employee/(:segment)', 'WorkHiveController::employeeRecord/$1');
		$routes->post('employee/(:segment)/(:num)', 'WorkHiveController::employeeRecord/$1/$2');

		$routes->post('team/(:segment)', 'WorkHiveController::team/$1');
		$routes->post('team/(:segment)/(:num)', 'WorkHiveController::team/$1/$2');

		$routes->post('department/(:segment)', 'WorkHiveController::department/$1');
		$routes->post('department/(:segment)/(:num)', 'WorkHiveController::department/$1/$2');

		$routes->post('role/(:segment)', 'WorkHiveController::role/$1');
		$routes->post('role/(:segment)/(:num)', 'WorkHiveController::role/$1/$2');

		$routes->post('setting/save', 'WorkHiveController::setting/save'); // [SYNC]

		// Single-controller catch-all [MAIN] — keep after specific api routes
		$routes->match(['get', 'post'], '(:segment)', 'WorkHiveController::api/$1');
	});
});


*/

/*
|--------------------------------------------------------------------------
| WorkHive
|--------------------------------------------------------------------------
*/
$routes->group('work-hive', static function ($routes) {
	// Access / role pages
	$routes->get('access', 'WorkHiveController::access');
	$routes->get('admin', 'WorkHiveController::admin');
	$routes->get('manager', 'WorkHiveController::manager');
	$routes->get('lead', 'WorkHiveController::lead');
	$routes->get('hr', 'WorkHiveController::hr');
	$routes->get('employee', 'WorkHiveController::employee');
	$routes->get('logout', 'WorkHiveController::logout');

	// Sidebar / page navigation
	$routes->get('page', 'WorkHiveController::page');
	$routes->get('page/(:segment)', 'WorkHiveController::page/$1');

	// Local-only role/page audit (127.0.0.1)
	$routes->get('audit/impersonate/(:num)', 'WorkHiveAuditController::impersonate/$1');
	$routes->get('audit/report', 'WorkHiveAuditController::report');

	// APIs
	$routes->group('api', static function ($routes) {
		$routes->post('punch', 'WorkHiveController::punch');
		$routes->post('punch/in', 'WorkHiveController::punchIn');
		$routes->post('punch/out', 'WorkHiveController::punchOut');

		$routes->get('detail/(:segment)/(:num)', 'WorkHiveController::detail/$1/$2');
		$routes->get('sync/(:segment)', 'WorkHiveController::sync/$1');
		$routes->get('main-user/(:num)', 'WorkHiveController::mainUser/$1');

		$routes->post('task/comment/(:num)', 'WorkHiveController::taskComment/$1');
		$routes->post('task/(:segment)', 'WorkHiveController::task/$1');
		$routes->post('task/(:segment)/(:num)', 'WorkHiveController::task/$1/$2');

		$routes->post('project/(:segment)', 'WorkHiveController::project/$1');
		$routes->post('project/(:segment)/(:num)', 'WorkHiveController::project/$1/$2');

		$routes->post('work-log/(:segment)', 'WorkHiveController::workLog/$1');
		$routes->post('work-log/(:segment)/(:num)', 'WorkHiveController::workLog/$1/$2');

		$routes->post('attendance-request/(:segment)', 'WorkHiveController::attendanceRequest/$1');
		$routes->post('attendance-request/(:segment)/(:num)', 'WorkHiveController::attendanceRequest/$1/$2');

		$routes->post('leave-request/(:segment)', 'WorkHiveController::leaveRequest/$1');
		$routes->post('leave-request/(:segment)/(:num)', 'WorkHiveController::leaveRequest/$1/$2');
		$routes->get('leave-request/attachment/(:num)', 'WorkHiveController::leaveRequest/attachment/$1');

		$routes->post('holiday/(:segment)', 'WorkHiveController::holiday/$1');
		$routes->post('holiday/(:segment)/(:num)', 'WorkHiveController::holiday/$1/$2');
		$routes->post('weekoff/(:segment)', 'WorkHiveController::weekoff/$1');
		$routes->post('weekoff/(:segment)/(:num)', 'WorkHiveController::weekoff/$1/$2');

		$routes->post('attendance/(:segment)', 'WorkHiveController::attendanceRecord/$1');
		$routes->post('attendance/(:segment)/(:num)', 'WorkHiveController::attendanceRecord/$1/$2');

		$routes->post('employee/(:segment)', 'WorkHiveController::employeeRecord/$1');
		$routes->post('employee/(:segment)/(:num)', 'WorkHiveController::employeeRecord/$1/$2');

		$routes->post('team/(:segment)', 'WorkHiveController::team/$1');
		$routes->post('team/(:segment)/(:num)', 'WorkHiveController::team/$1/$2');

		$routes->post('department/(:segment)', 'WorkHiveController::department/$1');
		$routes->post('department/(:segment)/(:num)', 'WorkHiveController::department/$1/$2');

		$routes->post('role/(:segment)', 'WorkHiveController::role/$1');
		$routes->post('role/(:segment)/(:num)', 'WorkHiveController::role/$1/$2');

		$routes->post('setting/save', 'WorkHiveController::setting');
		$routes->post('setting/(:segment)', 'WorkHiveController::setting/$1');
	});
});

/*
|--------------------------------------------------------------------------
| Attend aliases → WorkHive  [MAIN optional]
|--------------------------------------------------------------------------
| Keep only if old sidebar/buttons still open /attend/attendance or /attend/api.
*/
$routes->group('attend', static function ($routes) {
	$routes->get('attendance', 'WorkHiveController::index');
	$routes->match(['get', 'post'], 'api/(:segment)', 'WorkHiveController::api/$1');
	$routes->get('logout', 'WorkHiveController::logout');
});


$routes->get('grid/qr', 'GridDashboard::scanner');




/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

// HRMS module routes on main server [MAIN]
if (is_file(APPPATH . 'Config/HRMS_Routes.php')) {
	require APPPATH . 'Config/HRMS_Routes.php';
}


/*
|--------------------------------------------------------------------------
| DMS , Inventory , HRMS , Finance , Procurement .
|--------------------------------------------------------------------------
*/

if (is_file(APPPATH . 'Config/DmsApiRoutes.php')) {
    require APPPATH . 'Config/DmsApiRoutes.php';
}
if (is_file(APPPATH . 'Config/DmsRoutes.php')) {
    require APPPATH . 'Config/DmsRoutes.php';
}


if (is_file(APPPATH . 'Config/HRMS_Routes.php')) {
    require APPPATH . 'Config/HRMS_Routes.php';
}

if (is_file(APPPATH . 'Config/Inventory_Routes.php')) {
    require APPPATH . 'Config/Inventory_Routes.php';
}


if (is_file(APPPATH . 'Config/FinanceRoutes.php')) {
    require APPPATH . 'Config/FinanceRoutes.php';
}

if (is_file(APPPATH . 'Config/ProcurementRoutes.php')) {
    require APPPATH . 'Config/ProcurementRoutes.php';
}

// Finance shell dynamic company/user context
$routes->get('finance/shell/context', 'finance\FinanceController::shellContext');
$routes->post('finance/shell/company/save', 'finance\FinanceController::saveCompanyProfile');
$routes->post('finance/shell/user/save', 'finance\FinanceController::saveUserProfile');

if (is_file(APPPATH . 'Config/FinanceRoutes.php')) {
    require APPPATH . 'Config/FinanceRoutes.php';
}
// Finance shell dynamic company/user context
$routes->get('finance/shell/context', 'finance\FinanceController::shellContext');
$routes->post('finance/shell/company/save', 'finance\FinanceController::saveCompanyProfile');
$routes->post('finance/shell/user/save', 'finance\FinanceController::saveUserProfile');



/*
|--------------------------------------------------------------------------
| TagLink + Sticker Studio + Sticker Form
|--------------------------------------------------------------------------

*/
if (is_file(APPPATH . 'Config/sticker_stu_routes.php')) {
	require APPPATH . 'Config/sticker_stu_routes.php';
}


// App Home Page module (self-contained)
if (file_exists(APPPATH . 'Config/AppHomePageRoutes.php')) {
    require APPPATH . 'Config/AppHomePageRoutes.php';
}


if (is_file(APPPATH . 'Config/AssetManagementRoutes.php')) {
    require APPPATH . 'Config/AssetManagementRoutes.php';
}

/*********************KVM********************************/


// --------------kvm----------------

$routes->get('kvm', 'KvmController::index');
$routes->post('kvm/api', 'KvmController::api');






// $routes->get(
//     'dashboard/open-nas',
//     'Dashboard::openNas'
// );

/*****9-11-26 */



$routes->get('dashboard/open-nas', static function () {
    return redirect()->to(site_url('NAS/home'));
});

$nasRoutesFile = APPPATH . 'Config/NasRoutes.php';

/*if (is_file($nasRoutesFile)) {
    require $nasRoutesFile;
}*/


// if (is_file(APPPATH . 'Config/NasRoutes.php')) {
//     require_once APPPATH . 'Config/NasRoutes.php';
// }
/**10-11-26 */
if (is_file($nasRoutesFile)) {
    require $nasRoutesFile;
}


$routes->get('qr_login', 'Signin::qr_login');

$routes->get(
    'qr_login_production',
    'Signin::qr_login_production'
);



// IoT Dashboard builder
// -----------------------------------------------------------------------------
$routes->get('iot-dashboard', 'IotDashboard::index');
$routes->match(['get', 'post'], 'iot-dashboard/api/(:segment)', 'IotDashboard::api/$1');


$routes->post('items/remove_device_node', 'View_page_controller::remove_device_node');





$routes->get(
    'items/modal_api/(:num)',
    'modal_api_controller::modal/$1'
);


// Save new API or update existing API
$routes->post(
    'items/saveapi',
    'modal_api_controller::save'
);


// Delete complete API configuration
$routes->post(
    'items/delete_api_config/(:num)',
    'modal_api_controller::delete/$1'
);


// Brand / Product / Model / image metadata used by modal_api.php
$routes->get(
    'items/modal_api_vendor_meta/(:num)',
    'modal_api_controller::vendorMeta/$1'
);


// Optional clean URLs for the same dedicated module.
$routes->get(
    'modal-api/(:num)',
    'modal_api_controller::modal/$1'
);

$routes->post(
    'modal-api/save',
    'modal_api_controller::save'
);

$routes->post(
    'modal-api/delete/(:num)',
    'modal_api_controller::delete/$1'
);

$routes->get(
    'modal-api/vendor-meta/(:num)',
    'modal_api_controller::vendorMeta/$1'
);



$routes->get(
    'items/modal_config',
    'modal_config_controller::modal_config'
);

$routes->post(
    'items/update_config_default_value',
    'modal_config_controller::update_config_default_value'
);





// view apis
// -----------------------------------------------------------------------------
/*$routes->get('items/viewapis/(:num)', 'Viewapis_controller::viewapis/$1');
$routes->get('items/viewapis', 'Viewapis_controller::viewapis');
$routes->match(['get', 'post'], 'items/list_data2/(:num)', 'Viewapis_controller::list_data2/$1');
$routes->match(['get', 'post'], 'items/list_data2', 'Viewapis_controller::list_data2');
$routes->match(['get', 'post'], 'items/api_config_preview/(:num)', 'Viewapis_controller::api_config_preview/$1');
$routes->match(['get', 'post'], 'items/api_config_preview', 'Viewapis_controller::api_config_preview');
$routes->match(['get', 'post'], 'items/delete_api_config/(:num)', 'Viewapis_controller::delete_api_config/$1');
$routes->match(['get', 'post'], 'items/delete_api_config', 'Viewapis_controller::delete_api_config');*/



$routes->get('items/viewapis/(:num)', 'Viewapis_controller::viewapis/$1');
$routes->get('items/viewapis', 'Viewapis_controller::viewapis');
$routes->match(['get', 'post'], 'items/list_data2/(:num)', 'Viewapis_controller::list_data2/$1');
$routes->match(['get', 'post'], 'items/list_data2', 'Viewapis_controller::list_data2');
$routes->match(['get', 'post'], 'items/api_config_preview/(:num)', 'Viewapis_controller::api_config_preview/$1');
$routes->match(['get', 'post'], 'items/api_config_preview', 'Viewapis_controller::api_config_preview');
$routes->match(['get', 'post'], 'items/delete_api_config/(:num)', 'Viewapis_controller::delete_api_config/$1');
$routes->match(['get', 'post'], 'items/delete_api_config', 'Viewapis_controller::delete_api_config');




/***********GRID**********************************/


// -----------------------------------------------------------------------------
// Grid Dashboard builder (Grid New)
// -----------------------------------------------------------------------------
//$routes->get('grid-dashboard', 'GridDashboard::index');
//$routes->match(['get', 'post'], 'grid-dashboard/api/(:segment)', 'GridDashboard::api/$1');

// Scanned professional QR. Same shell as /grid; the page reads user_id and grid_id.
$routes->get('grid/connect', 'GRID_Controllers\GridController::index');


$routes->get('grid-dashboard', 'GridDashboard::index');
$routes->get('grid-dashboard/scanner', 'GridDashboard::scanner');
$routes->match(['get', 'post'], 'grid-dashboard/api/(:segment)', 'GridDashboard::api/$1');







// Grid Community Routes
$routes->get('grid', 'GRID_Controllers\GridController::index');
$routes->get('grid/profile', 'GRID_Controllers\GridController::profile');
$routes->get('grid/messaging', 'GRID_Controllers\GridController::messaging');
$routes->get('grid/team', 'GRID_Controllers\GridController::team');
$routes->get('grid/demo_loader', 'GRID_Controllers\GridController::demo_loader');
$routes->get('grid/network', 'GRID_Controllers\GridController::network');
$routes->get('grid/jobs', 'GRID_Controllers\GridController::jobs');




$gridRoutesFile = APPPATH . 'Config/GRID_Config/Routes.php';
if (is_file($gridRoutesFile)) {
    require $gridRoutesFile;
}

// Circle Connect share links must be registered before AppHomePageRoutes.
// CodeIgniter 4.1 keeps the first duplicate route, so these routes ensure
// Circle Connect is not intercepted by the Professional Space route.
/******27-10-2
 * 
 */

// $routes->get(
//     'grid-chat/connect',
//     '\App\Controllers\GRID_CHAT_CONTROLLERS\GridChatController::connect'
// );

// $routes->get(
//     'grid_chat/connect',
//     '\App\Controllers\GRID_CHAT_CONTROLLERS\GridChatController::connect'
// );

// // App Home Page module (self-contained)
// if (file_exists(APPPATH . 'Config/AppHomePageRoutes.php')) {
//     require APPPATH . 'Config/AppHomePageRoutes.php';
// }

// 




/******27-10-26 */


// $routes->get(
//     'grid-chat/connect',
//     '\App\Controllers\GRID_CHAT_CONTROLLERS\GridChatController::connect'
// );

// $routes->get(
//     'grid_chat/connect',
//     '\App\Controllers\GRID_CHAT_CONTROLLERS\GridChatController::connect'
// );

// -----------------------------------------------------------------------------
// GRID CHAT Module (Config: Config/GRID_CHAT_CONFIG/Routes.php | Controller: Controllers/GRID_CHAT_Controllers)
// -----------------------------------------------------------------------------
if (file_exists(APPPATH . 'Config/GRID_CHAT_CONFIG/Routes.php')) {
    require APPPATH . 'Config/GRID_CHAT_CONFIG/Routes.php';
}


// $routes->get('work-hive', 'WorkHiveController::index');
// $routes->get('work-hive/ping', 'WorkHiveController::ping');
// $routes->match(
//     ['get', 'post'],
//     'work-hive/api/(:segment)',
//     'WorkHiveController::api/$1'
// );
// $routes->get('work-hive/logout', 'WorkHiveController::logout');


// Circle Connect route must come before AppHomePageRoutes.

// $routes->get(
//     'grid-chat/connect',
//     '\App\Controllers\GRID_CHAT_CONTROLLERS\GridChatController::connect'
// );

// $routes->get(
//     'grid_chat/connect',
//     '\App\Controllers\GRID_CHAT_CONTROLLERS\GridChatController::connect'
// );


// // App Home Page module
// if (file_exists(APPPATH . 'Config/AppHomePageRoutes.php')) {
//     require APPPATH . 'Config/AppHomePageRoutes.php';
// }

/*
// -----------------------------------------------------------------------------
// GRID CHAT Module (Config: Config/GRID_CHAT_CONFIG/Routes.php | Controller: Controllers/GRID_CHAT_Controllers)
// -----------------------------------------------------------------------------
if (file_exists(APPPATH . 'Config/GRID_CHAT_CONFIG/Routes.php')) {
    require APPPATH . 'Config/GRID_CHAT_CONFIG/Routes.php';
}


*/

/******27-10-26
 * 
 */
$routes->get('grid-dashboard/scanner', 'GridDashboard::scanner');

/***********GRID**********************************/
$gridRoutesFile = APPPATH . 'Config/GRID_Config/Routes.php';
if (is_file($gridRoutesFile)) {
    require $gridRoutesFile;
}




$routes->get(
    'items/get_existing_parameters',
    'copy_api_contorller::get_existing_parameters'
);


$routes->get('items/get_existing_parameters', 'copy_api_contorller::get_existing_parameters');
$routes->post('items/saveconfig', 'copy_api_contorller::saveconfig');
$routes->get('items/copy_api', 'copy_api_contorller::copy_api');


$routes->get('items/copy_modal', 'vendor_controller::copy_modal');
$routes->post('items/save_copy_modal', 'vendor_controller::save_copy_modal');
$routes->get('items/copy_modal_preview', 'vendor_controller::copy_modal_preview');
$routes->post('items/copy_modal_preview', 'vendor_controller::copy_modal_preview');





// Vendor main page
$routes->get('items/vendor', 'vendor_controller::vendor');

// Add Model modal
$routes->match(
    ['get', 'post'],
    'items/modal_vendorproduct',
    'vendor_controller::modal_vendorproduct'
);

// Vendor model/card list
$routes->match(
    ['get', 'post'],
    'items/list_data1vendor',
    'vendor_controller::list_data1vendor'
);

// Save / Update vendor model
$routes->post(
    'items/savevendorproduct',
    'vendor_controller::savevendorproduct'
);

// Delete vendor model
$routes->post(
    'items/deletevendorproduct',
    'vendor_controller::deletevendorproduct'
);


// Load routes for the Grid module.
$gridRoutesFile = APPPATH . 'Config/GRID_Config/Routes.php';

if (is_file($gridRoutesFile)) {
    require $gridRoutesFile;
}



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



if (is_file(APPPATH . 'Config/E-label_routes.php')) {
    require APPPATH . 'Config/E-label_routes.php';
}


if (is_file(APPPATH . 'Config/ControlCenter-routes.php')) {
    require APPPATH . 'Config/ControlCenter-routes.php';
}



if (is_file(APPPATH . 'Config/APIGuideRoutes.php')) {
    require APPPATH . 'Config/APIGuideRoutes.php';
}


$routes->post('api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid-chat/api/call/decline', 'DevicePushController::declineCall');
$routes->post('grid_chat/api/call/decline', 'DevicePushController::declineCall');


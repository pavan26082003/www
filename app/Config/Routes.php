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

/*
|--------------------------------------------------------------------------
| TagLink + AssetsTag  [SYNC]
|--------------------------------------------------------------------------
| Pages: /taglink, /taglink/iot-models, /taglink/temp-tags, /assetstag
| Session APIs: /taglink/api/*
| REST mirror: /api/v1/taglink/*
| AssetsTag data API: /API/AssetsTag/index.php (standalone — not CI routes)
*/
$routes->group('taglink', static function ($routes) {
	// Pages
	$routes->get('/', 'Taglink::index');
	$routes->get('iot-models', 'Taglink::iotModels');
	$routes->get('temp-tags', 'Taglink::tempTags');

	// Session-backed JSON APIs
	$routes->group('api', static function ($routes) {
		$routes->get('health', 'Taglink::apiHealth');
		$routes->get('platforms', 'Taglink::apiPlatforms');
		$routes->get('dashboard', 'Taglink::apiDashboard');
		$routes->match(['get', 'post'], 'tags', 'Taglink::apiTags');
		$routes->get('models', 'Taglink::apiModels');
		$routes->get('models/(:num)', 'Taglink::apiModel/$1');
		$routes->get('models/(:num)/sticker-url', 'Taglink::apiStickerUrl/$1');
	});
});

$routes->get('assetstag', 'AssetsTag::index');

$routes->group('api/v1/taglink', ['namespace' => 'App\\Controllers\\Api\\Taglink'], static function ($routes) {
	$routes->get('health', 'TaglinkApiController::health');
	$routes->get('platforms', 'TaglinkApiController::platforms');
	$routes->get('dashboard', 'TaglinkApiController::dashboard');
	$routes->match(['get', 'post'], 'tags', 'TaglinkApiController::tags');
	$routes->get('models', 'TaglinkApiController::models');
	$routes->get('models/(:num)', 'TaglinkApiController::model/$1');
	$routes->get('models/(:num)/sticker-url', 'TaglinkApiController::stickerUrl/$1');
});

/*
|--------------------------------------------------------------------------
| Items / Stickers  [SYNC + MAIN extras]
|--------------------------------------------------------------------------
*/
// $routes->post('items/saveparameters', 'Items::saveParameters');
// $routes->match(['get', 'post'], 'items/api_page_linking/(:num)', 'Items::api_page_linking/$1');
// $routes->post('items/check_duplicate_parameter', 'Items::check_duplicate_parameter');
// $routes->get('items/sticker_form/(:any)', 'Sticker::sticker_form/$1');

/*$routes->post('items/save_view_page_ui_theme', 'Items::save_view_page_ui_theme'); // [MAIN]
$routes->post('items/update_linking_data', 'Items::update_linking_data');
$routes->post('items/delete_param', 'Items::delete_param');
$routes->post('items/add_param', 'Items::add_param');
$routes->post('items/update_page_data', 'Items::update_page_data');*/


$routes->get('items/view_page_ui/(:num)', 'View_page_controller::view_page_ui/$1');
$routes->post('items/save_view_page_ui_theme', 'View_page_controller::save_view_page_ui_theme');
$routes->get('items/api_page_linking/(:num)', 'Modal_api_page_linking_controller::api_page_linking/$1');
$routes->post('items/submit_page_params', 'Modal_api_page_linking_controller::submit_page_params');
$routes->post('items/save_page_linking_row', 'Modal_api_page_linking_controller::save_page_linking_row');


$routes->group('items', static function ($routes) {
	$routes->get('getDeviceTypes', 'Items::getDeviceTypes');
	// $routes->get('sticker_form/(:any)', 'Items::sticker_form/$1');
	$routes->get('sticker_form', 'Items::sticker_form');
	$routes->get('sticker_form/(:num)', 'Items::sticker_form/$1');
	$routes->get('sticker_print', 'Items::sticker_print');
	$routes->get('sticker_edit', 'Items::sticker_edit');
	$routes->post('saveFields', 'Items::saveFields');
	$routes->get('getFields', 'Items::getFields');
	$routes->get('view/(:any)', 'Items::viewStickerForm/$1');
	$routes->get('product_description/(:any)', 'Items::product_description/$1');
	$routes->get('product_description_form/(:num)', 'Items::product_description_form/$1');
	$routes->post('save_product_description', 'Items::save_product_description');
	$routes->get('getNodeIdsAllTables', 'Items::getNodeIdsAllTables');
	$routes->get('getNodeId', 'Items::getNodeId');
	$routes->post('deleteFieldPosition', 'Items::deleteFieldPosition');
	$routes->post('saveStickerFields', 'Items::saveStickerFields');
	$routes->get('printer_list', 'Items::listPrinters');
	$routes->post('printer_info', 'Items::getPrinterInfo');
	$routes->get('printer_page', 'Items::listPrinters');

	// Auto-test helpers [MAIN]
	$routes->post('specificationsapi', 'Items::specificationsapi');
	$routes->options('specificationsapi', 'Items::specificationsapi');
	$routes->get('test', 'Items::test');
});
$routes->get('generated-form', 'Items::generatedForm');

/**************************************************************/
$routes->post('items/delete_saved_page', 'Items::delete_saved_page');
$routes->post('items/reorder_saved_pages', 'Items::reorder_saved_pages');



/***************************************************************/




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









/*************************************************************************/


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
| TagLink + AssetsTag
|--------------------------------------------------------------------------
| Pages: /taglink, /taglink/iot-models, /taglink/temp-tags, /assetstag
| Session APIs: /taglink/api/*
| REST mirror: /api/v1/taglink/*  (same handlers as /API/Taglink/*)
| AssetsTag API: /API/AssetsTag/index.php (standalone — not CI routes)
*/
$routes->group('taglink', static function ($routes) {
	// Pages
	$routes->get('/', 'Taglink::index');
	$routes->get('iot-models', 'Taglink::iotModels');
	$routes->get('temp-tags', 'Taglink::tempTags');

	// Session-backed JSON APIs (used by taglink.php / taglink_iot_models.php)
	$routes->group('api', static function ($routes) {
		$routes->get('health', 'Taglink::apiHealth');
		$routes->get('platforms', 'Taglink::apiPlatforms');
		$routes->get('dashboard', 'Taglink::apiDashboard');
		$routes->match(['get', 'post'], 'tags', 'Taglink::apiTags');
		$routes->get('models', 'Taglink::apiModels');
		$routes->get('models/(:num)', 'Taglink::apiModel/$1');
		$routes->get('models/(:num)/sticker-url', 'Taglink::apiStickerUrl/$1');
	});
});

$routes->get('assetstag', 'AssetsTag::index');

$routes->group('api/v1/taglink', ['namespace' => 'App\\Controllers\\Api\\Taglink'], static function ($routes) {
	$routes->get('health', 'TaglinkApiController::health');
	$routes->get('platforms', 'TaglinkApiController::platforms');
	$routes->get('dashboard', 'TaglinkApiController::dashboard');
	$routes->match(['get', 'post'], 'tags', 'TaglinkApiController::tags');
	$routes->get('models', 'TaglinkApiController::models');
	$routes->get('models/(:num)', 'TaglinkApiController::model/$1');
	$routes->get('models/(:num)/sticker-url', 'TaglinkApiController::stickerUrl/$1');
});

/*
|--------------------------------------------------------------------------
| Items / Stickers
|--------------------------------------------------------------------------
*/
// $routes->post('items/saveparameters', 'Items::saveParameters');
// $routes->match(['get', 'post'], 'items/api_page_linking/(:num)', 'Items::api_page_linking/$1');
// $routes->post('items/check_duplicate_parameter', 'Items::check_duplicate_parameter');
$routes->post('items/update_linking_data', 'Items::update_linking_data');
$routes->post('items/delete_param', 'Items::delete_param');
$routes->post('items/add_param', 'Items::add_param');
$routes->post('items/update_page_data', 'Items::update_page_data');

$routes->group('items', static function ($routes) {
	$routes->get('getDeviceTypes', 'Items::getDeviceTypes');
	// $routes->get('sticker_form/(:any)', 'Items::sticker_form/$1');
	$routes->get('sticker_form', 'Items::sticker_form');
	$routes->get('sticker_form/(:num)', 'Items::sticker_form/$1');
	$routes->get('sticker_print', 'Items::sticker_print');
	$routes->get('sticker_edit', 'Items::sticker_edit');
	$routes->post('saveFields', 'Items::saveFields');
	$routes->get('getFields', 'Items::getFields');
	$routes->get('view/(:any)', 'Items::viewStickerForm/$1');
	$routes->get('product_description/(:any)', 'Items::product_description/$1');
	$routes->get('product_description_form/(:num)', 'Items::product_description_form/$1');
	$routes->post('save_product_description', 'Items::save_product_description');
	$routes->get('getNodeIdsAllTables', 'Items::getNodeIdsAllTables');
	$routes->get('getNodeId', 'Items::getNodeId');
	$routes->get('resolveNodeId', 'Items::resolveNodeId');
	$routes->post('deleteFieldPosition', 'Items::deleteFieldPosition');
	$routes->post('saveStickerFields', 'Items::saveStickerFields');
	$routes->get('printer_list', 'Items::listPrinters');
	$routes->post('printer_info', 'Items::getPrinterInfo');
	$routes->get('printer_page', 'Items::listPrinters');
});
$routes->get('generated-form', 'Items::generatedForm');


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



$routes->get(
    'items/display_lable',
    'Items::sticker_display'
);



$routes->get(
    'dashboard/open-nas',
    'Dashboard::openNas'
);


$nasRoutesFile = APPPATH . 'Config/NasRoutes.php';

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

// Grid New studio (dash URL needs an explicit route; translateURIDashes is false)
// -----------------------------------------------------------------------------
$routes->get('grid-dashboard', 'GridDashboard::index');
$routes->get('grid-dashboard/scanner', 'GridDashboard::scanner');
$routes->match(['get', 'post'], 'grid-dashboard/api/(:segment)', 'GridDashboard::api/$1');

$routes->get('items/vendor', 'vendor_controller::vendor');
$routes->get('items/index', 'Items::index');
$routes->get('items', 'Items::index');


$routes->post('items/remove_device_node', 'View_page_controller::remove_device_node');





$routes->get(
    'items/modal_api/(:num)',
    'Items::modal_api/$1'
);


// Save new API or update existing API
$routes->post(
    'items/saveapi',
    'Items::saveapi'
);


// Delete complete API configuration
$routes->post(
    'items/delete_api_config/(:num)',
    'Items::delete_api_config/$1'
);


// Brand / Product / Model / image metadata used by modal_api.php
$routes->get(
    'items/modal_api_vendor_meta/(:num)',
    'Items::vendorMeta/$1'
);


// Optional clean URLs for the same dedicated module.
$routes->get(
    'modal-api/(:num)',
    'Items::modal_api/$1'
);

$routes->post(
    'modal-api/save',
    'Items::saveapi'
);

$routes->post(
    'modal-api/delete/(:num)',
    'Items::delete_api_config/$1'
);

$routes->get(
    'modal-api/vendor-meta/(:num)',
    'Items::vendorMeta/$1'
);



$routes->get(
    'items/modal_config',
    'Items::modal_config'
);

$routes->post(
    'items/update_config_default_value',
    'Items::update_config_default_value'
);





// view apis
// -----------------------------------------------------------------------------
$routes->get('items/viewapis/(:num)', 'Viewapis_controller::viewapis/$1');
$routes->get('items/viewapis', 'Viewapis_controller::viewapis');
$routes->match(['get', 'post'], 'items/list_data2/(:num)', 'Viewapis_controller::list_data2/$1');
$routes->match(['get', 'post'], 'items/list_data2', 'Viewapis_controller::list_data2');
$routes->match(['get', 'post'], 'items/api_config_preview/(:num)', 'Viewapis_controller::api_config_preview/$1');
$routes->match(['get', 'post'], 'items/api_config_preview', 'Viewapis_controller::api_config_preview');
$routes->match(['get', 'post'], 'items/delete_api_config/(:num)', 'Viewapis_controller::delete_api_config/$1');
$routes->match(['get', 'post'], 'items/delete_api_config', 'Viewapis_controller::delete_api_config');

//-----------------------------------------------------------------------------
// Vendor main page route is registered above as vendor_controller::vendor.
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

/***********GRID**********************************/
$gridRoutesFile = APPPATH . 'Config/GRID_Config/Routes.php';
if (is_file($gridRoutesFile)) {
    require $gridRoutesFile;
}
// Scanned professional QR. Same shell as /grid; the page reads user_id and grid_id.
$routes->get('grid/connect', 'GRID_Controllers\GridController::index');


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

// -----------------------------------------------------------------------------
// GRID CHAT Module (Config: Config/GRID_CHAT_CONFIG/Routes.php | Controller: Controllers/GRID_CHAT_CONTROLLERS)
// -----------------------------------------------------------------------------
$gridChatRoutesFile = APPPATH . 'Config/GRID_CHAT_CONFIG/Routes.php';
if (is_file($gridChatRoutesFile)) {
    require $gridChatRoutesFile;
}
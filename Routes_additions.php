// Add these lines inside app/Config/Routes.php
$routes->get('kvm', 'KvmController::index');
$routes->post('kvm/api', 'KvmController::api');

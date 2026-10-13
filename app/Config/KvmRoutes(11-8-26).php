<?php

// Include this file from app/Config/Routes.php after $routes is created.
$routes->get('kvm', 'KvmController::index');
$routes->get('kvm/ping', 'KvmController::ping');
$routes->post('kvm/api', 'KvmController::api');

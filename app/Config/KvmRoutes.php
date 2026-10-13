<?php

// Local KVM test routes. Add these only once after $routes is created.
$routes->get('kvm', 'KvmController::index');
$routes->get('kvm/ping', 'KvmController::ping');
$routes->match(['get', 'post'], 'kvm/api', 'KvmController::api');

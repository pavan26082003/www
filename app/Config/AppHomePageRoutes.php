<?php

// App Home Page routes — self-contained module
// Files:
//   app/Controllers/AppHomePageController.php
//   app/Models/AppHomePageModel.php
//   app/Views/app_home_page/index.php
//   assets/css/app_home_page.css
//   assets/js/app_home_page.js

$routes->get('app-home-page', 'AppHomePageController::index');
$routes->match(['get', 'post', 'put', 'patch'], 'app-home-page/api/(:segment)', 'AppHomePageController::api/$1');

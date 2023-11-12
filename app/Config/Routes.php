<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/**
 * Service Integration
 */

service('auth')->routes($routes);



/**
 * Project Routing
 */




/**
 * HMVC Routing
 *
 * This section iterates through module directories to load additional route configurations.
 */
$moduleBasePath = APPPATH . 'Modules';

foreach (glob($moduleBasePath . '/*', GLOB_ONLYDIR) as $moduleDirectory) {
    $routesConfigFile = $moduleDirectory . '/Config/Routes.php';

    if (is_file($routesConfigFile)) {
        // Include the routes configuration from the module.
        require_once($routesConfigFile);
    }
}

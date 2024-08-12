<?php

require_once getcwd() . '/vendor/autoload.php';

use PhpMvc\AppBuilder;
AppBuilder::useNamespace('AmichiamociApp');
AppBuilder::useSession();

// routes
AppBuilder::routes(function($routes) {
    $routes->ignore('content/{*file}');

    // default route
    $routes->add('default', '{controller=Home}/{action=index}/{id?}');
});

// build
AppBuilder::build();
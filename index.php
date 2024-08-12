<?php
// use aoutoload (recommended)
require_once getcwd() . '/vendor/autoload.php';
// or include the required files
// require_once getcwd() . '/vendor/php-mvc-project/php-mvc/src/index.php';

// import the AppBuilder class
use PhpMvc\AppBuilder;

// be sure to specify the namespace of your application
AppBuilder::useNamespace('AmichiamociApp');

// use session if you need
AppBuilder::useSession();

// routes
AppBuilder::routes(function($routes) {
    // skip all the paths that point to the folder /content
    $routes->ignore('content/{*file}');

    // default route
    $routes->add('default', '{controller=Home}/{action=index}/{id?}');
});

// build
AppBuilder::build();
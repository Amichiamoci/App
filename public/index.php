<?php

use App\Kernel;

require_once dirname(path: __DIR__) . '/vendor/autoload_runtime.php';

return function (array $context): Kernel {
    return new Kernel(
        environment: $context['APP_ENV'],
        debug: (bool) $context['APP_DEBUG'],
    );
};

<?php

use App\Kernel;

require_once dirname(path: __DIR__) . '/vendor/autoload_runtime.php';

/*
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']))
{
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
}
if (!empty($_SERVER['HTTP_CF_VISITOR']))
{
    try {
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = json_decode(json: $_SERVER['HTTP_CF_VISITOR'])['scheme'];
    } catch (\Throwable) { }
}
*/

return function (array $context): Kernel {
    return new Kernel(
        environment: $context['APP_ENV'],
        debug: (bool) $context['APP_DEBUG'],
    );
};

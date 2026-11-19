<?php

// The legacy static file would otherwise shadow the Laravel robots route.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strtolower(rtrim((string) $path, '/')) === '/robots.txt') {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = getcwd().'/index.php';
    require $_SERVER['SCRIPT_FILENAME'];

    return true;
}

return require dirname(__DIR__, 2).'/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php';

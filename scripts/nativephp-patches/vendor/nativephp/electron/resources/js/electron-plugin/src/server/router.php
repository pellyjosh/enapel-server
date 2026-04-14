<?php

$publicDir = __DIR__ . '/public';
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Check if it's a static file
if ($uri !== '/' && file_exists($publicDir . $uri) && !is_dir($publicDir . $uri)) {
    $path = $publicDir . $uri;
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    $mimetypes = [
        'js'   => 'application/javascript',
        'css'  => 'text/css',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'otf'  => 'font/otf',
    ];

    if (isset($mimetypes[$extension])) {
        header('Content-Type: ' . $mimetypes[$extension]);
    }

    readfile($path);
    exit;
}

// Emulate mod_rewrite for Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once $publicDir . '/index.php';

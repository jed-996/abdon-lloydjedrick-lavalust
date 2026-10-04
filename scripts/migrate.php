<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit(1); }

$route = $argv[1] ?? '/status';
if (!preg_match('#^/(?:migrate|status|rollback|rollback-all|refresh|create-migration/[a-z][a-z0-9_]{2,80})$#', $route)) {
    fwrite(STDERR, "Invalid migration route.\n");
    exit(1);
}

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = $route;
$_SERVER['SCRIPT_NAME'] = '/index.php';
require dirname(__DIR__) . '/public/index.php';

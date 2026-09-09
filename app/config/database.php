<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
$database['main'] = [
 'driver' => getenv('DB_DRIVER') ?: 'mysql',
 'hostname' => getenv('DB_HOST') ?: 'localhost',
 'port' => getenv('DB_PORT') ?: '3306',
 'username' => getenv('DB_USERNAME') ?: '',
 'password' => getenv('DB_PASSWORD') ?: '',
 'database' => getenv('DB_DATABASE') ?: (getenv('DB_NAME') ?: 'defaultdb'),
 'charset' => 'utf8mb4', 'dbprefix' => '',
 'path' => getenv('DB_PATH') ?: ROOT_DIR . 'runtime/products.sqlite',
 'ssl_ca' => getenv('DB_SSL_CA') ?: ROOT_DIR . 'certs/ca.pem',
 'ssl_required' => (getenv('DB_SSL_REQUIRED') ?: 'true') !== 'false',
];
if (getenv('APP_ENV') === 'production' && $database['main']['driver'] !== 'mysql') {
 throw new RuntimeException('Production requires an Aiven MySQL database.');
}

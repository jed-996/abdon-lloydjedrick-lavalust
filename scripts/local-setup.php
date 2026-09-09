<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
if (is_file($root . '/.env')) { fwrite(STDERR, "Existing .env was not overwritten.\n"); exit(1); }
if (!is_dir($root . '/runtime')) mkdir($root . '/runtime', 0770, true);
$password = bin2hex(random_bytes(10));
file_put_contents($root . '/.env', "APP_ENV=development\nAPP_URL=http://127.0.0.1:8080\nAPP_KEY=" . bin2hex(random_bytes(32)) .
 "\nDB_DRIVER=sqlite\nDB_SSL_REQUIRED=false\nADMIN_EMAIL=admin@example.test\nADMIN_PASSWORD_HASH='" . password_hash($password, PASSWORD_DEFAULT) . "'\n");
file_put_contents($root . '/runtime/local-access.txt', "Local preview only (SQLite, not Aiven)\nURL: http://127.0.0.1:8080\nEmail: admin@example.test\nPassword: " . $password . "\n");
require __DIR__ . '/setup.php';
echo "Local credentials saved to runtime/local-access.txt (excluded from Git).\n";

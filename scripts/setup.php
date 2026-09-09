<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/load-env.php';
define('PREVENT_DIRECT_ACCESS', true);
define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_DIR', ROOT_DIR . 'app/');
define('SYSTEM_DIR', ROOT_DIR . 'scheme/');
require SYSTEM_DIR . 'kernel/Registry.php';
require SYSTEM_DIR . 'kernel/Routine.php';
require SYSTEM_DIR . 'database/Database.php';
try {
 foreach (['session', 'logs', 'cache'] as $directory) {
  $path = ROOT_DIR . 'runtime/' . $directory;
  if (!is_dir($path)) mkdir($path, 0770, true);
 }
 $db = new Database();
 if ((getenv('DB_DRIVER') ?: 'mysql') === 'sqlite') {
  $db->raw('CREATE TABLE IF NOT EXISTS products (
   id INTEGER PRIMARY KEY AUTOINCREMENT, product_name VARCHAR(100) NOT NULL,
   description TEXT NOT NULL, price DECIMAL(10,2) NOT NULL CHECK(price >= 0),
   quantity INTEGER NOT NULL CHECK(quantity >= 0),
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)');
 } else {
  $db->raw(file_get_contents(ROOT_DIR . 'database/products.sql'));
 }
 echo "Products table is ready. Existing products were preserved.\n";
} catch (Throwable $error) {
 fwrite(STDERR, "Database setup failed. Check database environment variables, network access, and the CA certificate.\n");
 exit(1);
}

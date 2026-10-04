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
    $sqlite = (getenv('DB_DRIVER') ?: 'mysql') === 'sqlite';

    if ($sqlite) {
        $db->raw('CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT, product_name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL, price DECIMAL(10,2) NOT NULL CHECK(price >= 0),
            quantity INTEGER NOT NULL CHECK(quantity >= 0),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)');
        $db->raw('CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(100) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT \'user\', is_active INTEGER NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL)');
        $db->raw('CREATE TABLE IF NOT EXISTS refresh_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL,
            token TEXT NOT NULL, expires_at DATETIME NOT NULL, jti TEXT NOT NULL)');
    } else {
        $db->raw(file_get_contents(ROOT_DIR . 'database/products.sql'));
        $db->raw('CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            username VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM(\'admin\',\'moderator\',\'user\') NOT NULL DEFAULT \'user\',
            is_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (id), UNIQUE KEY username_unique (username),
            UNIQUE KEY email_unique (email), KEY role_idx (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $db->raw('CREATE TABLE IF NOT EXISTS refresh_tokens (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL, token TEXT NOT NULL,
            expires_at DATETIME NOT NULL, jti TEXT NOT NULL,
            PRIMARY KEY (id), KEY user_id_idx (user_id), KEY expires_at_idx (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
    }

    $email = strtolower(trim(getenv('ADMIN_EMAIL') ?: ''));
    $hash = getenv('ADMIN_PASSWORD_HASH') ?: '';
    if ($email === '' || $hash === '') {
        throw new RuntimeException('ADMIN_EMAIL and ADMIN_PASSWORD_HASH are required.');
    }
    $username = preg_replace('/[^a-z0-9_]/', '', strstr($email, '@', true)) ?: 'labadmin';

    if ($sqlite) {
        $existing = $db->raw('SELECT id FROM users WHERE email = ? LIMIT 1', [$email])->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            $db->raw('UPDATE users SET password = ?, role = ?, is_active = 1 WHERE id = ?', [$hash, 'admin', $existing['id']]);
        } else {
            $db->raw('INSERT INTO users (username, email, password, role, is_active) VALUES (?, ?, ?, ?, 1)', [$username, $email, $hash, 'admin']);
        }
    } else {
        $db->raw('INSERT INTO users (username, email, password, role, is_active)
            VALUES (?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE password = VALUES(password), role = VALUES(role), is_active = 1',
            [$username, $email, $hash, 'admin']);
    }

    echo "Database tables and API administrator are ready. Existing products were preserved.\n";
} catch (Throwable $error) {
    fwrite(STDERR, "Database setup failed. Check database environment variables, network access, and the CA certificate.\n");
    exit(1);
}

<?php

class MigrationCommand
{
    public static $command = 'migration';
    public static $description = 'Run and inspect LavaLust database migrations';
    public static $arguments = [
        'run' => 'Run pending migrations',
        'status' => 'Show migration status',
        'create-migration <name>' => 'Create a migration file',
        'rollback' => 'Roll back the latest migration',
        'rollback-all' => 'Roll back every migration',
        'refresh' => 'Roll back and re-run all migrations',
    ];

    public function handle($action = null, array $flags = [])
    {
        global $argv;
        $action = $action ?: 'status';
        $routes = [
            'run' => '/migrate',
            'status' => '/status',
            'rollback' => '/rollback',
            'rollback-all' => '/rollback-all',
            'refresh' => '/refresh',
        ];

        if ($action === 'create-migration') {
            $name = $argv[3] ?? ($flags['name'] ?? '');
            if (!preg_match('/^[a-z][a-z0-9_]{2,80}$/', $name)) {
                fwrite(STDERR, "Usage: php lava migration create-migration migration_name\n");
                exit(1);
            }
            $routes[$action] = '/create-migration/' . $name;
        }

        if (!isset($routes[$action])) {
            fwrite(STDERR, "Unknown migration action: {$action}\n");
            exit(1);
        }

        $script = dirname(__DIR__, 2) . '/scripts/migrate.php';
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($routes[$action]);
        passthru($command, $exitCode);
        exit($exitCode);
    }
}

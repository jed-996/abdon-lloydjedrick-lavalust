<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Migration commands run through the CLI. Web access stays disabled in production.
$config['migration_enabled'] = PHP_SAPI === 'cli' || getenv('APP_ENV') !== 'production';
$config['migration_table'] = 'migrations';
$config['migration_path'] = APP_DIR . 'migrations/';

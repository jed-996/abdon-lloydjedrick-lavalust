<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class MigrationController extends Controller
{
    private function migration()
    {
        if (PHP_SAPI !== 'cli' && getenv('APP_ENV') === 'production') {
            show_404();
        }
        header('Content-Type: text/plain; charset=UTF-8');
        $this->call->library('migration');
        return $this->migration;
    }

    public function create_migration($migrationClass)
    {
        if (!preg_match('/^[a-z][a-z0-9_]{2,80}$/', $migrationClass)) {
            http_response_code(422);
            echo "Use a lowercase snake_case migration name.\n";
            return;
        }
        $this->migration()->create_migration($migrationClass);
    }

    public function migrate() { $this->migration()->migrate(); }
    public function rollback() { $this->migration()->rollback(); }
    public function rollback_all() { $this->migration()->rollback_all(); }
    public function refresh() { $this->migration()->refresh(); }
    public function status() { $this->migration()->status(); }
}

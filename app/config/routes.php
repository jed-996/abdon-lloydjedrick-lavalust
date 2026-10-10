<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/** @var object $router */

$router->get('/', 'AuthController::index');
$router->get('/welcome', 'Welcome::index');
$router->get('/student', 'StudentController::index');
$router->get('/student/profile', 'StudentController::profile')->middleware('student');
$router->get('/users', 'UsersController::index');
$router->get('/user', 'UsersController::index');

$router->get('/login', 'AuthController::login');
$router->post('/login', 'AuthController::login');
$router->group(['middleware' => 'auth'], function ($router) {
    $router->post('/logout', 'AuthController::logout');
    $router->get('/products', 'ProductController::index');
    $router->get('/products/database-evidence', 'ProductController::database_evidence');
    $router->get('/products/create', 'ProductController::create');
    $router->post('/products/create', 'ProductController::create');
    $router->get('/products/edit/{id}', 'ProductController::edit')->where_number('id');
    $router->post('/products/edit/{id}', 'ProductController::edit')->where_number('id');
    $router->get('/products/delete/{id}', 'ProductController::delete')->where_number('id');
    $router->post('/products/delete/{id}', 'ProductController::delete')->where_number('id');
});


$router->post('/api/auth/login', 'ApiAuthController::login');
$router->post('/api/auth/refresh', 'ApiAuthController::refresh');
$router->post('/api/auth/logout', 'ApiAuthController::logout');
$router->get('/api/auth/me', 'ApiAuthController::me');
$router->get('/api/products', 'ApiProductController::index');
$router->post('/api/products', 'ApiProductController::create');
$router->get('/api/products/{id}', 'ApiProductController::show')->where_number('id');
$router->put('/api/products/{id}', 'ApiProductController::update')->where_number('id');
$router->patch('/api/products/{id}', 'ApiProductController::update')->where_number('id');
$router->delete('/api/products/{id}', 'ApiProductController::delete')->where_number('id');

// Preflight requests must reach the API library before token checks.
foreach (['/api/auth/login', '/api/auth/refresh', '/api/auth/logout', '/api/auth/me'] as $path) {
    $router->options($path, 'ApiAuthController::me');
}
$router->options('/api/products', 'ApiProductController::index');
$router->options('/api/products/{id}', 'ApiProductController::show')->where_number('id');

// Companion migration exercise. The controller blocks these routes on production web requests.
$router->get('/create-migration/{migration_class}', 'MigrationController::create_migration');
$router->get('/migrate', 'MigrationController::migrate');
$router->get('/rollback', 'MigrationController::rollback');
$router->get('/rollback-all', 'MigrationController::rollback_all');
$router->get('/refresh', 'MigrationController::refresh');
$router->get('/status', 'MigrationController::status');

<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

function product_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function product_redirect($path) {
    header('Location: ' . site_url($path), true, 303);
    exit;
}

function product_csrf_token() {
    if (empty($_SESSION['product_csrf'])) {
        $_SESSION['product_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['product_csrf'];
}

function product_csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . product_csrf_token() . '">';
}

function product_verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(product_csrf_token(), $token)) {
        http_response_code(403);
        exit('Your form has expired. Go back, reload the page, and try again.');
    }
}

function product_input($key) {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function product_require_login() {
    if (empty($_SESSION['product_user'])) {
        product_redirect('login');
    }
}

<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper(['url', 'product']);
        header('Cache-Control: no-store');
    }

    public function index() {
        product_redirect(empty($_SESSION['product_user']) ? 'login' : 'products');
    }

    public function login() {
        if (!empty($_SESSION['product_user'])) {
            product_redirect('products');
        }
        $error = '';
        $email = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            product_verify_csrf();
            $email = product_input('email');
            $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
            $expectedEmail = getenv('ADMIN_EMAIL') ?: '';
            $hash = getenv('ADMIN_PASSWORD_HASH') ?: '';
            if (!$expectedEmail || !$hash) {
                http_response_code(503);
                $error = 'Sign-in is not configured yet. Please contact the administrator.';
            } elseif (password_verify($password, $hash) && hash_equals(strtolower($expectedEmail), strtolower($email))) {
                $this->session->regenerate_on_login(true);
                $this->session->reset_attempts();
                $_SESSION['product_user'] = $expectedEmail;
                $_SESSION['product_csrf'] = bin2hex(random_bytes(32));
                product_redirect('products');
            } else {
                $this->session->security_log_attempt($_SERVER['REMOTE_ADDR'] ?? 'unknown', $this->session->generate_fingerprint(), 'Failed login');
                http_response_code(422);
                $error = 'The email or password is incorrect.';
            }
        }
        $this->call->view('auth/login', compact('error', 'email'));
    }

    public function logout() {
        product_verify_csrf();
        $this->session->sess_destroy();
        product_redirect('login');
    }
}

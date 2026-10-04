<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiAuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('ApiUserModel');
        $this->call->library('api');
        header('Cache-Control: no-store');
    }

    public function login()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit('api-login:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 10, 300);

        $body = $this->api->body();
        $email = strtolower(trim(html_entity_decode((string) ($body['email'] ?? ''), ENT_QUOTES, 'UTF-8')));
        $password = html_entity_decode((string) ($body['password'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $this->api->respond_error('Enter a valid email and password.', 422);
        }

        $user = $this->ApiUserModel->find_by_email($email);
        if (!$user || !(bool) $user['is_active'] || !password_verify($password, $user['password'])) {
            $this->api->respond_error('The email or password is incorrect.', 401);
        }

        $tokens = $this->api->issue_tokens([
            'id' => (int) $user['id'],
            'role' => $user['role'],
            'scopes' => ['read', 'write', 'delete'],
        ]);

        $this->api->respond([
            'message' => 'Signed in successfully.',
            'user' => $this->public_user($user),
            'tokens' => $tokens,
        ]);
    }

    public function me()
    {
        $payload = $this->api->require_jwt();
        $user = $this->ApiUserModel->find((int) $payload['sub']);
        if (!$user || !(bool) $user['is_active']) {
            $this->api->respond_error('Unauthorized', 401);
        }
        $this->api->respond(['user' => $this->public_user($user)]);
    }

    public function refresh()
    {
        $this->api->require_method('POST');
        $body = $this->api->body();
        $refreshToken = html_entity_decode((string) ($body['refresh_token'] ?? ''), ENT_QUOTES, 'UTF-8');
        if ($refreshToken === '') {
            $this->api->respond_error('Refresh token is required.', 422);
        }
        $this->api->refresh_access_token($refreshToken);
    }

    public function logout()
    {
        $this->api->require_method('POST');
        $this->api->require_jwt();
        $body = $this->api->body();
        $refreshToken = html_entity_decode((string) ($body['refresh_token'] ?? ''), ENT_QUOTES, 'UTF-8');
        if ($refreshToken !== '') {
            $this->api->revoke_refresh_token($refreshToken);
        }
        $this->api->respond(['message' => 'Signed out successfully.']);
    }

    private function public_user(array $user): array
    {
        return [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }
}

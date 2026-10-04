<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$appKey = getenv('APP_KEY') ?: 'local-development-key-change-before-deploying';
$appUrl = rtrim(getenv('APP_URL') ?: 'http://localhost:3000', '/');

$config['api_helper_enabled'] = TRUE;
$config['payload_token_expiration'] = 900;
$config['refresh_token_expiration'] = 604800;
$config['jwt_secret'] = getenv('JWT_SECRET') ?: hash('sha256', $appKey . ':jwt');
$config['refresh_token_key'] = getenv('REFRESH_TOKEN_KEY') ?: hash('sha256', $appKey . ':refresh');
$config['allow_origin'] = getenv('FRONTEND_ORIGIN') ?: $appUrl;
$config['refresh_token_table'] = 'refresh_tokens';
$config['jwt_issuer'] = $appUrl;
$config['jwt_audience'] = $appUrl . '/app';
$config['rate_limit_enabled'] = true;
$config['rate_limit_requests'] = 120;
$config['rate_limit_seconds'] = 60;

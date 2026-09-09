<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
 foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
  $line = trim($line);
  if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
  [$key, $value] = explode('=', $line, 2);
  $key = trim($key); $value = trim($value);
  if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key) || getenv($key) !== false) continue;
  if (strlen($value) >= 2 && in_array($value[0], ['"', "'"], true) && $value[-1] === $value[0]) $value = substr($value, 1, -1);
  putenv($key . '=' . $value);
 }
}

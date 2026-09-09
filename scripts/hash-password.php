<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
fwrite(STDERR, "Enter a password (terminal input may be visible): ");
$password = rtrim(fgets(STDIN), "\r\n");
if (strlen($password) < 12 || strlen($password) > 72) { fwrite(STDERR, "Use 12 to 72 bytes.\n"); exit(1); }
echo password_hash($password, PASSWORD_DEFAULT) . PHP_EOL;

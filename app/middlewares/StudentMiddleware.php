<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentMiddleware
{
    private const ACCESS_CODE = 'ABDON-F2-2026';

    public function handle(Closure $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $providedCode = (string) ($_GET['access_code'] ?? '');

        if ($providedCode !== '' && hash_equals(self::ACCESS_CODE, $providedCode)) {
            $_SESSION['student_access'] = true;
        }

        if (($_SESSION['student_access'] ?? false) === true) {
            return $next();
        }

        redirect('student?notice=profile-protected');
        exit;
    }
}

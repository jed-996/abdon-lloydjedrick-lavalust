<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthMiddleware {
    public function handle(Closure $next) {
        lava_instance()->call->library('session');
        lava_instance()->call->helper(['url', 'product']);
        product_require_login();
        return $next();
    }
}

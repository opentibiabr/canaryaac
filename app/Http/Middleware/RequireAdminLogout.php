<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Http\Middleware;

use App\Session\Admin\Login as SessionAdminLogin;

class RequireAdminLogout
{
    public static function handle($request, $next)
    {
        if(SessionAdminLogin::isLogged()) {
            $request->getRouter()->redirect('/admin/home');
        }

        return $next($request);
    }
}

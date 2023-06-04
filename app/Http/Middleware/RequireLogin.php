<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Http\Middleware;

use App\Session\Admin\Login as SessionPlayerLogin;

class RequireLogin
{
    public static function handle($request, $next)
    {
        if(!SessionPlayerLogin::isLogged()) {
            $request->getRouter()->redirect('/account/login');
        }
        return $next($request);
    }
}

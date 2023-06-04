<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Http\Middleware;

use App\Model\Entity\Account;
use App\Session\Admin\Login as SessionAdminLogin;

class RolePermission
{
    public static function handle($request, $next)
    {
        $select_account = Account::getAccount('id = "'.SessionAdminLogin::idLogged().'"', null, 1, 'page_access')->fetchObject();
        if($select_account->page_access == 0) {
            $request->getRouter()->redirect('/admin/logout');
        }
        return $next($request);
    }
}

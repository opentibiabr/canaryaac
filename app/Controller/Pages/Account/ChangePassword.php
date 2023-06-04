<?php
/**
 * ChangePassword Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\Account as EntityAccount;
use App\Session\Admin\Login as SessionAdminLogin;

class ChangePassword extends Base
{
    public static function updatePassword($request)
    {
        $postVars = $request->getPostVars();

        $newpassword = $postVars['newpassword'];
        $filter_newpassword = filter_var($newpassword, FILTER_SANITIZE_SPECIAL_CHARS);
        $convert_newpassword = sha1($filter_newpassword);

        $old_password = $postVars['oldpassword'];
        $filter_oldpassword = filter_var($old_password, FILTER_SANITIZE_SPECIAL_CHARS);
        $convert_oldpassword = sha1($filter_oldpassword);

        if(SessionAdminLogin::isLogged() == true) {
            return self::viewChangePassword($request, 'You are not logged in.');
        }
        if(empty($newpassword)) {
            return self::viewChangePassword($request);
        }
        if(empty($old_password)) {
            return self::viewChangePassword($request);
        }
        $AccountId = SessionAdminLogin::idLogged();
        $account = EntityPlayer::getAccount('id = "'.$AccountId.'"')->fetchObject();
        if ($account->password != $convert_oldpassword) {
            return self::viewChangePassword($request, 'Invalid password.');
        }
        if($account->password == $convert_oldpassword) {
            EntityAccount::updateAccount('id = "'.$account->id.'"', [
                'password' => $convert_newpassword,
            ]);
            $request->getRouter()->redirect('/account/logout');
        }
        return self::viewChangePassword($request);
    }

    public static function viewChangePassword($request, $status = null)
    {
        $content = View::render('pages/account/changepassword', [
            'status' => $status,
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }
}

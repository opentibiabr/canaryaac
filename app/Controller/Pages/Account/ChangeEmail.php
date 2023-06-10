<?php
/**
 * ChangeEmail Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use \App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\Account as EntityAccount;
use App\Session\Admin\Login as SessionAdminLogin;

class ChangeEmail extends Base{

    public static function updateEmail($request)
    {
        $postVars = $request->getPostVars();
        
        $newemail = $postVars['email'];
        $filter_newemail = filter_var($newemail, FILTER_SANITIZE_EMAIL);
        $password = $postVars['password'];
        $filter_password = filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
        $convert_password = sha1($password);

        if(SessionAdminLogin::isLogged() == false){
            return self::viewChangeEmail($request);
        }
        if(!filter_var($newemail, FILTER_VALIDATE_EMAIL)){
            return self::viewChangeEmail($request);
        }
        if(empty($password)){
            return self::viewChangeEmail($request);
        }
        $AccountId = SessionAdminLogin::idLogged();
        $account = EntityPlayer::getAccount([ 'id' => $AccountId])->fetchObject();
        
        $duplicateEmail = EntityPlayer::getAccount([ 'email' => $newemail])->fetchObject();
        if($duplicateEmail == true){
            return self::viewChangeEmail($request);
        }
        if($account->password == $convert_password){
            EntityAccount::updateAccount([ 'id' => $account->id], [
                'email' => $filter_newemail,
            ]);
            $request->getRouter()->redirect('/account/logout');
        }
        return self::viewChangeEmail($request);
    }

    public static function viewChangeEmail($request)
    {
        $content = View::render('pages/account/changeemail', []);
        return parent::getBase('Account Management', $content, 'account');
    }

}
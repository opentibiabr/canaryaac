<?php
/**
 * Lost Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Controller\Pages\Base;
use App\Utils\View;
use App\Model\Entity\Account as EntityAccount;
use App\Utils\Argon;

class Lost extends Base{

    public static function viewRecoveryKey($request)
    {
        $postVars = $request->getPostVars();
        if(empty($postVars['key1']) or empty($postVars['key2']) or empty($postVars['key3']) or empty($postVars['key4'])){
            $request->getRouter()->redirect('/account/lostaccount');
        }
        $filter_key1 = filter_var($postVars['key1'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_key2 = filter_var($postVars['key2'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_key3 = filter_var($postVars['key3'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_key4 = filter_var($postVars['key4'], FILTER_SANITIZE_SPECIAL_CHARS);
        $recoverykey = $filter_key1 . '-' . $filter_key2 . '-' . $filter_key3 . '-' . $filter_key4;

        if (empty($postVars['email'])) {
            $request->getRouter()->redirect('/account/lostaccount');
        }
        $filter_email = filter_var($postVars['email'], FILTER_SANITIZE_SPECIAL_CHARS);
        $account = EntityAccount::getAccount([ 'email' => $filter_email])->fetchObject();
        if($account == false){
            $request->getRouter()->redirect('/account/lostaccount');
        }

        if (empty($postVars['newpassword'])) {
            $request->getRouter()->redirect('/account/lostaccount');
        }
        if (empty($postVars['newpasswordconfirm'])) {
            $request->getRouter()->redirect('/account/lostaccount');
        }
        $filter_password = filter_var($postVars['newpassword'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_passwordconfirm = filter_var($postVars['newpasswordconfirm'], FILTER_SANITIZE_SPECIAL_CHARS);
        if ($filter_password != $filter_passwordconfirm) {
            $request->getRouter()->redirect('/account/lostaccount');
        }
        $new_password = Argon::generateArgonPassword($filter_password);

        $account_recoverykey = EntityAccount::getAccountRegistration(['account_id' => $account->id])->fetchObject();
        if($account_recoverykey->recovery == $recoverykey){
            EntityAccount::updateAccount([ 'email' => $filter_email], [
                'password' => $new_password
            ]);
            $request->getRouter()->redirect('/account/login');
        }
        $request->getRouter()->redirect('/account/lostaccount');
    }

    public static function selectAccount($request)
    {
        $postVars = $request->getPostVars();
        $email = $postVars['email'];
        $filter_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!filter_var($filter_email, FILTER_VALIDATE_EMAIL)){
            return self::getLostAccount($request);
        }

        $account = EntityAccount::getAccount([ 'email' => $filter_email])->fetchObject();
        if($account == false){
            return self::getLostAccount($request);
        }
        
        $content = View::render('pages/account/lostaccount_first', [
            'email' => $account->email,
        ]);
        return parent::getBase('Lost Account', $content, 'lostaccount');
    }

    public static function getLostAccount($request)
    {
        $content = View::render('pages/account/lostaccount', [
        ]);
        return parent::getBase('Lost Account', $content, 'lostaccount');
    }

}
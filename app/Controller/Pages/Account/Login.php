<?php
/**
 * Login Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Utils\View;
use App\Http\Request;
use App\Controller\Pages\Base;
use App\Model\Entity\Login as EntityLogin;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Controller\Admin\Alert;
use App\Model\Entity\Account;
use PragmaRX\Google2FA\Google2FA;

class Login extends Base{

    /**
     * Method responsible for returning the login page rendering
     *
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request, $errorMessage = null)
    {
        // Login status
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        // Render login page and $status
        $content = View::render('pages/account/login', [
            'status' => $status
        ]);

        return parent::getBase('Account Management', $content, 'account');
    }

    /**
     * Method responsible for setting user login
     *
     * @param Request $request
     */
    public static function setLogin($request)
    {
        $postVars = $request->getPostVars();
        $email = $postVars['loginemail'] ?? '';
        $pass = $postVars['loginpassword'] ?? '';

        $filter_email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if($filter_email == false){
            return self::getLogin($request, 'true');
        }

        // Verify email
        $obAccount = EntityLogin::getLoginbyEmail($email);
        if(!$obAccount instanceof EntityLogin){
            return self::getLogin($request, 'true');
        }

        // Password verify by sha1
        if($obAccount->password !== sha1($pass)){
            return self::getLogin($request, 'true');
        }

        $authentication = Account::getAuthentication([ 'account_id' => $obAccount->id])->fetchObject();
        if (!empty($authentication)) {
            if ($authentication->status == 1) {
                if (empty($postVars['token'])) {
                    return self::getLogin($request, 'true');
                }
                $google2fa = new Google2FA();
                $auth = $google2fa->verifyKey($authentication->secret, $postVars['token']);
                if ($auth != 1) {
                    return self::getLogin($request, 'true');
                }
            }
        }
        
        SessionAdminLogin::login($obAccount);
        $request->getRouter()->redirect('/account');
    }

    public static function setLogout($request)
    {
        SessionAdminLogin::logout();
        $content = View::render('pages/account/logout', []);
        return parent::getBase('Logout Successful', $content, 'account');
    }

}
<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\Login as EntityLogin;
use App\Utils\View;
use App\Http\Request;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Controller\Admin\Alert;

class Login extends Base
{
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
        return $content = View::render('admin/login', [
            'title' => 'Login - CanaryAAC',
            'status' => $status
        ]);

        //return parent::getPanel('Login', $content, 'home');
    }

    /**
     * Method responsible for setting user login
     *
     * @param Request $request
     */
    public static function setLogin($request)
    {
        $postVars = $request->getPostVars();
        $email = $postVars['login-email'] ?? '';
        $pass = $postVars['login-password'] ?? '';

        $obAccount = EntityLogin::getLoginbyEmail($email);

        // Verify email
        if (!$obAccount instanceof EntityLogin) {
            return self::getLogin($request, 'Email ou password inválidos.');
        }

        // Password verify by sha1
        if ($obAccount->password !== sha1($pass)) {
            return self::getLogin($request, 'Email ou password inválidos.');
        }

        // Verify account access
        if (!($obAccount->page_access > 0)) {
            return self::getLogin($request, 'Você não tem acesso.');
        }

        /*
        // Password verify by hash
        if(!password_verify($pass, $obAccount->password)){
            return self::getLogin($request, 'Email ou password inválidos.');
        }
        */

        SessionAdminLogin::login($obAccount);

        $request->getRouter()->redirect('/admin');
    }

    public static function setLogout($request)
    {
        SessionAdminLogin::logout();

        $request->getRouter()->redirect('/admin/login');
    }
}

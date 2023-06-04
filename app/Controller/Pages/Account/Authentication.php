<?php
/**
 * Authentication Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Account;
use App\Model\Entity\ServerConfig;
use PragmaRX\Google2FA\Google2FA;
use App\Session\Admin\Login as SessionAdminLogin;

class Authentication extends Base
{
    public static function getQRcode($secretKey)
    {
        $google2fa = new Google2FA();
        $LoggedId = SessionAdminLogin::idLogged();

        $account = Account::getAccount('id = "' . $LoggedId . '"')->fetchObject();
        $website = ServerConfig::getInfoWebsite()->fetchObject();


        $companyName = $website->title;
        $companyEmail = $account->email;

        $qrCodeUrl = $google2fa->getQRCodeUrl($companyName, $companyEmail, $secretKey);
        $url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=15&data=' . $qrCodeUrl;
        return $url;
    }

    public static function createSecretKey()
    {
        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        return $secretKey;
    }

    public static function viewConfirmConnect($request)
    {
        $LoggedId = SessionAdminLogin::idLogged();
        $google2fa = new Google2FA();
        $postVars = $request->getPostVars();
        $authentication = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();
        $secretKey = $authentication->secret;
        $filter_token = filter_var($postVars['verificationcode'], FILTER_SANITIZE_SPECIAL_CHARS);

        if ($authentication->status == 1) {
            $request->getRouter()->redirect('/account/manage');
        }
        if (empty($filter_token)) {
            $request->getRouter()->redirect('/account/authentication');
        }

        $valid = $google2fa->verifyKey($secretKey, $filter_token);

        if ($google2fa->verifyKey($secretKey, $filter_token)) {
            Account::updateAuthentication('account_id = "' . $LoggedId . '"', [
                'status' => 1,
            ]);
            $content = View::render('pages/account/authentication_finish', [
                'valid' => $valid,
            ]);
            return parent::getBase('Account Authentication', $content, 'account');
        } else {
            $request->getRouter()->redirect('/account/authentication');
        }
    }

    public static function viewConnectAuthentication($request)
    {
        $LoggedId = SessionAdminLogin::idLogged();
        $postVars = $request->getPostVars();
        $filter_confirmationkey = filter_var($postVars['confirmationkey'], FILTER_SANITIZE_SPECIAL_CHARS);

        $authentication = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();
        if ($authentication->secret != $filter_confirmationkey) {
            $request->getRouter()->redirect('/account/authentication');
        }
        if ($authentication->status == 1) {
            $request->getRouter()->redirect('/account/manage');
        }

        $content = View::render('pages/account/authentication_connect', [
            'secret' => $authentication->secret,
            'qrcode' => self::getQRcode($authentication->secret),
        ]);
        return parent::getBase('Account Authentication', $content, 'account');
    }

    public static function viewConfirmAuthentication($request)
    {
        $LoggedId = SessionAdminLogin::idLogged();
        $postVars = $request->getPostVars();
        $secretKey = self::createSecretKey();

        $filter_confirm = filter_var($postVars['confirm'], FILTER_SANITIZE_NUMBER_INT);
        if ($filter_confirm != 1) {
            $request->getRouter()->redirect('/account/authentication');
        }

        $authentication = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();
        if (empty($authentication)) {
            Account::insertAuthentication([
                'account_id' => $LoggedId,
                'secret' => $secretKey,
            ]);
        }
        if (!empty($authentication)) {
            if ($authentication->status == 1) {
                $request->getRouter()->redirect('/account/manage');
            }
        }

        $authentication_secret = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();

        $content = View::render('pages/account/authentication_confirm', [
            'secretkey' => $authentication_secret->secret,
        ]);
        return parent::getBase('Account Authentication', $content, 'account');
    }

    public static function viewUnlinkAuthentication($request)
    {
        $content = View::render('pages/account/authentication_unlink', []);
        return parent::getBase('Account Authentication', $content, 'account');
    }

    public static function viewUnlinkConfirmToken($request)
    {
        $LoggedId = SessionAdminLogin::idLogged();
        $postVars = $request->getPostVars();

        $authentication = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();
        if ($authentication->status != 1) {
            $request->getRouter()->redirect('/account/manage');
        }

        if (empty($postVars['token'])) {
            $request->getRouter()->redirect('/account/manage');
        }
        if (!filter_var($postVars['token'], FILTER_VALIDATE_INT)) {
            $request->getRouter()->redirect('/account/manage');
        }
        $filter_token = filter_var($postVars['token'], FILTER_SANITIZE_NUMBER_INT);

        $google2fa = new Google2FA();
        if ($google2fa->verifyKey($authentication->secret, $filter_token)) {
            Account::deleteAuthentication('account_id = "' . $LoggedId . '"');
        }

        $content = View::render('pages/account/authentication_unlinkconfirm', []);
        return parent::getBase('Account Authentication', $content, 'account');
    }

    public static function viewUnlinkbyRecoveryKey($request)
    {
        $LoggedId = SessionAdminLogin::idLogged();
        $authentication = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();
        if ($authentication->status != 1) {
            $request->getRouter()->redirect('/account/manage');
        }

        $content = View::render('pages/account/authentication_unlinkrecoverykey', []);
        return parent::getBase('Account Authentication', $content, 'account');
    }

    public static function viewUnlinkbyRecoveryKeyConfirm($request)
    {
        $postVars = $request->getPostVars();
        $LoggedId = SessionAdminLogin::idLogged();
        $authentication = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();
        if ($authentication->status != 1) {
            $request->getRouter()->redirect('/account/manage');
        }
        if (empty($postVars['key1']) or empty($postVars['key2']) or empty($postVars['key3']) or empty($postVars['key4'])) {
            $request->getRouter()->redirect('/account/manage');
        }
        $filter_key1 = filter_var($postVars['key1'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_key2 = filter_var($postVars['key2'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_key3 = filter_var($postVars['key3'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_key4 = filter_var($postVars['key4'], FILTER_SANITIZE_SPECIAL_CHARS);
        $recoverykey = $filter_key1 . '-' . $filter_key2 . '-' . $filter_key3 . '-' . $filter_key4;
        $account_recoverykey = Account::getAccountRegistration('account_id = "' . $LoggedId . '"')->fetchObject();
        if ($account_recoverykey->recovery == $recoverykey) {
            Account::deleteAuthentication('account_id = "' . $LoggedId . '"');
        }

        $content = View::render('pages/account/authentication_unlinkconfirm', [
            'recovery' => $recoverykey,
            'recoveryacc' => $account_recoverykey->recovery,
        ]);
        return parent::getBase('Account Authentication', $content, 'account');
    }

    public static function viewAuthentication($request)
    {
        $LoggedId = SessionAdminLogin::idLogged();
        $authentication = Account::getAuthentication('account_id = "' . $LoggedId . '"')->fetchObject();
        if (!empty($authentication)) {
            if ($authentication->status == 1) {
                $request->getRouter()->redirect('/account/manage');
            }
        }

        $content = View::render('pages/account/authentication', []);
        return parent::getBase('Account Authentication', $content, 'account');
    }
}

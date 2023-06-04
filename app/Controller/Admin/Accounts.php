<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Utils\View;
use App\Controller\Admin\Alert;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\Account as EntityAccount;
use App\Model\Functions\Player;

class Accounts extends Base
{
    public static function updateAccount($request, $id)
    {
        $postVars = $request->getPostVars();

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $status = Alert::getError('Invalid account ID.');
            return self::viewAccount($request, $id, $status);
        }
        $filter_account_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        if (empty($postVars['account_email'])) {
            $status = Alert::getError('You need to enter an email.');
            return self::viewAccount($request, $id, $status);
        }
        if (!filter_var($postVars['account_email'], FILTER_VALIDATE_EMAIL)) {
            $status = Alert::getError('Invalid email format.');
            return self::viewAccount($request, $id, $status);
        }
        $filter_account_email = filter_var($postVars['account_email'], FILTER_SANITIZE_EMAIL);

        if (empty($postVars['account_pageaccess'])) {
            $status = Alert::getError('Invalid page access number.');
            return self::viewAccount($request, $id, $status);
        }
        $filter_account_access = filter_var($postVars['account_pageaccess'], FILTER_SANITIZE_NUMBER_INT);

        if (empty($postVars['account_type'])) {
            $status = Alert::getError('Invalid type number.');
            return self::viewAccount($request, $id, $status);
        }
        $filter_account_type = filter_var($postVars['account_type'], FILTER_SANITIZE_NUMBER_INT);

        if (!isset($postVars['account_premdays'])) {
            $status = Alert::getError('Invalid number of premium days.');
            return self::viewAccount($request, $id, $status);
        }
        $filter_account_premdays = filter_var($postVars['account_premdays'], FILTER_SANITIZE_NUMBER_INT);

        if (!isset($postVars['account_coins'])) {
            $status = Alert::getError('Invalid number of coins.');
            return self::viewAccount($request, $id, $status);
        }
        $filter_account_coins = filter_var($postVars['account_coins'], FILTER_SANITIZE_NUMBER_INT);

        if (isset($postVars['account_password'])) {
            $filter_account_password = filter_var($postVars['account_password'], FILTER_SANITIZE_SPECIAL_CHARS);
            $convert_password = sha1($filter_account_password);
        }

        if (empty($postVars['account_password'])) {
            EntityAccount::updateAccount('id = "' . $filter_account_id . '"', [
                'email' => $filter_account_email,
                'page_access' => $filter_account_access,
                'premdays' => $filter_account_premdays,
                'coins' => $filter_account_coins,
            ]);
        } else {
            EntityAccount::updateAccount('id = "' . $filter_account_id . '"', [
                'email' => $filter_account_email,
                'password' => $convert_password,
                'page_access' => $filter_account_access,
                'premdays' => $filter_account_premdays,
                'coins' => $filter_account_coins,
            ]);
        }
        $status = Alert::getSuccess('Account successfully updated.');
        return self::viewAccount($request, $id, $status);
    }

    public static function getAllAccounts()
    {
        $select = EntityPlayer::getAccount();
        while ($obAccounts = $select->fetchObject()) {
            $allAccounts[] = [
                'id' => $obAccounts->id,
                'email' => $obAccounts->email,
                'page_access' => $obAccounts->page_access,
                'coins' => $obAccounts->coins,
                'creation' => $obAccounts->creation,
                'premdays' => $obAccounts->premdays,
                'premium' => Player::convertPremy($obAccounts->id)
            ];
        }
        return $allAccounts;
    }

    public static function getAccountById($account_id)
    {
        $select_account = EntityPlayer::getAccount('id = "' . $account_id . '"')->fetchObject();
        return [
            'email' => $select_account->email,
            'page_access' => $select_account->page_access,
            'type' => $select_account->type,
            'premdays' => $select_account->premdays,
            'coins' => $select_account->coins,
        ];
    }

    public static function viewAccount($request, $id, $status = null)
    {
        $content = View::render('admin/modules/accounts/view', [
            'status' => $status,
            'account_id' => $id,
            'account' => self::getAccountById($id),
            'characters' => Player::getAllCharacters($id),
        ]);
        return parent::getPanel('Account #' . $id, $content, 'accounts');
    }

    public static function getAccounts($request)
    {
        $content = View::render('admin/modules/accounts/index', [
            'accounts' => self::getAllAccounts()
        ]);
        return parent::getPanel('Accounts', $content, 'accounts');
    }

    public function getStatus($request)
    {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status'])) {
            return '';
        }

        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Account criada com sucesso!');
            case 'updated':
                return Alert::getSuccess('Account atualizada com sucesso!');
            case 'deleted':
                return Alert::getSuccess('Account excluída com sucesso!');
            case 'duplicated':
                return Alert::getError('O email digitado já está sendo utilizado.');
        }
    }
}

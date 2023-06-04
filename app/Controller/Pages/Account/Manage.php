<?php
/**
 * Manage Class
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
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Functions\Player;
use App\Model\Entity\Badges as EntityBadges;
use App\Session\Admin\Login as SessionAdminLogin;

class Manage extends Base
{
    public static function getRegistration($account_id)
    {
        $registration = [];
        $selectRegistration = EntityAccount::getAccountRegistration('account_id = "'.$account_id.'"')->fetchObject();
        if($selectRegistration == true) {
            $registration = [
                'status' => true,
                'recovery' => $selectRegistration->recovery,
                'firstname' => $selectRegistration->firstname,
                'lastname' => $selectRegistration->lastname,
                'address' => $selectRegistration->address,
                'housenumber' => $selectRegistration->housenumber,
                'additional' => $selectRegistration->additional,
                'postalcode' => $selectRegistration->postalcode,
                'city' => $selectRegistration->city,
                'country' => $selectRegistration->country,
                'state' => $selectRegistration->state,
                'mobile' => $selectRegistration->mobile,
            ];
        } else {
            $registration = [
                'status' => false,
            ];
        }
        return $registration;
    }

    public static function getBadges($account_id)
    {
        $playerBadges = [
            'status' => false,
        ];
        $selectPlayerBadges = EntityBadges::getPlayerBadges('account_id = "'.$account_id.'"', '', '5');
        while($badges = $selectPlayerBadges->fetchObject()) {
            $selectServerBadges = EntityBadges::getServerBadges('id = "'.$badges->badge_id.'"');
            while($badges = $selectServerBadges->fetchObject()) {
                $playerBadges[] = [
                    'status' => true,
                    'image' => $badges->image,
                    'name' => $badges->name,
                    'description' => $badges->description,
                    'number' => $badges->number,
                ];
            }
        }
        return $playerBadges;
    }

    public static function getAccountLogged()
    {
        $logged = [];
        if(SessionAdminLogin::isLogged() == true) {
            $admin = SessionAdminLogin::idLogged();

            $account = EntityPlayer::getAccount('id = "'.$admin.'"')->fetchObject();
            $playerMain = EntityPlayer::getPlayer('account_id = "'.$account->id.'" AND main = "1"')->fetchObject();
            $playerNoMain = EntityPlayer::getPlayer('account_id = "'.$account->id.'" AND main = "0"');

            $datePrem = date('d F Y H:i', strtotime('+'.$account->premdays.' days'));
            $textPrem = Player::convertPremy($account->id);
            if($account->premdays > 0) {
                $colorPrem = 'green';
            } else {
                $colorPrem = 'red';
            }
            $created = date('d F Y, H:i', strtotime($account->creation));

            $playersInAccount = EntityPlayer::getPlayer('account_id = "'.$account->id.'"');
            while($playersAccount = $playersInAccount->fetchObject()) {
                $players[] = [
                    'name' => $playersAccount->name,
                    'main' => $playersAccount->main,
                ];
            }

            $authentication = EntityAccount::getAuthentication('account_id = "'.$admin.'"')->fetchObject();
            if(empty($authentication)) {
                $authentication = 0;
            } else {
                $authentication = $authentication->status;
            }

            $logged = [
                'id' => $account->id,
                'name' => $account->name,
                'email' => $account->email,
                'premdays' => $account->premdays,
                'textprem' => $textPrem,
                'colorprem' => $colorPrem,
                'dateprem' => $datePrem,
                'coins' => $account->coins,
                'creation' => $created,
                'badges' => self::getBadges($account->id),
                'players' => $players,
                'registration' => self::getRegistration($account->id),
                'authenticator' => $authentication,
            ];
        }
        return $logged;
    }

    public static function getAccount($request)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();

        $content = View::render('pages/account/manage', [
            'account' => self::getAccountLogged(),
            'active_donates' => $websiteInfo->donates,
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }
}

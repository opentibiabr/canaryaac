<?php
/**
 * Index Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Account;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\Worlds as EntityWorlds;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Functions\Player;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Model\Entity\Bans as EntityBans;

class Index extends Base
{
    public static function getWorlds($world_id)
    {
        $arrayAllWorlds = [];
        $world = EntityWorlds::getWorlds('id = "'.$world_id.'"')->fetchObject();
        if($world == true) {
            $arrayAllWorlds = [
                'id' => $world->id,
                'name' => $world->name,
                'location' => $world->location,
                'pvp_type' => $world->pvp_type,
                'premium_type' => $world->premium_type,
                'transfer_type' => $world->transfer_type,
                'battle_eye' => $world->battle_eye,
                'world_type' => $world->world_type
            ];
        }
        return $arrayAllWorlds;
    }

    public static function getAccountLogged()
    {
        $logged = [];
        if(SessionAdminLogin::isLogged() == true) {
            $admin = SessionAdminLogin::idLogged();

            $account = EntityPlayer::getAccount('id = "'.$admin.'"')->fetchObject();
            $playerMain = EntityPlayer::getPlayer('account_id = "'.$account->id.'" AND main = "1"')->fetchObject();
            $playerNoMain = EntityPlayer::getPlayer('account_id = "'.$account->id.'" AND main = "0"');

            $accountRegistred = Account::getAccountRegistration('account_id = "'.$admin.'"')->fetchObject();
            if(empty($accountRegistred)) {
                $registered = false;
            } else {
                $registered = true;
            }

            $datePrem = date('d F Y H:i:s', strtotime('+'.$account->premdays.' days'));

            $select_account_banned = EntityBans::getAccountBans('account_id = "'.$admin.'"')->fetchObject();
            if(empty($select_account_banned)) {
                $account_banned = false;
            } else {
                $account_banned = true;
                $ban_days_to_end = ($select_account_banned->expires_at - $select_account_banned->banned_at) / (60 * 60 * 24);
                $arrayBan = [
                    'date' => $select_account_banned->banned_at,
                    'reason' => $select_account_banned->reason,
                    'days_end' => $ban_days_to_end
                ];
            }

            $players = [];
            while($char = $playerNoMain->fetchObject()) {
                $players[] = [
                        'id' => $char->id,
                        'name' => $char->name,
                        'world' => self::getWorlds($char->world),
                        'level' => $char->level,
                        'outfit' => Player::getOutfit($char->id),
                        'vocation' => Player::convertVocation($char->vocation),
                        'online' => Player::isOnline($char->id),
                        'deletion' => $char->deletion,
                        'skull' => Player::getSkull($char->id),
                        'isreward' => $char->isreward,
                        'guild' => Player::getGuildMember($char->id),
                ];
            }

            $logged = [
                'id' => $account->id,
                'name' => $account->name,
                'email' => $account->email,
                'premdays' => $account->premdays,
                'dateprem' => $datePrem,
                'coins' => $account->coins,
                'registered' => $registered,
                'account_banned' => $account_banned,
                'page_access' => $account->page_access,
                'player' => [
                    'id' => $playerMain->id,
                    'name' => $playerMain->name,
                    'world' => self::getWorlds($playerMain->world),
                    'level' => $playerMain->level,
                    'outfit' => Player::getOutfit($playerMain->id),
                    'vocation' => Player::convertVocation($playerMain->vocation),
                    'group' => Player::convertGroup($playerMain->group_id),
                    'outfit' => Player::getOutfit($playerMain->id),
                    'main' => $playerMain->main,
                    'online' => Player::isOnline($playerMain->id),
                    'deletion' => $playerMain->deletion,
                    'isreward' => $playerMain->isreward,
                    'guild' => Player::getGuildMember($playerMain->id),
                ],
                'players' => $players,
                'ban_info' => $arrayBan ?? [],
            ];
        }
        return $logged;
    }

    public static function getAccount($request)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();

        $content = View::render('pages/account/index', [
            'account' => self::getAccountLogged(),
            'active_donates' => $websiteInfo->donates
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }

}

<?php
/**
 * DisbandGuild Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Guilds;

use App\Controller\Pages\Base;
use App\Utils\View;
use App\Model\Entity\Guilds as EntityGuilds;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Model\Functions\Guilds as FunctionGuilds;
use App\Model\Entity\Account as EntityAccount;

class DisbandGuild extends Base
{
    public static function convertGuildName($guild_name)
    {
        $decodeUrl = urldecode($guild_name);
        $filterName = filter_var($decodeUrl, FILTER_SANITIZE_SPECIAL_CHARS);
        $dbGuild = EntityGuilds::getGuilds('name = "'.$filterName.'"')->fetchObject();
        if($dbGuild == true) {
            $guild_id = $dbGuild->id;
        }
        return $guild_id ?? 0;
    }

    public static function deleteDisbandGuild($request, $name)
    {
        $isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
        if($isLeader == false) {
            $request->getRouter()->redirect('/community/guilds/'.$name.'/view');
        }

        $postVars = $request->getPostVars();
        $guild_id = self::convertGuildName($name);
        $idLogged = SessionAdminLogin::idLogged();

        if(empty($postVars['password'])) {
            $status = 'You need to enter your password.';
            self::viewDisbandGuild($request, $name, $status);
        }

        $filter_password = filter_var($postVars['password'], FILTER_SANITIZE_SPECIAL_CHARS);
        $dbAccount = EntityAccount::getAccount('id = "'.$idLogged.'" AND password = "'.$filter_password.'"');
        if(!empty($dbAccount)) {
            $status = 'Something went wrong.';
            self::viewDisbandGuild($request, $name, $status);
        }

        EntityGuilds::deleteGuild('id = "'.$guild_id.'"');
        $request->getRouter()->redirect('/community/guilds');
    }

    public static function viewDisbandGuild($request, $name, $status = null)
    {
        $isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
        if($isLeader == false) {
            $request->getRouter()->redirect('/community/guilds/'.$name.'/view');
        }

        $content = View::render('pages/guilds/disbandguild', [
            'status' => $status,
            'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
        ]);
        return parent::getBase('Guilds', $content, 'guilds');
    }
}

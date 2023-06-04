<?php
/**
 * ViewGuild Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Guilds;

use App\Controller\Pages\Base;
use \App\Utils\View;
use App\Model\Entity\Guilds as EntityGuilds;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Model\Functions\Guilds as FunctionGuilds;
use App\Model\Functions\Server as FunctionServer;
use App\Model\Entity\Worlds as EntityWorlds;

class ViewGuild extends Base{

	public static function convertGuildName($guild_name)
	{
		$decodeUrl = urldecode($guild_name);
		$filterName = filter_var($decodeUrl, FILTER_SANITIZE_SPECIAL_CHARS);
		$dbGuild = EntityGuilds::getGuilds([ 'name' => $filterName])->fetchObject();
		if($dbGuild == true){
			$guild_id = $dbGuild->id;
		}
		return $guild_id ?? 0;
	}

	public static function viewGuild($request,$name)
    {
        $content = View::render('pages/guilds/viewguild', [
            'worlds' => FunctionServer::getWorlds(),
            'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
            'members' => FunctionGuilds::getAllMembers(self::convertGuildName($name)),
            'invites' => FunctionGuilds::getGuildInvites(self::convertGuildName($name)),
			'isInvited' => FunctionGuilds::verifyAccountInvited(self::convertGuildName($name)),
			'isLogged' => SessionAdminLogin::isLogged(),
			'isLeader' => FunctionGuilds::verifyAccountLeader(self::convertGuildName($name)),
			'isViceLeader' => FunctionGuilds::verifyAccountViceLeader(self::convertGuildName($name)),
			'isMember' => FunctionGuilds::verifyAccountMember(self::convertGuildName($name)),
        ]);
        return parent::getBase('Guilds', $content, 'guilds');
    }

	public static function getWorld($request)
    {
        $queryParams = $request->getQueryParams();
        $currentWorld = $queryParams['world'] ?? '';
        $selectWorlds = EntityWorlds::getWorlds([ 'name' => $currentWorld])->fetchObject();
        if(empty($selectWorlds)){
            return null;
        }else{
            return $selectWorlds->name;
        }
    }

    public static function viewAllGuilds($request)
    {
		$queryParams = $request->getQueryParams();
		if (empty($queryParams['world'])) {
			$queryParams['world'] = '';
		}
		$filter_world = filter_var($queryParams['world'], FILTER_SANITIZE_SPECIAL_CHARS);
		$select_world = EntityWorlds::getWorlds([ 'name' => $filter_world])->fetchObject();
		if (empty($select_world)) {
			$select_world_id = 1;
		} else {
			$select_world_id = $select_world->id;
		}
		$content = View::render('pages/guilds/allguilds', [
			'worlds' => FunctionServer::getWorlds(),
			'select_world' => FunctionServer::getWorldById($select_world_id),
			'current_world' => self::getWorld($request),
			'guilds' => FunctionGuilds::getGuilds(),
			'guildsworld' => FunctionGuilds::getGuildbyWorldId($select_world_id),
		]);
		return parent::getBase('Guilds', $content, 'guilds');
    }

}

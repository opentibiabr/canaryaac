<?php
/**
 * JoinGuild Class
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
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Server as FunctionServer;

class JoinGuild extends Base{

	public static function convertGuildName($guild_name)
	{
		$decodeUrl = urldecode($guild_name);
		$filterName = filter_var($decodeUrl, FILTER_SANITIZE_SPECIAL_CHARS);
		$dbGuild = EntityGuilds::getGuilds('name = "'.$filterName.'"')->fetchObject();
		if($dbGuild == true){
			$guild_id = $dbGuild->id;
		}
		return $guild_id ?? 0;
	}

	public static function insertJoinGuild($request,$name)
	{
		$postVars = $request->getPostVars();
		$guild_id = self::convertGuildName($name);
		$idLogged = SessionAdminLogin::idLogged();

		if(empty($postVars['character_join'])){
			$status = 'Select a character.';
			return self::viewJoinGuild($request,$name,$status);
		}
		$character_name = filter_var($postVars['character_join'], FILTER_SANITIZE_SPECIAL_CHARS);
		$dbPlayer = EntityPlayer::getPlayer('name = "'.$character_name.'" AND account_id = "'.$idLogged.'"')->fetchObject();
		if(empty($dbPlayer)){
			$status = 'Invalid character.';
			return self::viewJoinGuild($request,$name,$status);
		}

		$dbMember = EntityGuilds::getMembership('player_id = "'.$dbPlayer->id.'"')->fetchObject();
		if(!empty($dbMember)){
			$status = 'This character is in a Guild.';
			return self::viewJoinGuild($request,$name,$status);
		}

		$dbApplication = EntityGuilds::getApplications('player_id = "'.$dbPlayer->id.'"')->fetchObject();
		if(!empty($dbApplication)){
			$status = 'This character is applied to another Guild.';
			return self::viewJoinGuild($request,$name,$status);
		}
		EntityGuilds::deleteInvite('player_id = "'.$dbPlayer->id.'" AND guild_id = "'.$guild_id.'"');

		$dbRanks = EntityGuilds::getRanks('guild_id = "'.$guild_id.'" AND level = 1')->fetchObject();

		EntityGuilds::insertJoinMember([
			'player_id' => $dbPlayer->id,
			'guild_id' => $guild_id,
			'rank_id' => $dbRanks->id, // MEMBER
			'date' => strtotime(date('d-m-Y H:i:s')),
		]);
		return self::viewJoinGuild($request,$name);
	}

	public static function viewJoinGuild($request,$name,$status = null)
    {
		$guild_id = self::convertGuildName($name);
		$idLogged = SessionAdminLogin::idLogged();
		$dbPlayersAccount = EntityPlayer::getPlayer('account_id = "'.$idLogged.'"');
		while($playersAccount = $dbPlayersAccount->fetchObject()){
			$dbInvitation = EntityGuilds::getInvites('player_id = "'.$playersAccount->id.'" AND guild_id = "'.$guild_id.'"');
			while($invitation = $dbInvitation->fetchObject()){
				$arrayInvitations[] = [
					'id' => $playersAccount->id,
					'name' => $playersAccount->name,
				];
			}
		}
		if(empty($arrayInvitations)){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}
		$content = View::render('pages/guilds/joinguild', [
			'status' => $status,
			'worlds' => FunctionServer::getWorlds(),
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
			'invitations' => $arrayInvitations,
		]);
		return parent::getBase('Guilds', $content, 'guilds');
    }

}

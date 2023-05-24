<?php
/**
 * InviteCharacter Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Guilds;

use App\Controller\Pages\Base;
use \App\Utils\View;
use App\Model\Entity\Guilds as EntityGuilds;
use App\Model\Functions\Guilds as FunctionGuilds;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Server as FunctionServer;

class InviteCharacter extends Base{

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

	public static function insertInviteCharacter($request,$name)
	{
		$postVars = $request->getPostVars();
		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}
		if(isset($postVars['btn_invite'])){
			if(empty($postVars['invite_name'])){
				$status_invite = 'Enter a character name.';
				return self::viewInviteCharacter($request,$name,$status_invite);
			}
			$input_name = filter_var($postVars['invite_name'], FILTER_SANITIZE_SPECIAL_CHARS);
			$dbPlayer = EntityPlayer::getPlayer('name = "'.$input_name.'"')->fetchObject();
			if(empty($dbPlayer)){
				$status_invite = 'Character does not exist.';
				return self::viewInviteCharacter($request,$name,$status_invite);
			}
			if($dbPlayer->deletion == 1){
				$status_invite = 'Character is deleted.';
				return self::viewInviteCharacter($request,$name,$status_invite);
			}
			$dbMembers = EntityGuilds::getMembership('player_id = "'.$dbPlayer->id.'"')->fetchObject();
			if(!empty($dbMembers)){
				$status_invite = 'This character already participates in a Guild.';
				return self::viewInviteCharacter($request,$name,$status_invite);
			}
			$guild_id = self::convertGuildName($name);
			$dbInvited = EntityGuilds::getInvites('player_id = "'.$dbPlayer->id.'" AND guild_id = "'.$guild_id.'"')->fetchObject();
			if(!empty($dbInvited)){
				$status_invite = 'This character is already invited.';
				return self::viewInviteCharacter($request,$name,$status_invite);
			}
			EntityGuilds::insertInvite([
				'player_id' => $dbPlayer->id,
				'guild_id' => $guild_id,
				'date' => strtotime(date('d-m-Y H:i:s')),
			]);
			$status_invite = 'Successfully invited character.';
			return self::viewInviteCharacter($request,$name,$status_invite);
		}

		if(isset($postVars['btn_cancel'])){
			if(empty($postVars['cancel_name'])){
				$status_cancelinvite = 'Select a character.';
				return self::viewInviteCharacter($request,$name,null,$status_cancelinvite);
			}
			$input_cancelname = filter_var($postVars['cancel_name'], FILTER_SANITIZE_SPECIAL_CHARS);
			$dbPlayer = EntityPlayer::getPlayer('name = "'.$input_cancelname.'"')->fetchObject();
			if(empty($dbPlayer)){
				$status_cancelinvite = 'Character does not exist.';
				return self::viewInviteCharacter($request,$name,null,$status_cancelinvite);
			}
			$guild_id = self::convertGuildName($name);
			$dbInvited = EntityGuilds::getInvites('player_id = "'.$dbPlayer->id.'" AND guild_id = "'.$guild_id.'"')->fetchObject();
			if(empty($dbInvited)){
				$status_cancelinvite = 'This character is not invited.';
				return self::viewInviteCharacter($request,$name,null,$status_cancelinvite);
			}
			EntityGuilds::deleteInvite('player_id = "'.$dbPlayer->id.'" AND guild_id = "'.$guild_id.'"');
			$status_cancelinvite = 'Successfully deleted.';
			return self::viewInviteCharacter($request,$name,null,$status_cancelinvite);
		}
	}

	public static function viewInviteCharacter($request,$name, $status_invite = null, $status_cancelinvite = null)
	{
		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}
		$content = View::render('pages/guilds/invitecharacter', [
			'status' => $status_invite,
			'status_cancelinvite' => $status_cancelinvite,
			'worlds' => FunctionServer::getWorlds(),
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
			'invites' => FunctionGuilds::getGuildInvites(self::convertGuildName($name)),
		]);
		return parent::getBase('Guild', $content, 'guilds');
	}

}

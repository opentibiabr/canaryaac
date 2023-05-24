<?php
/**
 * ApplyToThisGuild Class
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
use App\Model\Functions\Player as FunctionPlayer;

class ApplyToThisGuild extends Base{

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

	public static function insertApplyToThisGuild($request,$name)
	{
		$postVars = $request->getPostVars();
		$guild_id = self::convertGuildName($name);
		$idLogged = SessionAdminLogin::idLogged();
		if(isset($postVars['apply_btn'])){
			if(empty($postVars['apply_name'])){
				$status = 'You need to select a character.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}
			$input_character = filter_var($postVars['apply_name'], FILTER_SANITIZE_SPECIAL_CHARS);

			$dbPlayer = EntityPlayer::getPlayer('name = "'.$input_character.'" AND account_id = "'.$idLogged.'"')->fetchObject();
			if(empty($dbPlayer)){
				$status = 'This character does not belong to the logged in account.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}
			if($dbPlayer->deletion == 1){
				$status = 'This character is deleted.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}

			$dbApplications = EntityGuilds::getApplications('player_id = "'.$dbPlayer->id.'"')->fetchObject();
			if(!empty($dbApplications)){
				$status = 'This character has already applied to a Guild.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}

			if(empty($postVars['apply_text'])){
				$status = 'You need to add some text.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}

			$dbInvitations = EntityGuilds::getInvites('player_id = "'.$dbPlayer->id.'" AND guild_id = "'.$guild_id.'"')->fetchObject();
			if(!empty($dbInvitations)){
				$status = 'This character is already invited to this Guild.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}

			$dbMember = EntityGuilds::getMembership('player_id = "'.$dbPlayer->id.'"')->fetchObject();
			if(!empty($dbMember)){
				$status = 'This character is already in a Guild.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}

			$input_text = filter_var($postVars['apply_text'], FILTER_SANITIZE_SPECIAL_CHARS);
			$current_date = strtotime(date('d-m-Y H:i:s'));
			
			EntityGuilds::insertMyApplication([
				'player_id' => $dbPlayer->id,
				'account_id' => $idLogged,
				'guild_id' => $guild_id,
				'text' => $input_text,
				'status' => 0,
				'date' => $current_date
			]);
			$status = 'Sent with success!';
			return self::viewApplyToThisGuild($request,$name,$status);
		}

		if(isset($postVars['btn_cancelapply'])){

			if(empty($postVars['character_cancelapply'])){
				$status = 'Select an application.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}

			$player_id = $postVars['character_cancelapply'];
			$dbApplications = EntityGuilds::getApplications('player_id = "'.$player_id.'" AND account_id = "'.$idLogged.'"');

			if(empty($dbApplications)){
				$status = 'Wrong character.';
				return self::viewApplyToThisGuild($request,$name,$status);
			}
			
			EntityGuilds::deleteMyApplication('player_id = "'.$player_id.'" AND account_id = "'.$idLogged.'"');
			$status = 'Application successfully canceled.';
			return self::viewApplyToThisGuild($request,$name,$status);
		}

	}

	public static function viewApplyToThisGuild($request,$name,$status = null)
	{
		$idLogged = SessionAdminLogin::idLogged();
		$dbPlayers = FunctionPlayer::getAllCharacters($idLogged);
		$guild_id = self::convertGuildName($name);

		$dbPlayersAccount = EntityPlayer::getPlayer('account_id = "'.$idLogged.'" AND deletion = "0"');
		while($player = $dbPlayersAccount->fetchObject()){
			$dbMember = EntityGuilds::getMembership('player_id = "'.$player->id.'"')->fetchObject();
			if(empty($dbMember)){
				$arrayMembers[] = [
					'id' => $player->id,
					'name' => $player->name,
				];
			}
		}

		$dbApplications = EntityGuilds::getApplications('account_id = "'.$idLogged.'" AND guild_id = "'.$guild_id.'"');
		while($application = $dbApplications->fetchObject()){

			$player = EntityPlayer::getPlayer('id = "'.$application->player_id.'"')->fetchObject();

			switch($application->status){
				case 0:
					$status_app = 'open';
					break;
				case 1:
					$status_app = 'closed';
					break;
				case 2:
					$status_app = 'accept';
			}

			$applications[] = [
				'player_id' => $application->player_id,
				'player_name' => $player->name,
				'player_level' => $player->level,
				'player_vocation' => FunctionPlayer::convertVocation($player->vocation),
				'player_status' => FunctionPlayer::isOnline($player->id),
				'guild_id' => $application->guild_id,
				'status' => $status_app,
				'date' => date('M d Y', $application->date),
			];
		}

		$content = View::render('pages/guilds/applytothisguild', [
			'applications' => $applications ?? null,
			'status' => $status,
			'players' => $arrayMembers ?? null,
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
		]);
		return parent::getBase('Guild', $content, 'guilds');
	}

}

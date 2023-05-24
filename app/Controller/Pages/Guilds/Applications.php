<?php
/**
 * Applications Class
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
use App\Model\Functions\Player as FunctionPlayer;
use App\Model\Functions\Server as FunctionServer;

class Applications extends Base{

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

	public static function actionApplications($request,$name)
	{
		$postVars = $request->getPostVars();
		$guild_id = self::convertGuildName($name);

		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}

		if(empty($postVars['application_player'])){
			$status = 'Please select a valid application.';
			return self::viewApplications($request,$name,$status);
		}
		$input_idplayer = filter_var($postVars['application_player'], FILTER_SANITIZE_NUMBER_INT);

		if(isset($postVars['btn_accept'])){
			$dbMembers = EntityGuilds::getMembership('player_id = "'.$input_idplayer.'"')->fetchObject();
			if(!empty($dbMembers)){
				$status = 'This character is a member of another Guild.';
				return self::viewApplications($request,$name,$status);
			}

			EntityGuilds::updateApplication('player_id = "'.$input_idplayer.'" AND guild_id = "'.$guild_id.'"', [
				'status' => 2,
			]);

			$dbRanks = EntityGuilds::getRanks('guild_id = "'.$guild_id.'" AND level = 1')->fetchObject();

			EntityGuilds::insertJoinMember([
				'player_id' => $input_idplayer,
				'guild_id' => $guild_id,
				'rank_id' => $dbRanks->id,
				'nick' => '',
				'date' => strtotime(date('M d Y, H:i:s')),
			]);
			$status = 'Updated successfully.';
			return self::viewApplications($request,$name,$status);
		}

		if(isset($postVars['btn_reject'])){
			if(empty($postVars['application_player'])){
				$status = 'Please select a valid application.';
				return self::viewApplications($request,$name,$status);
			}
			$input_idplayer = filter_var($postVars['application_player'], FILTER_SANITIZE_NUMBER_INT);
			EntityGuilds::updateApplication('player_id = "'.$input_idplayer.'" AND guild_id = "'.$guild_id.'"', [
				'status' => 1,
			]);
			$status = 'Updated successfully.';
			return self::viewApplications($request,$name,$status);
		}

	}

	public static function viewApplications($request,$name,$status = null)
	{
		$guild_id = self::convertGuildName($name);

		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}

		$dbApplications = EntityGuilds::getApplications('guild_id = "'.$guild_id.'"');
		while($application = $dbApplications->fetchObject()){

			$dbPlayer = EntityPlayer::getPlayer('id = "'.$application->player_id.'"')->fetchObject();


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
				'player' => $dbPlayer->id,
				'date' => date('M d Y, H:i:s', $application->date),
				'character' => $dbPlayer->name,
				'level' => $dbPlayer->level,
				'vocation' => FunctionPlayer::convertVocation($dbPlayer->vocation),
				'status' => $status_app,
			];
		}

		$content = View::render('pages/guilds/applications', [
			'status' => $status,
			'worlds' => FunctionServer::getWorlds(),
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
			'applications' => $applications ?? null,
		]);
		return parent::getBase('Guild', $content, 'guilds');
	}

}

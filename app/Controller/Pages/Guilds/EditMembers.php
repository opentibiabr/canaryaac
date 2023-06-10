<?php
/**
 * EditMembers Class
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

class EditMembers extends Base{

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

	public static function updateEditMembers($request,$name)
	{
		$postVars = $request->getPostVars();
		$guild_id = self::convertGuildName($name);
		$idLogged = SessionAdminLogin::idLogged();
		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}

		$filter_action = filter_var($postVars['action'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

		switch($filter_action){
			case 'settitle':
				$action = true;
				break;
			case 'setrank':
				$action = true;
				break;
			case 'exclude':
				$action = true;
				break;
			default:
				$action = false;
				break;
		}

		if($action == false){
			$status = 'Select an action.';
			return self::viewEditMembers($request,$name,$status);
		}

		if($filter_action == 'settitle'){
			if(empty($postVars['character'])){
				$status = 'You need to select a character.';
				return self::viewEditMembers($request,$name,$status);
			}
			if(empty($postVars['newtitle'])){
				$status = 'Write a title.';
				return self::viewEditMembers($request,$name,$status);
			}

			$filter_character = filter_var($postVars['character'], FILTER_SANITIZE_SPECIAL_CHARS);
			$filter_title = filter_var($postVars['newtitle'], FILTER_SANITIZE_SPECIAL_CHARS);

			$dbPlayers = EntityPlayer::getPlayer([ 'name' => $filter_character])->fetchObject();
			if(empty($dbPlayers)){
				$status = 'Invalid character.';
				return self::viewEditMembers($request,$name,$status);
			}

			$dbMember = EntityGuilds::getMembership([ 'player_id' => $dbPlayers->id, 'guild_id' => $guild_id])->fetchObject();
			if(empty($dbMember)){
				$status = 'This character does not belong to this guild.';
				return self::viewEditMembers($request,$name,$status);
			}

			EntityGuilds::updateMember([ 'guild_id' => $guild_id, 'player_id' => $dbPlayers->id], [
				'nick' => $filter_title,
			]);
			$status = 'Updated successfully.';
			return self::viewEditMembers($request,$name,$status);
		}

		if($filter_action == 'exclude'){
			if(empty($postVars['character'])){
				$status = 'You need to select a character.';
				return self::viewEditMembers($request,$name,$status);
			}
			$filter_character = filter_var($postVars['character'], FILTER_SANITIZE_SPECIAL_CHARS);

			$dbPlayers = EntityPlayer::getPlayer([ 'name' => $filter_character])->fetchObject();
			if(empty($dbPlayers)){
				$status = 'Invalid character.';
				return self::viewEditMembers($request,$name,$status);
			}

			$dbMember = EntityGuilds::getMembership([ 'player_id' => $dbPlayers->id, 'guild_id' => $guild_id])->fetchObject();
			if(empty($dbMember)){
				$status = 'This character does not belong to this guild.';
				return self::viewEditMembers($request,$name,$status);
			}

			EntityGuilds::deleteMember([ 'guild_id' => $guild_id, 'player_id' => $dbPlayers->id]);
			$status = 'Updated successfully.';
			return self::viewEditMembers($request,$name,$status);
		}

		if($filter_action == 'setrank'){
			if(empty($postVars['character'])){
				$status = 'You need to select a character.';
				return self::viewEditMembers($request,$name,$status);
			}
			if(empty($postVars['newrank'])){
				$status = 'You need to select a rank.';
				return self::viewEditMembers($request,$name,$status);
			}
			$filter_character = filter_var($postVars['character'], FILTER_SANITIZE_SPECIAL_CHARS);
			$filter_rank = filter_var($postVars['newrank'], FILTER_SANITIZE_SPECIAL_CHARS);

			$dbPlayers = EntityPlayer::getPlayer([ 'name' => $filter_character])->fetchObject();
			if(empty($dbPlayers)){
				$status = 'Invalid character.';
				return self::viewEditMembers($request,$name,$status);
			}
			$dbMember = EntityGuilds::getMembership([ 'player_id' => $dbPlayers->id, 'guild_id' => $guild_id])->fetchObject();
			if(empty($dbMember)){
				$status = 'This character does not belong to this guild.';
				return self::viewEditMembers($request,$name,$status);
			}
			$dbRanks = EntityGuilds::getRanks([ 'guild_id' => $guild_id, 'level' => $filter_rank])->fetchObject();
			$newRank = $dbRanks->id;
			EntityGuilds::updateRankOnMember([ 'guild_id' => $guild_id, 'player_id' => $dbPlayers->id], [
				'rank_id' => $newRank,
			]);
			$status = 'Updated successfully.';
			return self::viewEditMembers($request,$name,$status);
		}
	}

	public static function viewEditMembers($request,$name,$status = null)
	{
		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}
		$dbMembers = EntityGuilds::getMembership([ 'guild_id' => self::convertGuildName($name)]);
		while($member = $dbMembers->fetchObject()){
			$memberRank = FunctionGuilds::convertRankGuild($member->rank_id);
			$dbPlayers = EntityPlayer::getPlayer([ 'id' => $member->player_id]);
			while($player = $dbPlayers->fetchObject()){
				$arrayMembers[] = [
					'player_name' => $player->name,
					'rank_name' => $memberRank['name'],
					'rank_level' => $memberRank['rank_level'],
				];
			}
		}
		$content = View::render('pages/guilds/editmembers', [
			'status' => $status,
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
			'members' => $arrayMembers,
			'ranks' => FunctionGuilds::getRanks(self::convertGuildName($name)),
		]);
		return parent::getBase('Guild', $content, 'guilds');
	}

}

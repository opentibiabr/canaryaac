<?php
/**
 * DeclareWar Class
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
use App\Model\Entity\Account as EntityAccount;
use App\Model\Functions\Server as FunctionServer;

class DeclareWar extends Base
{

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

	public static function verifyAccountGuild($guild_id)
	{
		$idLogged = SessionAdminLogin::idLogged();
		$select_guild = EntityGuilds::getGuilds([ 'id' => $guild_id])->fetchObject();
		$select_player = EntityPlayer::getPlayer([ 'id' => $select_guild->ownerid])->fetchObject();
		$select_account = EntityAccount::getAccount([ 'id' => $select_player->account_id])->fetchObject();
		if ($select_account->id != $idLogged) {
			return false;
		}
		if ($select_account->id == $idLogged) {
			return true;
		}
	}

	public static function acceptWar($request, $name, $war_id)
	{
		$filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
		$filter_war_id = filter_var($war_id, FILTER_SANITIZE_NUMBER_INT);
		$guild_id = self::convertGuildName($filter_name);
		$verifyAccount = self::verifyAccountGuild($guild_id);

		if ($verifyAccount == false) {
			$request->getRouter()->redirect('/community/guilds/'.$name.'/guildwars');
		}
		if ($verifyAccount == true) {
			EntityGuilds::updateWar('id = "'.$filter_war_id.'"', [
				'status' => 1,
			]);
			$request->getRouter()->redirect('/community/guilds/'.$name.'/guildwars');
		}
	}

	public static function rejectWar($request, $name, $war_id)
	{
		$filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
		$filter_war_id = filter_var($war_id, FILTER_SANITIZE_NUMBER_INT);
		$guild_id = self::convertGuildName($filter_name);
		$verifyAccount = self::verifyAccountGuild($guild_id);

		if ($verifyAccount == false) {
			$request->getRouter()->redirect('/community/guilds/'.$name.'/guildwars');
		}
		if ($verifyAccount == true) {
			EntityGuilds::updateWar('id = "'.$filter_war_id.'"', [
				'status' => 0,
			]);
			$request->getRouter()->redirect('/community/guilds/'.$name.'/guildwars');
		}
	}

	public static function cancelWar($request, $name, $war_id)
	{
		$filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
		$filter_war_id = filter_var($war_id, FILTER_SANITIZE_NUMBER_INT);
		$guild_id = self::convertGuildName($filter_name);
		$verifyAccount = self::verifyAccountGuild($guild_id);

		if ($verifyAccount == false) {
			$request->getRouter()->redirect('/community/guilds/'.$name.'/guildwars');
		}
		if ($verifyAccount == true) {
			EntityGuilds::deleteWar('id = "'.$filter_war_id.'"');
			$request->getRouter()->redirect('/community/guilds/'.$name.'/guildwars');
		}
	}

	public static function currentGuildWar($guild_id)
	{
		$dbGuildWars = EntityGuilds::getWars(['guild1' => $guild_id, 'guild2' => $guild_id], 'OR');
		while ($war = $dbGuildWars->fetchObject()) {
			$total_my_kills = 0;
			$total_opponent_kills = 0;

			$select_war_my_kills = EntityGuilds::getKills(['warid' => $war->id, 'killerguild' => $war->guild1]);
			while ($war_kills = $select_war_my_kills->fetchObject()) {
				$total_my_kills++;
				$array[] = [
					'killer' => $war_kills->killer,
					'target' => $war_kills->target,
					'killerguild' => $war_kills->killerguild,
					'targetguild' => $war_kills->targetguild,
					'warid' => $war_kills->warid,
					'time' => $war_kills->time,
				];
			}

			$select_war_opponent_kills = EntityGuilds::getKills(['warid' => $war->id, 'killerguild' => $war->guild2]);
			while ($war_kills = $select_war_opponent_kills->fetchObject()) {
				$total_opponent_kills++;
				$array[] = [
					'killer' => $war_kills->killer,
					'target' => $war_kills->target,
					'killerguild' => $war_kills->killerguild,
					'targetguild' => $war_kills->targetguild,
					'warid' => $war_kills->warid,
					'time' => $war_kills->time,
				];
			}

			if ($war->status == 1) {
				$arrayWars[] = [
					'id' => $war->id,
					'guild1' => $war->guild1,
					'name1' => $war->name1,
					'kills1' => $total_my_kills,
					'guild2' => $war->guild2,
					'name2' => $war->name2,
					'kills2' => $total_opponent_kills,
					'price' => $war->price2,
					'frags' => $war->frags,
					'comment' => $war->comment,
					'status' => $war->status,
					'started' => date('M d Y', $war->started),
					'ended' => date('M d Y', $war->ended),
				];
			}
		}
		return $arrayWars ?? '';
	}

	public static function historyGuildWar($guild_id)
	{
		$dbGuildWars = EntityGuilds::getWars(['guild1' => $guild_id, 'guild2' => $guild_id], 'OR');
		while ($war = $dbGuildWars->fetchObject()) {
			$total_my_kills = 0;
			$total_opponent_kills = 0;

			$select_war_my_kills = EntityGuilds::getKills(['warid' => $war->id, 'killerguild' => $war->guild1]);
			while ($war_kills = $select_war_my_kills->fetchObject()) {
				$total_my_kills++;
				$array[] = [
					'killer' => $war_kills->killer,
					'target' => $war_kills->target,
					'killerguild' => $war_kills->killerguild,
					'targetguild' => $war_kills->targetguild,
					'warid' => $war_kills->warid,
					'time' => $war_kills->time,
				];
			}

			$select_war_opponent_kills = EntityGuilds::getKills(['warid' => $war->id, 'killerguild' => $war->guild2]);
			while ($war_kills = $select_war_opponent_kills->fetchObject()) {
				$total_opponent_kills++;
				$array[] = [
					'killer' => $war_kills->killer,
					'target' => $war_kills->target,
					'killerguild' => $war_kills->killerguild,
					'targetguild' => $war_kills->targetguild,
					'warid' => $war_kills->warid,
					'time' => $war_kills->time,
				];
			}

			if ($war->status == 0) {
				$arrayWars[] = [
					'id' => $war->id,
					'guild1' => $war->guild1,
					'name1' => $war->name1,
					'kills1' => $total_my_kills,
					'guild2' => $war->guild2,
					'name2' => $war->name2,
					'kills2' => $total_opponent_kills,
					'price' => $war->price2,
					'frags' => $war->frags,
					'comment' => $war->comment,
					'status' => $war->status,
					'started' => date('M d Y', $war->started),
					'ended' => date('M d Y', $war->ended),
				];
			}
		}
		return $arrayWars ?? '';
	}

    public static function viewGuildWars($request,$name)
	{
		$filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
		$guild_id = self::convertGuildName($filter_name);
		$dbGuildWars = EntityGuilds::getWars(['guild1' => $guild_id, 'guild2' => $guild_id], 'OR');
		while($war = $dbGuildWars->fetchObject()){
			if ($war->status == 2) {
				if ($war->guild1 == $guild_id) {
					$arrayWars[] = [
						'id' => $war->id,
						'guild1' => $war->guild1,
						'guild2' => $war->guild2,
						'name' => $war->name2,
						'price' => $war->price2,
						'frags' => $war->frags,
						'comment' => $war->comment,
						'status' => $war->status,
						'started' => date('M d Y', $war->started),
						'ended' => date('M d Y', $war->ended),
					];
				}
				if ($war->guild2 == $guild_id) {
					$arrayWars[] = [
						'id' => $war->id,
						'guild1' => $war->guild1,
						'guild2' => $war->guild2,
						'name' => $war->name1,
						'price' => $war->price1,
						'frags' => $war->frags,
						'comment' => $war->comment,
						'status' => $war->status,
						'started' => date('M d Y', $war->started),
						'ended' => date('M d Y', $war->ended),
					];
				}
			}
		}
		$content = View::render('pages/guilds/guildwars', [
			'isleader' => FunctionGuilds::verifyAccountLeader(self::convertGuildName($name)),
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
			'wars' => $arrayWars ?? '',
			'current_wars' => self::currentGuildWar(self::convertGuildName($name)),
			'history_wars' => self::historyGuildWar(self::convertGuildName($name)),
		]);
		return parent::getBase('Guilds', $content, 'guilds');
	}

	public static function viewDeclareWar($request,$name,$status = null)
	{
		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}
		$guild_id = self::convertGuildName($name);
		$dbGuilds = EntityGuilds::getGuilds();

		while($guild = $dbGuilds->fetchObject()){
			$guild_war = EntityGuilds::getWars(['guild1' => $guild->id, 'guild2' => $guild->id], 'OR')->fetchObject();
			if (empty($guild_war)) {
				$war_status = 0;
			} else {
				if ($guild_war->status == 2) {
					$war_status = 1;
				}
				if ($guild_war->status == 1) {
					$war_status = 1;
				}
				if ($guild_war->status == 0) {
					$war_status = 0;
				}
			}

			if($guild->id != $guild_id){
				if ($war_status == 0) {
					$arrayGuilds[] = [
						'id' => $guild->id,
						'name' => $guild->name,
						'status' => $war_status,
					];
				}
			}
		}
		$content = View::render('pages/guilds/declarewar', [
			'status' => $status,
			'isleader' => FunctionGuilds::verifyAccountLeader($guild_id),
			'worlds' => FunctionServer::getWorlds(),
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
			'guilds' => $arrayGuilds ?? null,
		]);
		return parent::getBase('Guilds', $content, 'guilds');
	}

	public static function insertDeclareWar($request,$name)
	{
		$postVars = $request->getPostVars();
		$guild_id = self::convertGuildName($name);

		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}

		if(empty($postVars['war_opponent'])){
			$status = 'You need to select an opponent.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if(empty($postVars['war_duration'])){
			$status = 'You need to set war duration.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if(empty($postVars['war_kills'])){
			$status = 'You need to define how many kills.';
			return self::viewDeclareWar($request,$name,$status);
		}
		
		$filter_opponent = filter_var($postVars['war_opponent'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
		$filter_duration = filter_var($postVars['war_duration'], FILTER_SANITIZE_NUMBER_INT);
		$filter_kills = filter_var($postVars['war_kills'], FILTER_SANITIZE_NUMBER_INT);
		$filter_pricemyguild = filter_var($postVars['price_myguild'], FILTER_SANITIZE_NUMBER_INT);
		$filter_priceopponent = filter_var($postVars['price_opponent'], FILTER_SANITIZE_NUMBER_INT);
		$filter_comment = filter_var($postVars['war_comment'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

		$dbGuilds = EntityGuilds::getGuilds([ 'name' => $filter_opponent])->fetchObject();
		if(empty($dbGuilds)){
			$status = 'Invalid opponent.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if($dbGuilds->id == $guild_id){
			$status = 'Invalid opponent.';
			return self::viewDeclareWar($request,$name,$status);
		}

		if(!filter_var($filter_duration, FILTER_VALIDATE_INT)){
			$status = 'You need to set a valid duration.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if($filter_duration > 180){
			$status = 'You need to set a time between 7 to 180 days.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if($filter_duration < 7){
			$status = 'You need to set a time between 7 to 180 days.';
			return self::viewDeclareWar($request,$name,$status);
		}

		if(!filter_var($filter_kills, FILTER_VALIDATE_INT)){
			$status = 'You need to set a valid kills value.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if($filter_kills > 1000){
			$status = 'You need to set a kills value less than 1000.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if($filter_kills < 10){
			$status = 'You need to set a kills value greater than 10.';
			return self::viewDeclareWar($request,$name,$status);
		}

		if(!filter_var($filter_pricemyguild, FILTER_VALIDATE_INT)){
			$status = 'You need to set a gold value for you to pay.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if($filter_pricemyguild > 2000000000){
			$status = 'You need to set a gold value less than 2000000000.';
			return self::viewDeclareWar($request,$name,$status);
		}

		if(!filter_var($filter_priceopponent, FILTER_VALIDATE_INT)){
			$status = 'You need to set a gold value for the opponent to pay.';
			return self::viewDeclareWar($request,$name,$status);
		}
		if($filter_priceopponent > 2000000000){
			$status = 'You need to set a gold value less than 2000000000.';
			return self::viewDeclareWar($request,$name,$status);
		}

		$myGuild = EntityGuilds::getGuilds([ 'id' => $guild_id])->fetchObject();

		EntityGuilds::insertWar([
			'guild1' => $guild_id,
			'guild2' => $dbGuilds->id,
			'name1' => $myGuild->name,
			'name2' => $dbGuilds->name,
			'price1' => $filter_pricemyguild,
			'price2' => $filter_priceopponent,
			'frags' => $filter_kills,
			'comment' => $filter_comment,
			'status' => 2,
			'started' => strtotime(date('Y-m-d H:i:s')),
			'ended' => strtotime(date('Y-m-d H:i:s', strtotime('+7 Days'))),
		]);
		$request->getRouter()->redirect('/community/guilds/'.$name.'/guildwars');
	}
    
}
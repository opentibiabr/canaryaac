<?php
/**
 * ResignLeadership Class
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

class ResignLeadership extends Base{

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

	public static function updateResignLeadership($request,$name)
	{
		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}

		$postVars = $request->getPostVars();
		$guild_id = self::convertGuildName($name);
		$idLogged = SessionAdminLogin::idLogged();

		if(empty($postVars['password'])){
			$status = 'You need to enter your password.';
			self::viewResignLeadership($request,$name,$status);
		}
		if(empty($postVars['character'])){
			$status = 'You need to select a character.';
			self::viewResignLeadership($request,$name,$status);
		}

		$filter_character = filter_var($postVars['character'], FILTER_SANITIZE_SPECIAL_CHARS);
		$filter_password = filter_var($postVars['password'], FILTER_SANITIZE_SPECIAL_CHARS);

		$dbAccount = EntityAccount::getAccount([ 'id' => $idLogged, 'password' => $filter_password])->fetchObject();
		if(empty($dbAccount)){
			$status = 'Something went wrong.';
			self::viewResignLeadership($request,$name,$status);
		}

		$dbPlayer = EntityPlayer::getPlayer([ 'name' => $filter_character])->fetchObject();
		if(empty($dbPlayer)){
			$status = 'Character does not exist.';
			self::viewResignLeadership($request,$name,$status);
		}

		$dbMember = EntityGuilds::getMembership([ 'player_id' => $dbPlayer->id, 'guild_id' => $guild_id])->fetchObject();
		if(empty($dbMember)){
			$status = 'Must be a member of the Guild.';
			self::viewResignLeadership($request,$name,$status);
		}

		EntityGuilds::updateGuild('guild_id = "'.$guild_id.'"', [
			'ownerid' => $dbPlayer->id,
		]);
		$status = 'Updated successfully.';
		self::viewResignLeadership($request,$name,$status);

	}

	public static function viewResignLeadership($request,$name,$status = null)
	{
		$isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
		if($isLeader == false){
			$request->getRouter()->redirect('/community/guilds/'.$name.'/view');
		}

		$content = View::render('pages/guilds/resignleadership', [
			'status' => $status,
			'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
			'members' => FunctionGuilds::getGuildMembership(self::convertGuildName($name)),
		]);
		return parent::getBase('Guilds', $content, 'guilds');
	}

}

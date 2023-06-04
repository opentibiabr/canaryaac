<?php
/**
 * EditRanks Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Guilds;

use App\Controller\Pages\Base;
use App\Utils\View;
use App\Model\Entity\Guilds as EntityGuilds;
use App\Model\Functions\Guilds as FunctionGuilds;
use App\Model\Functions\Server as FunctionServer;

class EditRanks extends Base
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

    public static function updateEditRanks($request, $name)
    {
        $postVars = $request->getPostVars();
        $guild_id = self::convertGuildName($name);

        $isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
        if($isLeader == false) {
            $request->getRouter()->redirect('/community/guilds/'.$name.'/view');
        }

        if(empty($postVars['rank_name'])) {
            $status = 'Write a name.';
            return self::viewEditRanks($request, $name, $status);
        }
        if(empty($postVars['rank_level'])) {
            $status = 'Select a rank.';
            return self::viewEditRanks($request, $name, $status);
        }

        $filter_name = filter_var($postVars['rank_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_level = filter_var($postVars['rank_level'], FILTER_SANITIZE_SPECIAL_CHARS);

        $dbRanks = EntityGuilds::getRanks('id = "'.$filter_level.'" AND guild_id = "'.$guild_id.'"')->fetchObject();
        if(empty($dbRanks)) {
            $status = 'Please select a valid rank.';
            return self::viewEditRanks($request, $name, $status);
        }

        EntityGuilds::updateRank('id = "'.$filter_level.'" AND guild_id = "'.$guild_id.'"', [
            'name' => $filter_name,
        ]);
        $status = 'Updated successfully.';
        return self::viewEditRanks($request, $name, $status);
    }

    public static function viewEditRanks($request, $name, $status = null)
    {
        $content = View::render('pages/guilds/editranks', [
            'status' => $status,
            'worlds' => FunctionServer::getWorlds(),
            'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
            'ranks' => FunctionGuilds::getRanks(self::convertGuildName($name)),
        ]);
        return parent::getBase('Guild', $content, 'guilds');
    }
}

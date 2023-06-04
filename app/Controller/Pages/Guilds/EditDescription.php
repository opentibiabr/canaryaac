<?php
/**
 * Edit Description Guild Class
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

class EditDescription extends Base
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

    public static function updateEditDescription($request, $name)
    {
        $isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
        if($isLeader == false) {
            $request->getRouter()->redirect('/community/guilds/'.$name.'/view');
        }

        $postVars = $request->getPostVars();
        $guild_id = self::convertGuildName($name);
        if(empty($postVars['description'])) {
            $status = 'You need to write some description.';
            return self::viewEditDescription($request, $name, $status);
        }

        $filter_description = filter_var($postVars['description'], FILTER_SANITIZE_SPECIAL_CHARS);
        $count_description = strlen($filter_description);
        if($count_description > 250) {
            $status = 'It must be less than 250 characters.';
            return self::viewEditDescription($request, $name, $status);
        }

        EntityGuilds::updateGuild('id = "'.$guild_id.'"', [
            'description' => $filter_description,
        ]);
        $status = 'Updated successfully.';
        return self::viewEditDescription($request, $name, $status);
    }

    public static function viewEditDescription($request, $name, $status = null)
    {
        $isLeader = FunctionGuilds::verifyAccountLeader(self::convertGuildName($name));
        if($isLeader == false) {
            $request->getRouter()->redirect('/community/guilds/'.$name.'/view');
        }

        $content = View::render('pages/guilds/editdescription', [
            'status' => $status,
            'guild' => FunctionGuilds::getGuildbyId(self::convertGuildName($name)),
        ]);
        return parent::getBase('Guilds', $content, 'guilds');
    }
}

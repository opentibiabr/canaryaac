<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Api;

use App\Model\Entity\Guilds as EntityGuild;
use Exception;

class Guilds extends Api
{
    public static function getGuildsList($request)
    {
        $guilds = [];
        $select = EntityGuild::getGuilds();
        while ($obGuilds = $select->fetchObject(EntityGuild::class)) {
            $guilds[] = [
                'id' => $obGuilds->id,
                'level' => $obGuilds->level,
                'name' => $obGuilds->name,
                'ownerid' => $obGuilds->ownerid,
                'creationdata' => $obGuilds->creationdata,
                'motd' => $obGuilds->motd,
                'residence' => $obGuilds->residence,
                'balance' => $obGuilds->balance,
                'points' => $obGuilds->points
            ];
        }

        if ($obGuilds == false) {
            throw new Exception('Nenhuma guild foi encontrada.', 404);
        }

        return $guilds;
    }

    public static function getGuilds($request)
    {
        return self::getGuildsList($request);
    }
}

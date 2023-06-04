<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Api;

use App\Model\Entity\Player as EntityPlayer;
use Exception;

class LastDeaths extends Api
{
    public static function getDeaths($request)
    {
        $deaths = [];
        $select = EntityPlayer::getDeaths(null, 'time DESC');
        while ($obDeaths = $select->fetchObject(EntityPlayer::class)) {
            $deaths[] = [
                'time' => $obDeaths->time,
                'level' => $obDeaths->level,
                'killed_by' => $obDeaths->killed_by,
                'is_player' => $obDeaths->is_player,
                'mostdamage_by' => $obDeaths->mostdamage_by,
                'mostdamage_is_player' => $obDeaths->mostdamage_is_player,
                'unjustified' => $obDeaths->unjustified,
                'mostdamage_unjustified' => $obDeaths->mostdamage_unjustified
            ];
        }

        if ($obDeaths == false) {
            throw new Exception('Nenhuma morte foi encontrada.', 404);
        }

        return $deaths;
    }

    public static function getLastDeaths($request)
    {
        return self::getDeaths($request);
    }
}

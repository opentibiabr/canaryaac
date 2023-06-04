<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Api;

use App\Model\Functions\Server;
use Exception;

class Boosted extends Api
{
    public static function getBoostedDiscordBOT()
    {
        $boosted_creature = Server::getBoostedCreature();
        $boosted_boss = Server::getBoostedBoss();

        $boosted = [
            'boostedcreature' => $boosted_creature['boostname'],
            'boostedcreature_img' => $boosted_creature['image_url'],
            'boostedboss' => $boosted_boss['boostname'],
            'boostedboss_img' => $boosted_boss['image_url']
        ];
        if (empty($boosted)) {
            throw new Exception('Algo deu errado.', 404);
        }
        return $boosted;
    }
}

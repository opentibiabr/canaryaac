<?php
/**
 * Signature Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Player as FunctionsPlayer;

class Signature{

    public static function generate($request, $id)
    {
        $bg_image = URL . '/resources/images/signature/bg.png';
        $filter_playerId = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $player = EntityPlayer::getPlayer([ 'id' => $filter_playerId])->fetchObject();
        if (empty($player)) {
            return null;
        }
        
        $signature = imagecreatefrompng($bg_image);
        $color_white = imagecolorallocate($signature, 255, 255, 255);

        imagestring($signature, 5, 100, 15, $player->name, $color_white);

        // Level
        imagestring($signature, 4, 100, 30, 'Level:', $color_white);
        imagestring($signature, 4, 155, 30, $player->level, $color_white);

        // Vocation
        imagestring($signature, 4, 100, 45, 'Vocation:', $color_white);
        imagestring($signature, 4, 185, 45, FunctionsPlayer::convertVocation($player->vocation), $color_white);

        // World
        imagestring($signature, 4, 100, 60, 'World:', $color_white);
        imagestring($signature, 4, 155, 60, FunctionsPlayer::convertWorld($player->world), $color_white);

        // Town
        imagestring($signature, 4, 100, 75, 'Town:', $color_white);
        imagestring($signature, 4, 155, 75, FunctionsPlayer::convertTown($player->town_id), $color_white);

        // Magic
        imagestring($signature, 3, 10, 100, 'ML:', $color_white);
        imagestring($signature, 3, 35, 100, $player->maglevel, $color_white);

        // Fist
        imagestring($signature, 3, 60, 100, 'Fist:', $color_white);
        imagestring($signature, 3, 100, 100, $player->skill_fist, $color_white);

        // Club
        imagestring($signature, 3, 130, 100, 'Club:', $color_white);
        imagestring($signature, 3, 170, 100, $player->skill_club, $color_white);

        // Sword
        imagestring($signature, 3, 195, 100, 'Sword:', $color_white);
        imagestring($signature, 3, 240, 100, $player->skill_sword, $color_white);

        // Axe
        imagestring($signature, 3, 265, 100, 'Axe:', $color_white);
        imagestring($signature, 3, 295, 100, $player->skill_axe, $color_white);

        // Distance
        imagestring($signature, 3, 325, 100, 'Dist:', $color_white);
        imagestring($signature, 3, 365, 100, $player->skill_dist, $color_white);

        // Shield
        imagestring($signature, 3, 395, 100, 'Shield:', $color_white);
        imagestring($signature, 3, 450, 100, $player->skill_shielding, $color_white);

        header('Content-type: image/png');
        $signature = imagepng($signature);
        return $signature;
    }
}
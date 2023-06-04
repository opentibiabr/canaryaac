<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\Achievements as EntityAchievements;
use App\Model\Functions\Achievements as FunctionsAchievements;
use \App\Utils\View;

class Achievements extends Base{

    public static function viewAchievements()
    {
        $content = View::render('pages/library/achievements', [
            'achievements' => FunctionsAchievements::getAllAchievements(),
            'total_secretachievements' => (int)EntityAchievements::getAchievements(['secret' => "1"], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
        ]);
        return parent::getBase('Achievements', $content, 'achievements');
    }

}
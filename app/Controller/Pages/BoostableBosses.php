<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\Creatures as EntityCreatures;
use App\Utils\View;
use App\Model\Functions\Server;

class BoostableBosses extends Base
{
    public static function getBosses()
    {
        $arrayCreatures = [];
        $dbCreatures = EntityCreatures::getBoss();
        while($creature = $dbCreatures->fetchObject()) {
            $arrayCreatures[] = [
                'tag' => $creature->tag,
                'name' => $creature->name,
            ];
        }
        return $arrayCreatures;
    }

    public static function viewBoostableBosses()
    {
        $content = View::render('pages/library/boostablebosses', [
            'bosses' => self::getBosses(),
            'boostedboss' => Server::getBoostedBoss(),
        ]);
        return parent::getBase('Boostable Bosses', $content, 'boostablebosses');
    }

}

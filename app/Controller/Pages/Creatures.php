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

class Creatures extends Base
{
    public static function getCreatures()
    {
        $arrayCreatures = [];
        $dbCreatures = EntityCreatures::getCreatures();
        while($creature = $dbCreatures->fetchObject()) {
            $arrayCreatures[] = [
                'tag' => $creature->tag,
                'name' => $creature->name,
                'description' => $creature->description,
            ];
        }
        return $arrayCreatures;
    }

    public static function viewCreatures()
    {
        $content = View::render('pages/library/creatures', [
            'creatures' => self::getCreatures(),
            'boostedcreature' => Server::getBoostedCreature(),
        ]);
        return parent::getBase('Creatures', $content, 'creatures');
    }
}

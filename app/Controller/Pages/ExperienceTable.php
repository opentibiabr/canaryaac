<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Functions\Server;

class ExperienceTable extends Base
{
    public static function viewExperienceTable()
    {
        $content = View::render('pages/library/experiencetable', [
            'boostedboss' => Server::getBoostedBoss(),
        ]);
        return parent::getBase('Experience Table', $content, 'experiencetable');
    }
}

<?php
/**
 * Rules Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Support;

use App\Utils\View;
use App\Controller\Pages\Base;

class Rules extends Base
{
    public static function viewRules($request)
    {
        $content = View::render('pages/support/rules', []);
        return parent::getBase('Rules', $content, $currentPage = 'rules');
    }
}

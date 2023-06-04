<?php
/**
 * PremiumFeatures Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Utils\View;
use App\Controller\Pages\Base;

class PremiumFeatures extends Base
{
    public static function viewPremiumFeatures($request)
    {
        $content = View::render('pages/premiumfeatures', []);
        return parent::getBase('Premium Features', $content, $currentPage = 'premiumfeatures');
    }
}

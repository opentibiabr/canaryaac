<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Utils;

use App\Model\Functions\Website;
use Twig\TwigFilter;

class ViewFilters
{
    public static function addFilters()
    {
        $filter = new TwigFilter('exp', function ($exp) {
            return number_format($exp, '2', '.', '');
        });

        return $filter;
    }
}

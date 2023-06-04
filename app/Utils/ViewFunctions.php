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
use Twig\TwigFunction;

class ViewFunctions
{
    public static function addStyleCode()
    {
        $function = new TwigFunction('getStyle', function ($i) {
            return Website::getStyleCode($i);
        });
        return $function;
    }

    public static function addStyleCss()
    {
        $function = new TwigFunction('getStyleCss', function ($i) {
            return Website::getStyleCss($i);
        });
        return $function;
    }
}

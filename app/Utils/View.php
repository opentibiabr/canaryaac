<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Utils;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Utils\ViewFunctions;
use App\Utils\ViewFilters;

class View
{
    private static $vars = [];

    public static function init($vars = [])
    {
        self::$vars = $vars;
    }

    public static function getContentView($view)
    {
        $loader = new FilesystemLoader([
            __DIR__.'/../../resources/view/' . $view . '/',
            __DIR__.'/../../resources/view/base/',
            __DIR__.'/../../resources/view/pagination/',
            __DIR__.'/../../resources/view/admin/',
            __DIR__.'/../../resources/view/admin/alert',
            __DIR__.'/../../resources/view/themeboxes/',
        ]);
        if($_ENV['DEV_MODE'] == true) {
            $twig = new Environment($loader, [
                'debug' => true,
                'charset' => 'utf-8',
                'cache' => false,
                'autoescape' => 'html',
            ]);
        } else {
            $twig = new Environment($loader, [
                'debug' => false,
                'charset' => 'utf-8',
                'cache' => __DIR__.'/../../resources/view/cache',
            ]);
        }

        $ViewFunctions = new ViewFunctions();
        $twig->addFunction($ViewFunctions->addStyleCode());
        $twig->addFunction($ViewFunctions->addStyleCss());

        $ViewFilters = new ViewFilters();
        $twig->addFilter($ViewFilters->addFilters());

        return $twig;
    }

    public static function render($view, $vars = [])
    {
        $vars = array_merge(self::$vars, $vars);

        $array = explode('/', $view);
        $view_file = end($array);
        $remove_file = array_pop($array);
        $view_path = implode('/', $array);

        $contentView = self::getContentView($view_path);
        return $contentView->render($view_file.'.html.twig', $vars);
    }
}

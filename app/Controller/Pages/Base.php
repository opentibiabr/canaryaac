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
use App\Session\Admin\Login as SessionPlayerLogin;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Functions\Server as FunctionsServer;
use App\Model\Functions\ThemeBox as FunctionsThemeBox;

class Base
{
    public static function getPagination($request, $obPagination)
    {
        $pages = $obPagination->getPages();
        /*if(count($pages) <= 1) return '';*/

        $links = '';
        $url = $request->getRouter()->getCurrentUrl();
        $queryParams = $request->getQueryParams();

        foreach($pages as $page) {
            $queryParams['page'] = $page['page'];
            $link = $url.'?'.http_build_query($queryParams);
            $links .= View::render('pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'CurrentPageLink' : '',
            ]);
        }
        return View::render('pagination/box', [
            'links' => $links,
            'total' => count($pages)
        ]);
    }

    public static function getLogged()
    {
        if(SessionPlayerLogin::isLogged() == true) {
            $code = 'true';
        } else {
            $code = 'false';
        }
        return $code;
    }

    public static function getMenu($currentPage)
    {
        $menu = [
            'latestnews' => [
                'name' => 'Last News',
                'tag' => 'latestnews',
                'link' => 'latestnews',
                'color' => 'd7d7d7',
                'category' => 'news',
            ],
            'newsarchive' => [
                'name' => 'News Archive',
                'tag' => 'newsarchive',
                'link' => 'newsarchive',
                'color' => 'd7d7d7',
                'category' => 'news',
            ],
            'Event Schedule' => [
                'name' => 'Event Schedule',
                'tag' => 'Event Schedule',
                'link' => 'eventcalendar',
                'color' => 'd7d7d7',
                'category' => 'news',
            ],
            'creatures' => [
                'name' => 'Creatures',
                'tag' => 'creatures',
                'link' => 'library/creatures',
                'color' => 'd7d7d7',
                'category' => 'library',
            ],
            'boostablebosses' => [
                'name' => 'Boostable Bosses',
                'tag' => 'boostablebosses',
                'link' => 'library/boostablebosses',
                'color' => 'd7d7d7',
                'category' => 'library',
            ],
            'achievements' => [
                'name' => 'Achievements',
                'tag' => 'achievements',
                'link' => 'library/achievements',
                'color' => 'd7d7d7',
                'category' => 'library',
            ],
            'experiencetable' => [
                'name' => 'Experience Table',
                'tag' => 'experiencetable',
                'link' => 'library/experiencetable',
                'color' => 'd7d7d7',
                'category' => 'library',
            ],
            'characters' => [
                'name' => 'Characters',
                'tag' => 'characters',
                'link' => 'community/characters',
                'color' => 'd7d7d7',
                'category' => 'community',
            ],
            'worlds' => [
                'name' => 'Worlds',
                'tag' => 'worlds',
                'link' => 'community/worlds',
                'color' => 'd7d7d7',
                'category' => 'community',
            ],
            'highscores' => [
                'name' => 'Highscores',
                'tag' => 'highscores',
                'link' => 'community/highscores',
                'color' => 'd7d7d7',
                'category' => 'community',
            ],
            'lastdeaths' => [
                'name' => 'Last Deaths',
                'tag' => 'lastdeaths',
                'link' => 'community/lastdeaths',
                'color' => 'd7d7d7',
                'category' => 'community',
            ],
            'houses' => [
                'name' => 'Houses',
                'tag' => 'houses',
                'link' => 'community/houses',
                'color' => 'd7d7d7',
                'category' => 'community',
            ],
            'guilds' => [
                'name' => 'Guilds',
                'tag' => 'guilds',
                'link' => 'community/guilds',
                'color' => 'd7d7d7',
                'category' => 'community',
            ],
            'polls' => [
                'name' => 'Polls',
                'tag' => 'polls',
                'link' => 'community/polls',
                'color' => 'd7d7d7',
                'category' => 'community',
            ],
            'accountmanagement' => [
                'name' => 'Account Management',
                'tag' => 'account',
                'link' => 'account',
                'color' => 'd7d7d7',
                'category' => 'account',
            ],
            'createaccount' => [
                'name' => 'Create Account',
                'tag' => 'createaccount',
                'link' => 'createaccount',
                'color' => 'd7d7d7',
                'category' => 'account',
            ],
            'downloads' => [
                'name' => 'Download Client',
                'tag' => 'downloads',
                'link' => 'downloads',
                'color' => 'd7d7d7',
                'category' => 'account',
            ],
            'lostaccount' => [
                'name' => 'Lost Account',
                'tag' => 'lostaccount',
                'link' => 'account/lostaccount',
                'color' => 'd7d7d7',
                'category' => 'account',
            ],
            'Active Wars' => [
                'name' => 'Active Wars',
                'tag' => 'activewars',
                'link' => 'guildwars/active',
                'color' => 'd7d7d7',
                'category' => 'wars',
            ],
            'Pending Wars' => [
                'name' => 'Pending Wars',
                'tag' => 'pendingwars',
                'link' => 'guildwars/pending',
                'color' => 'd7d7d7',
                'category' => 'wars',
            ],
            'Surrender Wars' => [
                'name' => 'Surrender Wars',
                'tag' => 'surrenderwars',
                'link' => 'guildwars/surrender',
                'color' => 'd7d7d7',
                'category' => 'wars',
            ],
            'Ended Wars' => [
                'name' => 'Ended Wars',
                'tag' => 'endedwars',
                'link' => 'guildwars/ended',
                'color' => 'd7d7d7',
                'category' => 'wars',
            ],
            'rules' => [
                'name' => 'Rules',
                'tag' => 'rules',
                'link' => 'support/rules',
                'color' => 'd7d7d7',
                'category' => 'support',
            ],
            'team' => [
                'name' => 'Team',
                'tag' => 'team',
                'link' => 'support/team',
                'color' => 'd7d7d7',
                'category' => 'support',
            ],
            'shop' => [
                'name' => 'Donate',
                'tag' => 'donate',
                'link' => 'payment',
                'color' => 'd7d7d7',
                'category' => 'shop',
            ],
            'premiumfeatures' => [
                'name' => 'Premium Features',
                'tag' => 'premiumfeatures',
                'link' => 'premiumfeatures',
                'color' => 'd7d7d7',
                'category' => 'shop',
            ],
        ];
        foreach($menu as $key => $value) {
            if($key == $currentPage) {
                $current = 1;
            } else {
                $current = 0;
            }
            $format[] = [
                'name' => $value['name'],
                'tag' => $value['tag'],
                'link' => $value['link'],
                'color' => $value['color'],
                'category' => $value['category'],
                'current' => $current,
            ];
        }
        return $format;
    }

    public static function getBase($title, $content, $currentPage = 'latestnews')
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();

        return View::render('pages/base', [
            'title' => $title . ' - ' . $websiteInfo->title . '',
            'content' => $content,
            'menu' => self::getMenu($currentPage),
            'activemenu' => $currentPage,
            'loginStatus' => self::getLogged(),
            'discord' => $websiteInfo->discord,
            'boostedcreature' => FunctionsServer::getBoostedCreature(),
            'boostedboss' => FunctionsServer::getBoostedBoss(),
            'playersonline' => FunctionsServer::getCountPlayersOnline(),
            'server_status' => FunctionsServer::getServerStatus(),
            'active_donates' => $websiteInfo->donates,
            'highscores' => FunctionsThemeBox::getHighscoresTop5(),
            'current_poll' => FunctionsThemeBox::getCurrentPoll(),
            'countdown' => FunctionsThemeBox::getCurrentCountdown()
        ]);
    }
}

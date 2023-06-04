<?php
/**
 * LastDeaths Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Utils\View;
use App\Controller\Pages\Base;
use App\DatabaseManager\Pagination;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Server;

class LastDeaths extends Base
{
    public static function getLastDeaths($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $totalAmount = EntityPlayer::getDeaths(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        $obPagination = new Pagination($totalAmount, $currentPage, 10);
        $select_deaths = EntityPlayer::getDeaths(null, 'time DESC', $obPagination->getLimit());

        while($obDeaths = $select_deaths->fetchObject(EntityPlayer::class)) {

            $countDeaths = (int)EntityPlayer::getDeaths(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            $select_player_death = EntityPlayer::getPlayer('id = "'.$obDeaths->player_id.'"')->fetchObject();

            $lasthit = ($obDeaths->is_player) ? $obDeaths->killed_by : $obDeaths->killed_by;
            $description = '<a href="'.URL.'/community/characters/'.$select_player_death->name.'">' . $select_player_death->name . '</a> died at level <b>' . $obDeaths->level . '</b> by ' . $lasthit;
            if($obDeaths->unjustified) {
                $description .= ' <span style="color: red; font-style: italic;">(unjustified)</span>';
            }
            $mostdamage = ($obDeaths->mostdamage_by !== $obDeaths->killed_by) ? true : false;
            if($mostdamage) {
                $mostdamage = ($obDeaths->mostdamage_is_player) ? $obDeaths->mostdamage_by : $obDeaths->mostdamage_by;
                $description .=  ' and by ' . $mostdamage;
                if($obDeaths->mostdamage_unjustified) {
                    $description .=  ' <span style="color: red; font-style: italic;">(unjustified)</span>';
                }
            } else {
                $description .=  " <b>(soloed)</b>";
            }
            $arrayDeaths[] = [
                'date' => date('M d Y, H:i:s', $obDeaths->time),
                'description' => $description,
            ];
        }

        return $arrayDeaths ?? [];
    }

    public static function viewLastDeaths($request)
    {
        $content = View::render('pages/lastdeaths', [
            'deaths' => self::getLastDeaths($request, $obPagination),
            'worlds' => Server::getWorlds(),
            'pagination' => self::getPagination($request, $obPagination),
        ]);
        return parent::getBase('Last Deaths', $content, $currentPage = 'lastdeaths');
    }
}

<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Controller\Admin\Players;
use App\DatabaseManager\Pagination;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Player;
use App\Model\Entity\Worlds as EntityWorlds;
use \App\Utils\View;
use App\Model\Functions\Server;
use App\Model\Functions\ServerStatus;

class Worlds extends Base{
    
    public static function getWorld($request)
    {
        $queryParams = $request->getQueryParams();
        $currentWorld = $queryParams['world'] ?? '';
        $world = [];

        $selectWorlds = EntityWorlds::getWorlds([ 'name' => $currentWorld]);
        while($obWorlds = $selectWorlds->fetchObject()){
            $world = [
                'id' => $obWorlds->id,
                'name' => $obWorlds->name
            ];
        }
        if(empty($world)){
            return false;
        }else{
            return $world;
        }
    }

    public static function getPlayersOnline($request,&$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $totalAmount = EntityWorlds::getPlayersOnline(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;

        $obPagination = new Pagination($totalAmount, $currentPage, 50);
        $select_PlayersOnline = EntityWorlds::getPlayersOnline(null, null, $obPagination->getLimit());

        while($obOnline = $select_PlayersOnline->fetchObject(EntityWorlds::class)){
            $playersInfo = EntityPlayer::getPlayer([ 'id' => $obOnline->player_id]);
            while($obPlayers = $playersInfo->fetchObject()){
                $players[] = [
                    'name' => $obPlayers->name,
                    'level' => $obPlayers->level,
                    'vocation' => Player::convertVocation($obPlayers->vocation),
                    'outfit' => Player::getOutfit($obPlayers->id)
                ];
            }
        }
        return $players ?? [];
    }

    public static function getWorlds($request)
    {
        $content = View::render('pages/worlds', [
            'current_world' => self::getWorld($request),
            'worlds' => Server::getWorlds(),
            'players_record' => Server::getRecordPlayersWorlds(),
            'online' => self::getPlayersOnline($request, $obPagination),
            'pagination' => self::getPagination($request, $obPagination),
            'boostedcreature' => Server::getBoostedCreature(),
        ]);
        return parent::getBase('Worlds', $content, 'worlds');
    }
}
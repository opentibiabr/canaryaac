<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Api;

use App\Model\Entity\Houses as EntityHouses;
use App\Model\Entity\Towns;
use App\Model\Functions\Player;
use Exception;

class Houses extends Api{

    public static function getTowns()
    {
        $selectTowns = Towns::getTowns();
        while($obTowns = $selectTowns->fetchObject()){
            $towns[] = [
                'town_id' => $obTowns->town_id,
                'name' => $obTowns->name
            ];
        }
        return $towns;
    }

    public static function getHouseList($request)
    {
        $queryParams = $request->getQueryParams();
        $page_Type = $queryParams['type'] ?? 'houses';
        $page_Town = $queryParams['town'] ?? 1;
        $page_State = $queryParams['state'] ?? 'all';
        $page_Order = $queryParams['order'] ?? 'name';
        $page_Details = $queryParams['page'] ?? '';

        if(isset($queryParams['town'])){
            $query_Town = 'town_id = '.$queryParams['town'];
        }else{
            $query_Town = '';
        }

        if(isset($queryParams['order'])){
            $query_Order = $queryParams['order'];
        }else{
            $query_Order = '';
        }

        if($page_Type == 'houses'){
            $title_Type = 'Houses and Flats';
        }elseif($page_Type == 'guildhalls'){
            $title_Type = 'Guildhalls';
        }

        $selectHouse = EntityHouses::getHouses(['town_id' => $query_Town], $query_Order);
        while($obHouse = $selectHouse->fetchObject()){
            $house['houses'] = [
                'owner' => $obHouse->owner,
                'paid' => $obHouse->paid,
                'warnings' => $obHouse->warnings,
                'name' => $obHouse->name,
                'rent' => $obHouse->rent,
                'town_id' => $obHouse->town_id,
                'bid' => $obHouse->bid,
                'bid_end' => $obHouse->bid_end,
                'last_bid' => $obHouse->last_bid,
                'highest_bidder' => $obHouse->highest_bidder,
                'size' => $obHouse->size,
                'guildid' => $obHouse->guildid,
                'beds' => $obHouse->beds
            ];
        }
        $house['current'] = [
            'title' => $title_Type ?? '',
            'world' => $queryParams['world'] ?? '',
            'town' => Player::convertTown($page_Town),
            'town_id' => $page_Town,
            'state' => $page_State,
            'type' => $page_Type,
            'order' => $page_Order,
            'page' => $page_Details
        ];

        if($obHouse == false){
            throw new Exception('Nenhuma house foi encontrada.', 404);
        }

        return $house ?? '';
    }

    public static function getHouses($request)
    {
        return self::getHouseList($request);
    }
    
}
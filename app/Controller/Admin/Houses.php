<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Houses as EntityHouse;
use App\Model\Functions\Server as FunctionsServer;
use App\Controller\Admin\Alert;

class Houses extends Base{

    public static function getHousesXml($request)
    {
        $postVars = $request->getPostVars();
        if (empty($postVars['localxml'])) {
            return self::getHouses($request);
        }
        if (empty($postVars['house_world'])) {
            return self::getHouses($request);
        }
        $localxml = filter_var($postVars['localxml'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_world = filter_var($postVars['house_world'], FILTER_SANITIZE_NUMBER_INT);
        $array = simplexml_load_file($localxml);

        $select_world = FunctionsServer::getWorldById($filter_world);
        if (empty($select_world)) {
            return self::getHouses($request);
        }

        foreach($array as $house){
            if($house['guildhall'] == true){
                $guild = 1;
            }else{
                $guild = 0;
            }
            $houses = [
                'name' => $house['name'],
                'rent' => $house['rent'],
                'town_id' => $house['townid'],
                'size' => $house['size'],
                'guildid' => $guild,
            ];
            
            if ($_ENV['MULTI_WORLD'] == 'true') {
                $houses['house_id'] = $house['houseid'];
                $houses['world_id'] = $select_world['id'];
            } else {
                $houses['id'] = $house['houseid'];
            }
            
            EntityHouse::insertHouses($houses);
        }
        $status = Alert::getSuccess('XML importado com sucesso!') ?? null;
        return self::getHouses($request, $status);
    }

    public static function deleteHouses($request)
    {
        $postVars = $request->getPostVars();
        $house_id = $postVars['houseid'];
        EntityHouse::deleteHouse([ 'id' => $house_id]);
        
        $status = Alert::getSuccess('House deletada com sucesso!') ?? null;

        return self::getHouses($request, $status);
    }

    public static function getAllHouses()
    {
        global $globalWorldId;
        FunctionsServer::getWorlds();
        $select = EntityHouse::getHouses();
        while($obAllHouses = $select->fetchObject()){
            $allHouses[] = [
                'id' => (int)$obAllHouses->id,
                'house_id' => ($_ENV['MULTI_WORLD'] == 'true' ? $obAllHouses->house_id : $obAllHouses->id),
                'world_id' => ($_ENV['MULTI_WORLD'] == 'true' ? $obAllHouses->world_id : ''),
                'world' => ($_ENV['MULTI_WORLD'] == 'true' || !empty($obAllHouses->world_id) ? FunctionsServer::getWorldById($obAllHouses->world_id) : FunctionsServer::getWorldById($globalWorldId)),
                'owner' => $obAllHouses->owner,
                'paid' => $obAllHouses->paid,
                'warnings' => $obAllHouses->warnings,
                'name' => $obAllHouses->name,
                'rent' => number_format($obAllHouses->rent, 0, '.', '.'),
                'town_id' => FunctionsServer::convertTown($obAllHouses->town_id),
                'bid' => $obAllHouses->bid,
                'bid_end' => $obAllHouses->bid_end,
                'last_bid' => $obAllHouses->last_bid,
                'highest_bidder' => $obAllHouses->highest_bidder,
                'size' => (int)$obAllHouses->size,
                'guildid' => (int)$obAllHouses->guildid,
                'beds' => (int)$obAllHouses->beds
            ];
        }
        return $allHouses ?? false;
    }

    public static function getHouses($request, $errorMessage = null)
    {
        $content = View::render('admin/modules/houses/index', [
            'status' => $errorMessage,
            'houses' => self::getAllHouses(),
            'worlds' => FunctionsServer::getWorlds(),
            'total_houses' => (int)EntityHouse::getHouses(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
            'total_houses_rented' => (int)EntityHouse::getHouses([ 'owner' != 0], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
        ]);

        return parent::getPanel('Houses', $content, 'houses');
    }

}
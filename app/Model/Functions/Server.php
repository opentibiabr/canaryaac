<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\DatabaseManager\Database;

define('BCRYPT_ROUNDS', 12);

class Server
{
    public static function convertGold($gold)
    {
        $count = strlen($gold);
        if ($count > 3) {
            return substr($gold, 0, $count - 3) . 'k';
        } elseif ($count > 6) {
            return substr($gold, 0, $count - 6) . 'kk';
        } elseif ($count > 9) {
            return substr($gold, 0, $count - 9) . 'kkk';
        } else {
            return $gold;
        }
    }

    public static function convertLocation($location_id)
    {
        $location = [
            '0' => 'All',
            '1' => 'Africa',
            '2' => 'Antarctica',
            '3' => 'Asia',
            '4' => 'Australia/Oceania',
            '5' => 'Europe',
            '6' => 'North America',
            '7' => 'South America'
        ];
        foreach($location as $key => $value) {
            if($key == $location_id) {
                return $value ?? '';
            }
        }
    }

    public static function convertLocationInitials($location_id)
    {
        $location = [
            '0' => 'All',
            '1' => 'EUR',
            '2' => 'USA',
            '3' => 'EUR',
            '4' => 'EUR',
            '5' => 'EUR',
            '6' => 'USA',
            '7' => 'BRA'
        ];
        foreach($location as $key => $value) {
            if($key == $location_id) {
                return $value ?? '';
            }
        }
    }

    public static function getLocationIcon($location_id)
    {
        $location = [
            '0' => '/global/content/option_server_location_all.png',
            '1' => '/global/content/option_server_location_', // no exists
            '2' => '/global/content/option_server_location_', // no exists
            '3' => '/global/content/option_server_location_', // no exists
            '4' => '/global/content/option_server_location_', // no exists
            '5' => '/global/content/option_server_location_eur.png',
            '6' => '/global/content/option_server_location_usa.png',
            '7' => '/global/content/option_server_location_bra.png'
        ];
        foreach($location as $key => $value) {
            if($key == $location_id) {
                return $value ?? '';
            }
        }
    }

    public static function convertPvpType($pvp_type)
    {
        $types = [
            '0' => 'Open PvP',
            '1' => 'Optional PvP',
            '2' => 'Hardcore PvP',
            '3' => 'Retro Open PvP',
            '4' => 'Retro Hardcore PvP'
        ];
        foreach($types as $key => $value) {
            if($key == $pvp_type) {
                return $value ?? '';
            }
        }
    }

    public static function convertPvpTypeToCreateAccount($pvp_type)
    {
        $types = [
            '0' => 'open',
            '1' => 'optional',
            '2' => 'hardcore',
            '3' => 'Retro Open',
            '4' => 'Retro Hardcore'
        ];
        foreach($types as $key => $value) {
            if($key == $pvp_type) {
                return $value ?? '';
            }
        }
    }

    public static function getPvpTypeIcon($pvp_type)
    {
        $types = [
            '0' => '/global/content/option_server_pvp_type_open.gif',
            '1' => '/global/content/option_server_pvp_type_optional.gif',
            '2' => '/global/content/option_server_pvp_type_hardcore.gif',
            '3' => '/global/content/option_server_pvp_type_retro.gif',
            '4' => '/global/content/option_server_pvp_type_retrohardcore.gif'
        ];
        foreach($types as $key => $value) {
            if($key == $pvp_type) {
                return $value ?? '';
            }
        }
    }

    public static function getWorldQuests()
    {
        $worldQuests = [];
        $selectWorldQuests = (new Database('global_storage'))->select();
        while($obWorldQuests = $selectWorldQuests->fetchObject()) {
            $selectQuests = (new Database('canary_worldquests'))->select([ 'storage' => $obWorldQuests->key]);
            while($obQuests = $selectQuests->fetchObject()) {
                $worldQuests = [
                    'name' => $obQuests->name,
                    'description' => $obQuests->description
                ];
            }
        }
        return $worldQuests;
    }

    public static function convertTown($town_id)
    {
        $selectTowns = (new Database('canary_towns'))->select([ 'town_id' => $town_id]);
        while($obTowns = $selectTowns->fetchObject()) {
            return $obTowns->name;
        }
    }

    public static function convertTransferType($transfertype_id)
    {
        $transfer = [
            '0' => 'blocked',
            '1' => 'released'
        ];
        foreach($transfer as $key => $value) {
            if($key == $transfertype_id) {
                return $value;
            }
        }
    }

    public static function convertPremiumType($premium_type)
    {
        $premium = [
            '0' => 'premium',
            '1' => 'free premium'
        ];
        foreach($premium as $key => $value) {
            if($key == $premium_type) {
                return $value;
            }
        }
    }

    public static function convertBattleEye($battleEye_id)
    {
        $battle_eye = [
            '0' => 'Not protected by BattlEye.',
            '1' => 'Protected by BattlEye for a while.',
            '2' => 'Protected by BattlEye since its release.'
        ];
        foreach($battle_eye as $key => $value) {
            if($key == $battleEye_id) {
                return $value;
            }
        }
    }

    public static function convertBattleEyeIcon($battleEye_id)
    {
        $battle_eye = [
            '0' => '/global/content/icon_battleye.gif',
            '1' => '/global/content/icon_battleyeinitial.gif',
            '2' => '/global/content/icon_battleyeinitial.gif'
        ];
        foreach($battle_eye as $key => $value) {
            if($key == $battleEye_id) {
                return $value;
            }
        }
    }

    public static function convertWorldType($worldtype_id)
    {
        $world_type = [
            '0' => 'Regular',
            '1' => 'Experimental'
        ];
        foreach($world_type as $key => $value) {
            if($key == $worldtype_id) {
                return $value;
            }
        }
    }

    public static function getRecordPlayers($world_id = 1)
    {
        $selectRecordPlayers = (new Database('server_config'))->select([ 'config' => "players_record"]);
        while($obRecordPlayers = $selectRecordPlayers->fetchObject()) {
            $playersRecord = [
                'record' => $obRecordPlayers->value,
                'timestamp' => $obRecordPlayers->timestamp
            ];
        }
        return $playersRecord ?? '';
    }

    public static function getRecordPlayersWorlds()
    {
        $players_record = 0;
        $selectRecordPlayers = (new Database('server_config'))->select([ 'config' => "players_record"]);
        while($obRecordPlayers = $selectRecordPlayers->fetchObject()) {
            $players_record = $players_record + $obRecordPlayers->value;
            $playersRecord = [
                'record' => $players_record,
                'timestamp' => $obRecordPlayers->timestamp
            ];
        }
        return $playersRecord ?? '';
    }

    public static function getPlayersOnline()
    {
        $select = (new Database('players_online'))->select();

        while($obPlayersOnline = $select->fetchObject()) {

            $selectPlayers = (new Database('players'))->select([ 'id' => $obPlayersOnline->player_id]);

            while($obPlayer = $selectPlayers->fetchObject()) {
                $playersOnline[] = [
                    'name' => $obPlayer->name,
                    'vocation' => $obPlayer->vocation,
                    'level' => $obPlayer->level
                ];
            }
        }
        return $playersOnline ?? '';
    }

    public static function getCountPlayersOnline()
    {
        $select = (new Database('players_online'))->select(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        return $select;
    }

    public static function getServerStatus()
    {
        $select = (new Database('players_online'))->select(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        if($select > 0) {
            $status = 'Server Online';
        } else {
            $status = 'Server Offline';
        }
        return $status;
    }

    public static function getMonsterImage($looktypeEx = 0, $looktype = 0, $lookaddons = 0, $lookbody = 0, $lookfeet = 0, $lookhead = 0, $looklegs = 0, $mount = 0)
    {
        if ($looktypeEx == 0) {
            $outfit = URL . OUTFITS_FOLDER. '/animoutfit.php?id='.$looktype.'&addons='.$lookaddons.'&head='.$lookhead.'&body='.$lookbody.'&legs='.$looklegs.'&feet='.$lookfeet.'&mount='.$mount.'';
        } else {
            $outfit = URL . "/resources/images/charactertrade/items/" . $looktypeEx . ".gif";
        }
        return $outfit;
    }

    public static function getBoostedBoss()
    {
        $boostedBoss = [];
        $results = (new Database('boosted_boss'))->select();
        while ($obBoostedBoss = $results->fetchObject()) {


            $boostedBoss = [
                'image_url' => self::getMonsterImage($obBoostedBoss->looktypeEx, $obBoostedBoss->looktype, $obBoostedBoss->lookaddons, $obBoostedBoss->lookbody, $obBoostedBoss->lookfeet, $obBoostedBoss->lookhead, $obBoostedBoss->looklegs),
                'looktypeEx' => $obBoostedBoss->looktypeEx,
                'looktype' => $obBoostedBoss->looktype,
                'lookfeet' => $obBoostedBoss->lookfeet,
                'looklegs' => $obBoostedBoss->looklegs,
                'lookhead' => $obBoostedBoss->lookhead,
                'lookbody' => $obBoostedBoss->lookbody,
                'lookaddons' => $obBoostedBoss->lookaddons,
                'lookmount' => $obBoostedBoss->lookmount,
                'date' => $obBoostedBoss->date,
                'boostname' => $obBoostedBoss->boostname,
                'raceid' => $obBoostedBoss->raceid
            ];
        }
        return $boostedBoss;
    }

    public static function getBoostedCreature()
    {
        $boostedCreature = [];
        $looktypeEx = 0;
        $results = (new Database('boosted_creature'))->select();
        while($obBoostedCreature = $results->fetchObject()) {
            $boostedCreature = [
                'image_url' => self::getMonsterImage($looktypeEx, $obBoostedCreature->looktype, $obBoostedCreature->lookaddons, $obBoostedCreature->lookbody, $obBoostedCreature->lookfeet, $obBoostedCreature->lookhead, $obBoostedCreature->looklegs),
                'looktype' => $obBoostedCreature->looktype,
                'lookfeet' => $obBoostedCreature->lookfeet,
                'looklegs' => $obBoostedCreature->looklegs,
                'lookhead' => $obBoostedCreature->lookhead,
                'lookbody' => $obBoostedCreature->lookbody,
                'lookaddons' => $obBoostedCreature->lookaddons,
                'lookmount' => $obBoostedCreature->lookmount,
                'date' => $obBoostedCreature->date,
                'boostname' => $obBoostedCreature->boostname,
                'raceid' => $obBoostedCreature->raceid
            ];
        }
        return $boostedCreature;
    }

    public static function getWorlds()
    {
        global $globalWorldId;
        $selectWorlds = (new Database('canary_worlds'))->select();

        while($obWorlds = $selectWorlds->fetchObject()) {
            $world[] = [
                'id' => $obWorlds->id,
                'name' => $obWorlds->name,
                'creation' => $obWorlds->creation,
                'creation_int' => strtotime($obWorlds->creation),
                'location' => self::convertLocation($obWorlds->location),
                'location_icon' => self::getLocationIcon($obWorlds->location),
                'location_initial' => self::convertLocationInitials($obWorlds->location),
                'pvp_type' => self::convertPvpType($obWorlds->pvp_type),
                'pvp_type_icon' => self::getPvpTypeIcon($obWorlds->pvp_type),
                'pvp_type_initial' => self::convertPvpTypeToCreateAccount($obWorlds->pvp_type),
                'premium_type' => self::convertPremiumType($obWorlds->premium_type),
                'transfer_type' => self::convertTransferType($obWorlds->transfer_type),
                'world_quests' => self::getWorldQuests(),
                'battle_eye' => self::convertBattleEye($obWorlds->battle_eye),
                'battle_eye_int' => $obWorlds->battle_eye,
                'battle_eye_icon' => self::convertBattleEyeIcon($obWorlds->battle_eye),
                'world_type' => self::convertWorldType($obWorlds->world_type),
                'players_record' => self::getRecordPlayers($obWorlds->id),
                'players_online' => self::getCountPlayersOnline(),
                'server_status' => self::getServerStatus(),
                'ipaddress' => $obWorlds->ip,
                'port' => $obWorlds->port,
            ];
        }

        if(count($world) === 1) {
            $globalWorldId = $world[0]['id'];
        }

        return $world;
    }

    public static function getWorldById($world_id)
    {
        $selectWorlds = (new Database('canary_worlds'))->select([ 'id' => $world_id]);

        while($obWorlds = $selectWorlds->fetchObject()) {
            $world = [
                'id' => $obWorlds->id,
                'name' => $obWorlds->name,
                'creation' => $obWorlds->creation,
                'location' => self::convertLocation($obWorlds->location),
                'location_icon' => self::getLocationIcon($obWorlds->location),
                'pvp_type' => self::convertPvpType($obWorlds->pvp_type),
                'pvp_type_icon' => self::getPvpTypeIcon($obWorlds->pvp_type),
                'premium_type' => self::convertPremiumType($obWorlds->premium_type),
                'transfer_type' => self::convertTransferType($obWorlds->transfer_type),
                'world_quests' => self::getWorldQuests(),
                'battle_eye' => self::convertBattleEye($obWorlds->battle_eye),
                'battle_eye_icon' => self::convertBattleEyeIcon($obWorlds->battle_eye),
                'world_type' => self::convertWorldType($obWorlds->world_type),
                'players_record' => self::getRecordPlayers($obWorlds->id),
                'players_online' => self::getCountPlayersOnline(),
                'server_status' => self::getServerStatus(),
                'ipaddress' => $obWorlds->ip,
                'port' => $obWorlds->port,
            ];
        }
        return $world ?? null;
    }
}

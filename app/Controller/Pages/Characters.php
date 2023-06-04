<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\Account;
use App\Utils\View;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Functions\Player;
use App\Model\Functions\Server;

const PSTRG_RESERVED_RANGE_START = 10000000;
const PSTRG_RESERVED_RANGE_SIZE = 10000000;

const PSTRG_OUTFITS_RANGE_START = (PSTRG_RESERVED_RANGE_START + 1000);
const PSTRG_OUTFITS_RANGE_SIZE = 500;

const PSTRG_MOUNTS_RANGE_START = (PSTRG_RESERVED_RANGE_START + 2001);
const PSTRG_MOUNTS_RANGE_SIZE = 10;
const PSTRG_MOUNTS_CURRENTMOUNT = (PSTRG_MOUNTS_RANGE_START + 10);

class Characters extends Base
{
    public static function isInRangeMounts($key, $range)
    {
        return ($key >= PSTRG_MOUNTS_RANGE_START) && (($key - PSTRG_MOUNTS_RANGE_START) <= PSTRG_MOUNTS_RANGE_SIZE);
    }

    public static function isInRangeOutfits($key, $range)
    {
        return ($key >= PSTRG_OUTFITS_RANGE_START) && (($key - PSTRG_OUTFITS_RANGE_START) <= PSTRG_OUTFITS_RANGE_SIZE);
    }

    public static function getPlayers($request, $name)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $postVars = $request->getPostVars();
        if (!empty($postVars['name'])) {
            $filterpost_name = filter_var($postVars['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $decodepost_name = urldecode($filterpost_name);
            if (!empty($decodepost_name)) {
                $request->getRouter()->redirect('/community/characters/' . $decodepost_name);
            }
        }
        $filter_name = filter_var($name, FILTER_SANITIZE_URL);
        $decode_name = urldecode($filter_name);
        if (!empty($decode_name)) {
            $obPlayer = EntityPlayer::getPlayer('name = "' . $decode_name . '"')->fetchObject();
            $dbAccount = Account::getAccount('id = "' . $obPlayer->account_id . '"')->fetchObject();
            if ($obPlayer == false) {
                $request->getRouter()->redirect('/community/characters');
            }
        }

        if (!empty($dbAccount)) {
            $outfits = self::getOutfits();
            $select_storage_outfit = EntityPlayer::getStorage('player_id = "' . $obPlayer->id . '"');
            while ($outfit = $select_storage_outfit->fetchObject()) {
                if (self::isInRangeOutfits($outfit->key, $outfit)) {
                    $index = $outfit->value >> 16;
                    $addons = $outfit->value & 0xFF;

                    $teste = Player::convertSex($obPlayer->sex);

                    if ($teste == 'male') {
                        $sex = 1;
                    } else {
                        $sex = 0;
                    }

                    if (isset($outfits[$index])) {
                        $outfitData = $outfits[$index];

                        // Verifica se o tipo do outfit é igual ao sexo do jogador
                        if ($outfitData['type'] == $sex) {
                            $outfitName = $outfitData['name'];
                            $looktype = $outfitData['looktype'];
                            $arrayOutfits[] = [
                                'looktype' => $looktype,
                                'name' => $outfitName,
                                'image_url' => Player::getOutfitImage($looktype, $addons, $obPlayer->lookbody, $obPlayer->lookfeet, $obPlayer->lookhead, $obPlayer->looklegs, $obPlayer->lookmountbody),
                                'lookbody' => $obPlayer->lookbody,
                                'lookfeet' => $obPlayer->lookfeet,
                                'lookhead' => $obPlayer->lookhead,
                                'looklegs' => $obPlayer->looklegs,
                            ];
                        }
                    }
                }
            }

            $mounts = self::getMounts();
            $select_storage_mount = EntityPlayer::getStorage('player_id = "' . $obPlayer->id . '"');
            while ($mount = $select_storage_mount->fetchObject()) {
                if (self::isInRangeMounts($mount->key, $mount)) {
                    $binaryValue = decbin($mount->value);
                    $binaryValue = strrev($binaryValue);

                    for ($i = 0; $i < strlen($binaryValue); $i++) {
                        if ($binaryValue[$i] == '1') {
                            $index = ($mount->key - PSTRG_MOUNTS_RANGE_START) * 31 + $i + 1;

                            if (isset($mounts[$index])) {
                                $mountData = $mounts[$index];
                                $clientId = $mountData['clientid'];
                                $mountName = $mountData['name'];
                                if (!isset($arrayMounts[$looktype])) {
                                    $arrayMounts[] = [
                                        'looktype' => Player::getOutfitImage($clientId),
                                        'name' => $mountName,
                                    ];
                                }
                            }
                        }
                    }
                }
            }

        }

        $MountsAndOutfits = [
            'outfits' => $arrayOutfits ?? '',
            'mounts' => $arrayMounts ?? ''
        ];

        if (!empty($dbAccount)) {
            $player['info'] = [
                'name' => $obPlayer->name,
                'group_id' => Player::convertGroup($obPlayer->group_id),
                'main' => $obPlayer->main,
                'online' => Player::isOnline($obPlayer->id),
                'creation' => date('M d Y, H:i:s', strtotime($dbAccount->creation)),
                'experience' => number_format($obPlayer->experience, 0, '.', '.'),
                'level' => $obPlayer->level,
                'vocation' => Player::convertVocation($obPlayer->vocation),
                'town_id' => Player::convertTown($obPlayer->town_id),
                'world' => Server::getWorldById($obPlayer->world),
                'sex' => Player::convertSex($obPlayer->sex),
                'marriage_status' => $obPlayer->marriage_status,
                'marriage_spouse' => $obPlayer->marriage_spouse,
                'bonus_rerolls' => $obPlayer->bonus_rerolls,
                'prey_wildcard' => $obPlayer->prey_wildcard,
                'task_points' => $obPlayer->task_points,
                'lookfamiliarstype' => $obPlayer->lookfamiliarstype,
                'premdays' => Player::convertPremy($obPlayer->account_id),
                'deletion' => $obPlayer->deletion,
            ];
            $player['stats'] = [
                'balance' => $obPlayer->balance,
                'health' => number_format($obPlayer->health, 0, '.', '.'),
                'healthmax' => number_format($obPlayer->healthmax, 0, '.', '.'),
                'mana' => number_format($obPlayer->mana, 0, '.', '.'),
                'manamax' => number_format($obPlayer->manamax, 0, '.', '.'),
                'manashield' => $obPlayer->manashield,
                'max_manashield' => $obPlayer->max_manashield,
                'soul' => $obPlayer->soul,
                'cap' => $obPlayer->cap,
                'skull' => $obPlayer->skull,
                'skulltime' => $obPlayer->skulltime,
                'lastlogout' => Player::convertLastLogin($obPlayer->lastlogin),
                'deletion' => $obPlayer->deletion,
                'achievements_points' => Player::getAchievementPoints($obPlayer->id)
            ];
            $player['outfit'] = [
                'image_url' => Player::getOutfitImage($obPlayer->looktype, $obPlayer->lookaddons, $obPlayer->lookbody, $obPlayer->lookfeet, $obPlayer->lookhead, $obPlayer->looklegs, $obPlayer->lookmountbody),
                'lookbody' => $obPlayer->lookbody,
                'lookfeet' => $obPlayer->lookfeet,
                'lookhead' => $obPlayer->lookhead,
                'looklegs' => $obPlayer->looklegs,
                'looktype' => $obPlayer->looktype,
                'lookaddons' => $obPlayer->lookaddons,
            ];
            $player['mount'] = [
                'lookmountbody' => $obPlayer->lookmountbody,
                'lookmountfeet' => $obPlayer->lookmountfeet,
                'lookmounthead' => $obPlayer->lookmounthead,
                'lookmountlegs' => $obPlayer->lookmountlegs,
            ];
            $player['blessings'] = [
                'blessings' => $obPlayer->blessings,
                'blessings1' => $obPlayer->blessings1,
                'blessings2' => $obPlayer->blessings2,
                'blessings3' => $obPlayer->blessings3,
                'blessings4' => $obPlayer->blessings4,
                'blessings5' => $obPlayer->blessings5,
                'blessings6' => $obPlayer->blessings6,
                'blessings7' => $obPlayer->blessings7,
                'blessings8' => $obPlayer->blessings8,
            ];
            $player['skills'] = [
                'onlinetime' => $obPlayer->onlinetime,
                'stamina' => $obPlayer->stamina,
                'xpboost_stamina' => $obPlayer->deletion,
                'xpboost_value' => $obPlayer->deletion,
                'maglevel' => $obPlayer->maglevel,
                'manaspent' => $obPlayer->manaspent,
                'skill_fist' => $obPlayer->skill_fist,
                'skill_fist_tries' => $obPlayer->skill_fist_tries,
                'skill_club' => $obPlayer->skill_club,
                'skill_club_tries' => $obPlayer->skill_club_tries,
                'skill_sword' => $obPlayer->skill_sword,
                'skill_sword_tries' => $obPlayer->skill_sword_tries,
                'skill_axe' => $obPlayer->skill_axe,
                'skill_axe_tries' => $obPlayer->skill_axe_tries,
                'skill_dist' => $obPlayer->skill_dist,
                'skill_dist_tries' => $obPlayer->skill_dist_tries,
                'skill_shielding' => $obPlayer->skill_shielding,
                'skill_shielding_tries' => $obPlayer->skill_shielding_tries,
                'skill_fishing' => $obPlayer->skill_fishing,
                'skill_fishing_tries' => $obPlayer->skill_fishing_tries,
                'skill_critical_hit_chance' => $obPlayer->skill_critical_hit_chance,
                'skill_critical_hit_chance_tries' => $obPlayer->skill_critical_hit_chance_tries,
                'skill_critical_hit_damage' => $obPlayer->skill_critical_hit_damage,
                'skill_critical_hit_damage_tries' => $obPlayer->skill_critical_hit_damage_tries,
                'skill_life_leech_chance' => $obPlayer->skill_life_leech_chance,
                'skill_life_leech_chance_tries' => $obPlayer->skill_life_leech_chance_tries,
                'skill_life_leech_amount' => $obPlayer->skill_life_leech_amount,
                'skill_life_leech_amount_tries' => $obPlayer->skill_life_leech_amount_tries,
                'skill_mana_leech_chance' => $obPlayer->skill_mana_leech_chance,
                'skill_mana_leech_chance_tries' => $obPlayer->skill_mana_leech_chance_tries,
                'skill_mana_leech_amount' => $obPlayer->skill_mana_leech_amount,
                'skill_mana_leech_amount_tries' => $obPlayer->skill_mana_leech_amount_tries,
                'skill_criticalhit_chance' => $obPlayer->skill_criticalhit_chance,
                'skill_criticalhit_damage' => $obPlayer->skill_criticalhit_damage,
                'skill_lifeleech_chance' => $obPlayer->skill_lifeleech_chance,
                'skill_lifeleech_amount' => $obPlayer->skill_lifeleech_amount,
                'skill_manaleech_chance' => $obPlayer->skill_manaleech_chance,
                'skill_manaleech_amount' => $obPlayer->skill_manaleech_amount,
            ];
            $player['allplayers'] = Player::getAllCharacters($obPlayer->account_id);
            $player['houses'] = Player::getHouse($obPlayer->id);
            $player['achievements'] = Player::getAchievements($obPlayer->id, 30000);
            $player['guild'] = Player::getGuildMember($obPlayer->id);
            $player['deaths'] = Player::getDeaths($obPlayer->id);
            $player['frags'] = Player::getFrags($obPlayer->id);
            $player['equipaments'] = Player::getEquipaments($obPlayer->id);
            $player['display'] = Player::getDisplay($obPlayer->id);
            $player['outfitsmounts'] = $MountsAndOutfits;

        } else {
            $player = false;
        }
        return $player;
    }

    public static function getMounts()
    {
        $xml_mounts = ($_ENV['SERVER_PATH'] . 'data/XML/mounts.xml');

        if (!file_exists($xml_mounts)) {
            return [];
        }

        $mountsXml = simplexml_load_file($xml_mounts);

        $mountsArray = [];
        foreach ($mountsXml->mount as $mount) {
            $id = intval($mount['id']);
            $clientid = intval($mount['clientid']);
            $name = (string) $mount['name'];

            $mountsArray[$id] = [
                'clientid' => $clientid,
                'name' => $name,
            ];
        }

        return $mountsArray;
    }

    public static function getOutfits()
    {
        $xml_outfits = ($_ENV['SERVER_PATH'] . 'data/XML/outfits.xml');

        if (!file_exists($xml_outfits)) {
            return [];
        }

        $outfitsXml = simplexml_load_file($xml_outfits);

        $outfitsArray = [];
        foreach ($outfitsXml->outfit as $outfit) {
            $looktype = intval($outfit['looktype']);
            $name = (string) $outfit['name'];
            $type = intval($outfit['type']); // Adicionando a variável $type aqui

            if (!isset($outfitsArray[$looktype])) {
                $outfitsArray[$looktype] = [
                    'looktype' => $looktype,
                    'name' => $name,
                    'type' => $type, // Adicionando 'type' ao array $outfitsArray
                ];
            }
        }

        return $outfitsArray;
    }


    public static function getCharacters($request, $name = null, $errorMessage = null)
    {
        $content = View::render('pages/community/characters', [
            'player' => self::getPlayers($request, $name),
            'boostedcreature' => Server::getBoostedCreature(),
            'status' => $errorMessage,
        ]);
        return parent::getBase('Characters', $content, 'characters');
    }
}

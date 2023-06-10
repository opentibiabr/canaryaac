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
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\Worlds;
use App\Model\Functions\Player;
use App\Model\Functions\Server;

class Players extends Base{

    public static function updatePlayer($request, $id)
    {
        $postVars = $request->getPostVars();

        if (isset($postVars['char_edit_skills'])) {
            $filter_char_magic = filter_var($postVars['char_magic'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_fist = filter_var($postVars['char_fist'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_club = filter_var($postVars['char_club'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_sword = filter_var($postVars['char_sword'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_axe = filter_var($postVars['char_axe'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_dist = filter_var($postVars['char_dist'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_shielding = filter_var($postVars['char_shielding'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_fishing = filter_var($postVars['char_fishing'], FILTER_SANITIZE_NUMBER_INT);

            EntityPlayer::updatePlayer([ 'id' => $id], [
                'maglevel' => $filter_char_magic,
                'skill_fist' => $filter_char_fist,
                'skill_club' => $filter_char_club,
                'skill_sword' => $filter_char_sword,
                'skill_axe' => $filter_char_axe,
                'skill_dist' => $filter_char_dist,
                'skill_shielding' => $filter_char_shielding,
                'skill_fishing' => $filter_char_fishing,
            ]);
            $status = Alert::getSuccess('Character successfully updated.');
            return self::viewPlayer($request, $id, $status);
        }

        if (isset($postVars['char_edit_info'])) {
            $filter_char_name = filter_var($postVars['char_name'], FILTER_SANITIZE_SPECIAL_CHARS);

            $filter_char_health = filter_var($postVars['char_health'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_healthmax = filter_var($postVars['char_healthmax'], FILTER_SANITIZE_NUMBER_INT);

            $filter_char_mana = filter_var($postVars['char_mana'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_manamax = filter_var($postVars['char_manamax'], FILTER_SANITIZE_NUMBER_INT);

            $filter_char_level = filter_var($postVars['char_level'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_experience = filter_var($postVars['char_experience'], FILTER_SANITIZE_NUMBER_INT);

            $filter_char_soul = filter_var($postVars['char_soul'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_cap = filter_var($postVars['char_cap'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_balance = filter_var($postVars['char_balance'], FILTER_SANITIZE_NUMBER_INT);

            $filter_char_vocation = filter_var($postVars['char_vocation'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_sex = filter_var($postVars['char_sex'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_world = filter_var($postVars['char_world'], FILTER_SANITIZE_NUMBER_INT);
            $filter_char_town = filter_var($postVars['char_town'], FILTER_SANITIZE_NUMBER_INT);

            EntityPlayer::updatePlayer([ 'id' => $id], [
                'name' => $filter_char_name,
                'health' => $filter_char_health,
                'healthmax' => $filter_char_healthmax,
                'mana' => $filter_char_mana,
                'manamax' => $filter_char_manamax,
                'level' => $filter_char_level,
                'experience' => $filter_char_experience,
                'soul' => $filter_char_soul,
                'cap' => $filter_char_cap,
                'balance' => $filter_char_balance,
                'vocation' => $filter_char_vocation,
                'sex' => $filter_char_sex,
                'world' => $filter_char_world,
                'town_id' => $filter_char_town,
            ]);
            $status = Alert::getSuccess('Character successfully updated.');
            return self::viewPlayer($request, $id, $status);
        }
    }

    public static function getAllPlayers()
    {
        $select = EntityPlayer::getPlayer();
        while($obAllPlayers = $select->fetchObject()){
            $allPlayers[] = [
                'id' => $obAllPlayers->id,
                'account_id' => $obAllPlayers->account_id,
                'name' => $obAllPlayers->name,
                'level' => (int)$obAllPlayers->level,
                'vocation' => Player::convertVocation($obAllPlayers->vocation),
                'group' => Player::convertGroup($obAllPlayers->group_id),
                'online' => Player::isOnline($obAllPlayers->id),
                'outfit' => Player::getOutfit($obAllPlayers->id),
                'skull' => Player::getSkull($obAllPlayers->id),
                'premdays' => Player::getPremDays($obAllPlayers->account_id),
                'premium' => Player::convertPremy($obAllPlayers->account_id),
                'coins' => Player::getCoins($obAllPlayers->account_id),
                'main' => $obAllPlayers->main
            ];
        }
        return $allPlayers;
    }

    public static function viewPlayer($request, $id, $status = null)
    {
        $select_town = Worlds::getTowns();
        while ($town = $select_town->fetchObject()) {
            $arrayTown[] = [
                'id' => $town->town_id,
                'name' => $town->name,
            ];
        }

        $character = EntityPlayer::getPlayer([ 'id' => $id], null, '1')->fetchObject();
        $content = View::render('admin/modules/players/view', [
            'status' => $status,
            'worlds' => Server::getWorlds(),
            'towns' => $arrayTown,
            'player_id' => $id,
            'account_id' => $character->account_id,
            'main' => $character->main,
            'sex' => Player::convertSex($character->sex),
            'world' => Player::convertWorld($character->world),
            'health' => $character->health,
            'healthmax' => $character->healthmax,
            'mana' => $character->mana,
            'manamax' => $character->manamax,
            'maglevel' => $character->maglevel,
            'skill_fist' => $character->skill_fist,
            'skill_club' => $character->skill_club,
            'skill_sword' => $character->skill_sword,
            'skill_axe' => $character->skill_axe,
            'skill_dist' => $character->skill_dist,
            'skill_shielding' => $character->skill_shielding,
            'skill_fishing' => $character->skill_fishing,
            'level' => $character->level,
            'experience' => $character->experience,
            'cap' => $character->cap,
            'soul' => $character->soul,
            'balance' => $character->balance,
            'isreward' => $character->isreward,
            'istutorial' => $character->istutorial,
            'town' => Server::convertTown($character->town_id),
            'name' => $character->name,
            'level' => $character->level,
            'vocation' => Player::convertVocation($character->vocation),
            'group' => Player::convertGroup($character->group_id),
            'outfit' => Player::getOutfit($character->id),
            'online' => Player::isOnline($character->id)
        ]);
        return parent::getPanel('Player #'.$id, $content, 'players');
    }

    public static function getPlayers($request)
    {
        $content = View::render('admin/modules/players/index', [
            'players' => self::getAllPlayers()
        ]);
        return parent::getPanel('Players', $content, 'players');
    }

}
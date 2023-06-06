<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Api;

use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Player;
use Exception;

class Characters extends Api{

    public static function searchCharacterDiscordBOT($request)
    {
        $postVars = $request->getPostVars();
        $queryParams = $request->getQueryParams();
        
        if(empty($postVars['name'])){
            throw new Exception('Nenhum character foi encontrado.', 404);
        }
        $filter_name = filter_var($postVars['name'], FILTER_SANITIZE_SPECIAL_CHARS);

        $player = EntityPlayer::getPlayer(['name LIKE' => $filter_name], null, 1)->fetchObject(EntityPlayer::class);
        $characters = [
            'outfit' => Player::getOutfit($player->id),
            'name' => $player->name,
            'level' => $player->level,
            'vocation' => Player::convertVocation($player->vocation),
            'status' => Player::isOnline($player->id)
        ];
        if(empty($characters)){
            throw new Exception('Nenhum character foi encontrado.', 404);
        }
        return $characters;
    }

    public static function searchCharacter($request)
    {
        $postVars = $request->getPostVars();
        if(empty($postVars['name'])){
            throw new Exception('Nenhum character foi encontrado.', 404);
        }
        $filter_name = filter_var($postVars['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $select_character = EntityPlayer::getPlayer(['name LIKE' => $filter_name], null, 10);
        while($player = $select_character->fetchObject(EntityPlayer::class)){
            $characters[] = [
                'outfit' => Player::getOutfit($player->id),
                'name' => $player->name,
                'level' => $player->level,
                'vocation' => Player::convertVocation($player->vocation),
            ];
        }
        if(empty($characters)){
            throw new Exception('Nenhum character foi encontrado.', 404);
        }
        return $characters;
    }

    public static function getPlayer($request)
    {
        // recebe os gets da url
        $queryParams = $request->getQueryParams();

        // pega informações do player (usando LIKE)
        $results = EntityPlayer::getPlayerLike(null, ['name' => $queryParams['name']]);


        // trás informações do player
        $obPlayer = $results->fetchObject(EntityPlayer::class);

        if($obPlayer == false){
            throw new Exception('Nenhum character foi encontrado.', 404);
        }

        $player['info'] = [
            'name' => $obPlayer->name,
            'group_id' => Player::convertGroup($obPlayer->group_id),
            'main' => (int)$obPlayer->main,
            'comment' => $obPlayer->comment,
            'online' => Player::isOnline($obPlayer->id),
            'experience' => (int)$obPlayer->experience,
            'level' => (int)$obPlayer->level,
            'vocation' => Player::convertVocation($obPlayer->vocation),
            'town_id' => (int)$obPlayer->town_id,
            'town_name' => Player::convertTown($obPlayer->town_id),
            'world' => Player::convertWorld($obPlayer->world),
            'sex' => Player::convertSex($obPlayer->sex),
            'marriage_status' => (int)$obPlayer->marriage_status,
            'marriage_spouse' => $obPlayer->marriage_spouse,
            'bonus_rerolls' => (int)$obPlayer->bonus_rerolls,
            'prey_wildcard' => (int)$obPlayer->prey_wildcard,
            'task_points' => (int)$obPlayer->task_points,
            'lookfamiliarstype' => (int)$obPlayer->lookfamiliarstype,
            'premdays' => Player::convertPremy($obPlayer->account_id),
        ];
        $player['stats'] = [
            'balance' => (int)$obPlayer->balance,
            'health' => (int)$obPlayer->health,
            'healthmax' => (int)$obPlayer->healthmax,
            'mana' => (int)$obPlayer->mana,
            'manamax' => (int)$obPlayer->manamax,
            'manashield' => (int)$obPlayer->manashield,
            'max_manashield' => (int)$obPlayer->max_manashield,
            'soul' => (int)$obPlayer->soul,
            'cap' => (int)$obPlayer->cap,
            'skull' => (int)$obPlayer->skull,
            'skulltime' => (int)$obPlayer->skulltime,
            'lastlogout' => Player::convertLastLogin($obPlayer->lastlogout),
            'deletion' => $obPlayer->deletion,
            'achievements_points' => Player::getAchievementPoints($obPlayer->id)
        ];
        $player['outfit'] = [
            'image_url' => Player::getOutfitImage($obPlayer->looktype, $obPlayer->lookaddons, $obPlayer->lookbody, $obPlayer->lookfeet, $obPlayer->lookhead, $obPlayer->looklegs, $obPlayer->lookmountbody),
            'lookbody' => (int)$obPlayer->lookbody,
            'lookfeet' => (int)$obPlayer->lookfeet,
            'lookhead' => (int)$obPlayer->lookhead,
            'looklegs' => (int)$obPlayer->looklegs,
            'looktype' => (int)$obPlayer->looktype,
            'lookaddons' => (int)$obPlayer->lookaddons,
        ];
        $player['mount'] = [
            'lookmountbody' => (int)$obPlayer->lookmountbody,
            'lookmountfeet' => (int)$obPlayer->lookmountfeet,
            'lookmounthead' => (int)$obPlayer->lookmounthead,
            'lookmountlegs' => (int)$obPlayer->lookmountlegs,
        ];
        $player['blessings'] = [
            'blessings' => (int)$obPlayer->blessings,
            'blessings1' => (int)$obPlayer->blessings1,
            'blessings2' => (int)$obPlayer->blessings2,
            'blessings3' => (int)$obPlayer->blessings3,
            'blessings4' => (int)$obPlayer->blessings4,
            'blessings5' => (int)$obPlayer->blessings5,
            'blessings6' => (int)$obPlayer->blessings6,
            'blessings7' => (int)$obPlayer->blessings7,
            'blessings8' => (int)$obPlayer->blessings8,
        ];
        $player['skills'] = [
            'onlinetime' => (int)$obPlayer->onlinetime,
            'stamina' => (int)$obPlayer->stamina,
            'xpboost_stamina' => (int)$obPlayer->deletion,
            'xpboost_value' => (int)$obPlayer->deletion,
            'maglevel' => (int)$obPlayer->maglevel,
            'manaspent' => (int)$obPlayer->manaspent,
            'skill_fist' => (int)$obPlayer->skill_fist,
            'skill_fist_tries' => (int)$obPlayer->skill_fist_tries,
            'skill_club' => (int)$obPlayer->skill_club,
            'skill_club_tries' => (int)$obPlayer->skill_club_tries,
            'skill_sword' => (int)$obPlayer->skill_sword,
            'skill_sword_tries' => (int)$obPlayer->skill_sword_tries,
            'skill_axe' => (int)$obPlayer->skill_axe,
            'skill_axe_tries' => (int)$obPlayer->skill_axe_tries,
            'skill_dist' => (int)$obPlayer->skill_dist,
            'skill_dist_tries' => (int)$obPlayer->skill_dist_tries,
            'skill_shielding' => (int)$obPlayer->skill_shielding,
            'skill_shielding_tries' => (int)$obPlayer->skill_shielding_tries,
            'skill_fishing' => (int)$obPlayer->skill_fishing,
            'skill_fishing_tries' => (int)$obPlayer->skill_fishing_tries,
        ];
        $player['allplayers'] = Player::getAllCharacters($obPlayer->account_id);
        $player['houses'] = Player::getHouse($obPlayer->id);
        $player['achievements'] = Player::getAchievements($obPlayer->id, 30000);
        $player['guild'] = Player::getGuildMember($obPlayer->id);
        $player['deaths'] = Player::getDeaths($obPlayer->id);
        $player['equipaments'] = Player::getEquipaments($obPlayer->id);
        $player['hidden'] = [
            'quests' => false,
            'achievements' => false,
            'deaths' => false,
            'details' => false,
            'characters' => false,
            'balance' => false,
            'house' => false,
            'comment' => false,
        ];
        return $player;
    }

    /**
     * Método responsável por retornar os detalhes da API
     *
     * @param Request $request
     * @return array
     */
    public static function getCharacters($request)
    {
        return self::getPlayer($request);
    }
    
}
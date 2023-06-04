<?php
/**
 * CreateCharacter Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Functions\Player;
use App\Model\Functions\Server;
use App\Session\Admin\Login as SessionAdminLogin;
use App\Model\Entity\CreateAccount as EntityCreateAccount;
use App\Model\Entity\Worlds as EntityWorlds;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\ServerConfig as EntityServerConfig;

class CreateCharacter extends Base
{
    public static function getWorlds()
    {
        $allWorlds = EntityWorlds::getWorlds();
        while($world = $allWorlds->fetchObject()) {
            $arrayAllWorlds[] = [
                'id' => $world->id,
                'name' => $world->name,
                'location' => $world->location,
                'pvp_type' => $world->pvp_type,
                'premium_type' => $world->premium_type,
                'transfer_type' => $world->transfer_type,
                'battle_eye' => $world->battle_eye,
                'world_type' => $world->world_type
            ];
        }
        return $arrayAllWorlds;
    }

    public static function getActiveVocation()
    {
        $activeVocations = EntityServerConfig::getInfoWebsite()->fetchObject();
        $active = $activeVocations->player_voc;
        return $active;
    }

    public static function insertCharacter($request)
    {
        if(SessionAdminLogin::isLogged() == false) {
            return self::viewCreateCharacter($request);
        }

        $AccountId = SessionAdminLogin::idLogged();
        $ServerConfig = EntityServerConfig::getInfoWebsite()->fetchObject();
        $countPlayers = (int)EntityPlayer::getPlayer('account_id = "'.$AccountId.'"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
        if($countPlayers >= $ServerConfig->player_max) {
            return self::viewCreateCharacter($request, 'Your account has reached the character limit.');
        }

        $postVars = $request->getPostVars();
        $LoggedId = SessionAdminLogin::idLogged();

        if(empty($postVars['name'])) {
            return self::viewCreateCharacter($request, 'Set a name for the character.');
        }
        if(empty($postVars['world'])) {
            return self::viewCreateCharacter($request, 'Select a World.');
        }

        $character_name = filter_var($postVars['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $character_vocation = filter_var($postVars['vocation'], FILTER_SANITIZE_NUMBER_INT);
        $character_world = filter_var($postVars['world'], FILTER_SANITIZE_NUMBER_INT);
        $character_tutorial = $postVars['tutorial'] ?? '';

        $CountName = strlen($character_name);
        if($CountName < 5) {
            return self::viewCreateCharacter($request, 'The name must be at least 5 characters long.');
        }
        if($CountName > 29) {
            return self::viewCreateCharacter($request, 'The name must be at least 29 characters long.');
        }
        $verifyPlayerName = EntityPlayer::getPlayer('name = "'.$character_name.'"')->fetchObject();
        if($verifyPlayerName == true) {
            return self::viewCreateCharacter($request, 'This character name is already being used.');
        }

        $character_sex = filter_var($postVars['sex'], FILTER_SANITIZE_NUMBER_INT);
        if($character_sex > 2) {
            return self::viewCreateCharacter($request, 'Please select a valid gender.');
        }
        if ($character_sex == 2) {
            $character_sex = 0;
        }

        $activeVocations = EntityServerConfig::getInfoWebsite()->fetchObject();
        if($activeVocations->player_voc == 1) {
            if (empty($character_vocation)) {
                return self::viewCreateCharacter($request, 'Choose a vocation for the character.');
            }

            $verifyVocation = EntityCreateAccount::getPlayerSamples('vocation = "'.$character_vocation.'"')->fetchObject();
            if($verifyVocation == false) {
                return self::viewCreateCharacter($request, 'Please select your character vocation!');
            }
        } else {
            $character_vocation = 0;
        }

        $selectWorlds = EntityWorlds::getWorlds('id = "'.$character_world.'"')->fetchObject();
        if($selectWorlds == false) {
            return self::viewCreateCharacter($request, 'Invalid world.');
        }
        if(empty($character_tutorial)) {
            $character_tutorial = 0;
        }

        if(self::getActiveVocation() == 0) {
            $character_vocation = 0;
        }
        $playerSample = EntityCreateAccount::getPlayerSamples('vocation = "'.$character_vocation.'"')->fetchObject();

        $character = [
            'name' => $character_name,
            'group_id' => '1',
            'account_id' => $LoggedId,
            'main' => '0',
            'level' => $playerSample->level,
            'vocation' => $playerSample->vocation,
            'health' => $playerSample->health,
            'healthmax' => $playerSample->healthmax,
            'experience' => $playerSample->experience,
            'lookbody' => $playerSample->lookbody,
            'lookfeet' => $playerSample->lookfeet,
            'lookhead' => $playerSample->lookhead,
            'looklegs' => $playerSample->looklegs,
            'looktype' => $playerSample->looktype,
            'lookaddons' => $playerSample->lookaddons,
            'maglevel' => $playerSample->maglevel,
            'mana' => $playerSample->mana,
            'manamax' => $playerSample->manamax,
            'manaspent' => $playerSample->manaspent,
            'soul' => $playerSample->soul,
            'town_id' => $playerSample->town_id,
            'world' => $character_world,
            'posx' => $playerSample->posx,
            'posy' => $playerSample->posy,
            'posz' => $playerSample->posz,
            'cap' => $playerSample->cap,
            'sex' => $character_sex,
            'balance' => $playerSample->balance,
            'istutorial' => $character_tutorial,
        ];
        EntityCreateAccount::createCharacter($character);

        $content = View::render('pages/account/createcharacter_confirm', [
            'character_name' => $character_name,
            'character_sex' => Player::convertSex($character_sex),
            'world_name' => $selectWorlds->name,
            'world_pvptype' => Server::convertPvpType($selectWorlds->pvp_type),
            'world_location' => Server::convertLocation($selectWorlds->location),
        ]);
        return parent::getBase('Create Account', $content);
    }

    public static function viewCreateCharacter($request, $errorMessage = null)
    {
        $content = View::render('pages/account/createcharacter', [
            'worlds' => self::getWorlds(),
            'status' => $errorMessage,
            'activevoc' => self::getActiveVocation(),
        ]);
        return parent::getBase('Account Management', $content);
    }
}

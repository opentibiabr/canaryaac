<?php
/**
 * CharacterEdit Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Controller\Pages\Base;
use \App\Utils\View;
use App\Model\Entity\Achievements as EntityAchievements;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Player as FunctionsPlayer;
use App\Model\Functions\Server;
use App\Session\Admin\Login as SessionAdminLogin;

class CharacterEdit extends Base{

    public static function updateCharacter($request, $name)
    {
        if(SessionAdminLogin::isLogged() == true){
            $AccountId = SessionAdminLogin::idLogged();
            $postVars = $request->getPostVars();

            $filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
            $filt = urldecode($filter_name);
            $select_player = EntityPlayer::getPlayer('name = "'.$filt.'"')->fetchObject();
            if($select_player->deletion == 1){
                $request->getRouter()->redirect('/account');
            }

            $filter_account = filter_var($postVars['hide_account'], FILTER_SANITIZE_NUMBER_INT);
            if(empty($filter_account)){
                $filter_account = 0;
            }
            $filter_outfit = filter_var($postVars['hide_outfit'], FILTER_SANITIZE_NUMBER_INT);
            if(empty($filter_outfit)){
                $filter_outfit = 0;
            }
            $filter_inventory = filter_var($postVars['hide_inventory'], FILTER_SANITIZE_NUMBER_INT);
            if(empty($filter_inventory)){
                $filter_inventory = 0;
            }
            $filter_health_mana = filter_var($postVars['hide_healthmana'], FILTER_SANITIZE_NUMBER_INT);
            if(empty($filter_health_mana)){
                $filter_health_mana = 0;
            }
            $filter_skills = filter_var($postVars['hide_skills'], FILTER_SANITIZE_NUMBER_INT);
            if(empty($filter_skills)){
                $filter_skills = 0;
            }
            $filter_bonus = filter_var($postVars['hide_bonus'], FILTER_SANITIZE_NUMBER_INT);
            if(empty($filter_bonus)){
                $filter_bonus = 0;
            }
            $filter_comment = filter_var($postVars['comment'], FILTER_SANITIZE_SPECIAL_CHARS);
            if(empty($filter_comment)){
                $filter_comment = "";
            }

            $filter_hidden = filter_var($postVars['accountvisible'], FILTER_SANITIZE_NUMBER_INT);
            if($filter_hidden > 1){
                $request->getRouter()->redirect('/account');
            }
            
            if($select_player->account_id == $AccountId){

                $arrayCharacterDisplay = [
                    'player_id' => $select_player->id,
                    'account' => $filter_account,
                    'outfit' => $filter_outfit,
                    'inventory' => $filter_inventory,
                    'health_mana' => $filter_health_mana,
                    'skills' => $filter_skills,
                    'bonus' => $filter_bonus,
                    'comment' => $filter_comment,
                ];

                $check_exists_display = EntityPlayer::getDisplay('player_id = "'.$select_player->id.'"')->fetchObject();
                if (empty($check_exists_display)) {
                    EntityPlayer::insertDisplay($arrayCharacterDisplay);
                    return self::viewCharacterEdit($request, $name, 'Updated successfully.');
                } else {
                    EntityPlayer::updateDisplay('player_id = "'.$select_player->id.'"', [
                        'account' => $filter_account,
                        'outfit' => $filter_outfit,
                        'inventory' => $filter_inventory,
                        'health_mana' => $filter_health_mana,
                        'skills' => $filter_skills,
                        'bonus' => $filter_bonus,
                        'comment' => $filter_comment,
                    ]);

                    return self::viewCharacterEdit($request, $name, 'Updated successfully.');
                }
                $request->getRouter()->redirect('/account/character/'.$name.'/edit');
            }
        }
        $request->getRouter()->redirect('/account');
    }

    public static function getCharacterEdit($request, $name)
    {
        if(SessionAdminLogin::isLogged() == true){
            $AccountId = SessionAdminLogin::idLogged();
            $filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
            $filt = urldecode($filter_name);

            $player = [];
            $select_player = EntityPlayer::getPlayer('name = "'.$filt.'"')->fetchObject();
            if($select_player->deletion == 1){
                $request->getRouter()->redirect('/account');
            }
            if($select_player->account_id == $AccountId){
                $player = [
                    'name' => $select_player->name,
                    'sex' => FunctionsPlayer::convertSex($select_player->sex),
                    'world' => Server::getWorldById($select_player->world),
                    'hidden' => self::getCharacterDisplay($request, $name),
                    'guild' => FunctionsPlayer::getGuildMember($select_player->id),
                ];
            }
        }
        return $player;
    }

    public static function getCharacterDisplay($request, $name)
    {
        $filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_nameurl = urldecode($filter_name);
        $select_player = EntityPlayer::getPlayer('name = "'.$filter_nameurl.'"')->fetchObject();
        if($select_player->deletion == 1){
            $request->getRouter()->redirect('/account');
        }

        $select_displayCharacter = FunctionsPlayer::getDisplay($select_player->id);
        return $select_displayCharacter ?? [];
    }

    public static function getAchievementsPlayer($request, $name)
    {
        $filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_nameurl = urldecode($filter_name);
        $select_player = EntityPlayer::getPlayer('name = "'.$filter_nameurl.'"')->fetchObject();
        if($select_player->deletion == 1){
            $request->getRouter()->redirect('/account');
        }

        $select_AllAchievements = EntityAchievements::getAchievements();
        while ($achievement = $select_AllAchievements->fetchObject()) {

            $achievementStatus = FunctionsPlayer::getPlayerStorage($select_player->id, $achievement->storage);
            $arrayAchievement[] = [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'description' => $achievement->description,
                'grade' => $achievement->grade,
                'points' => $achievement->points,
                'storage' => $achievement->storage,
                'secret' => $achievement->secret,
                'status' => $achievementStatus
            ];
        }
        return $arrayAchievement ?? '';
    }

    public static function viewCharacterEdit($request, $name, $status = null)
    {
        $content = View::render('pages/account/characteredit', [
            'player' => self::getCharacterEdit($request, $name),
            'achievements' => self::getAchievementsPlayer($request, $name),
            'total_secretachievements' => (int)EntityAchievements::getAchievements('secret = "1"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd,
            'status' => $status,
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }

}
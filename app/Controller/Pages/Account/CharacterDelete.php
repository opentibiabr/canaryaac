<?php
/**
 * CharacterDelete Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use \App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Player;
use App\Model\Functions\Server;
use App\Session\Admin\Login as SessionAdminLogin;

class CharacterDelete extends Base{

    public static function deleteCharacter($request, $name)
    {
        $AccountId = SessionAdminLogin::idLogged();

        $postVars = $request->getPostVars();
        $password = $postVars['password'];
        $convert_password = sha1($password);
        $filt = urldecode($name);

        if(SessionAdminLogin::isLogged() == true){
            $selectPlayer = EntityPlayer::getPlayer([ 'name' => $filt])->fetchObject();
            if($selectPlayer->deletion == 1){
                $request->getRouter()->redirect('/account');
            }
            if($selectPlayer->account_id == $AccountId){
                $selectAccount = EntityPlayer::getAccount([ 'id' => $selectPlayer->account_id])->fetchObject();
                if($selectAccount->password == $convert_password){
                    EntityPlayer::updatePlayer('id = "'.$selectPlayer->id.'"', [
                        'deletion' => 1
                    ]);
                    $request->getRouter()->redirect('/account');
                }
            }
        }
        $request->getRouter()->redirect('/account');
    }

    public static function getCharacterDelete($request, $name)
    {
        if(SessionAdminLogin::isLogged() == true){
            $AccountId = SessionAdminLogin::idLogged();
            $filter_name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
            $filt = urldecode($filter_name);

            $player = [];
            $selectPlayer = EntityPlayer::getPlayer([ 'name' => $filt])->fetchObject();
            if($selectPlayer->deletion == 1){
                $request->getRouter()->redirect('/account');
            }
            if($selectPlayer->account_id == $AccountId){
                $player = [
                    'name' => $selectPlayer->name,
                    'sex' => Player::convertSex($selectPlayer->sex),
                    'world' => Server::getWorldById($selectPlayer->world),
                    'comment' => $selectPlayer->comment,
                    'hidden' => $selectPlayer->hidden,
                ];
            }
        }
        return $player;
    }

    public static function viewCharacterDelete($request, $name)
    {
        $content = View::render('pages/account/characterdelete', [
            'player' => self::getCharacterDelete($request, $name)
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }

}
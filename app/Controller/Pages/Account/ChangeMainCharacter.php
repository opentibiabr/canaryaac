<?php
/**
 * ChangeMainCharacter Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Player;
use App\Model\Functions\Player as FunctionsPlayer;
use App\Session\Admin\Login as SessionAdminLogin;

class ChangeMainCharacter extends Base
{
    public static function viewChangedMain($request)
    {
        $idLogged = SessionAdminLogin::idLogged();
        $postVars = $request->getPostVars();
        if (empty($postVars['selected_main'])) {
            return self::viewChangeMain();
        }
        $filter_main = filter_var($postVars['selected_main'], FILTER_SANITIZE_SPECIAL_CHARS);
        $select_character = Player::getPlayer('account_id = "' . $idLogged . '" AND name = "' . $filter_main . '"')->fetchObject();
        $current_main = Player::getPlayer('account_id = "' . $idLogged . '" AND main = "1"')->fetchObject();
        if (empty($select_character)) {
            return self::viewChangeMain();
        }
        if ($select_character->main == 1) {
            return self::viewChangeMain();
        }
        if ($select_character->deletion == 1) {
            return self::viewChangeMain();
        }

        Player::updatePlayer('id = "' . $current_main->id . '"', [
            'main' => '0',
        ]);
        Player::updatePlayer('id = "' . $select_character->id . '"', [
            'main' => '1',
        ]);

        $content = View::render('pages/account/changemain_changed', [
            'selected' => $filter_main,
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }

    public static function viewConfirmCharacter($request)
    {
        $idLogged = SessionAdminLogin::idLogged();
        $postVars = $request->getPostVars();
        if (empty($postVars['maincharacter'])) {
            return self::viewChangeMain();
        }
        $filter_main = filter_var($postVars['maincharacter'], FILTER_SANITIZE_SPECIAL_CHARS);
        $select_character = Player::getPlayer('account_id = "' . $idLogged . '" AND name = "' . $filter_main . '"')->fetchObject();
        if (empty($select_character)) {
            return self::viewChangeMain();
        }
        if ($select_character->main == 1) {
            return self::viewChangeMain();
        }
        if ($select_character->deletion == 1) {
            return self::viewChangeMain();
        }
        $content = View::render('pages/account/changemain_confirm', [
            'selected' => $filter_main,
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }

    public static function viewChangeMain()
    {
        $idLogged = SessionAdminLogin::idLogged();
        $main_character = Player::getPlayer('account_id = "' . $idLogged . '" AND main = "1" AND deletion = "0"')->fetchObject();
        $select_players = Player::getPlayer('account_id = "' . $idLogged . '" AND main = "0" AND deletion = "0"');
        while ($player = $select_players->fetchObject()) {
            $arrayPlayers[] = [
                'id' => $player->id,
                'name' => $player->name,
                'level' => $player->level,
                'vocation' => FunctionsPlayer::convertVocation($player->vocation),
            ];
        }
        $content = View::render('pages/account/changemain', [
            'characters' => $arrayPlayers ?? null,
            'main' => $main_character->name,
        ]);
        return parent::getBase('Account Management', $content, 'account');
    }
}

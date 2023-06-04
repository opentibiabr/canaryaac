<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\ServerConfig;
use App\Utils\View;
use App\Model\Functions\Player;

class Samples extends Base
{
    public static function editSample($request, $id)
    {
        $postVars = $request->getPostVars();
        if (empty($id)) {
            return self::viewSamples($request);
        }
        if (isset($postVars['btn_delete'])) {
            ServerConfig::deletePlayerSample('id = "' . $id . '"');
            $status = Alert::getSuccess('Deletado com sucesso.');
            return self::viewSamples($request, $status);
        }
        $filter_vocation = filter_var($postVars['editsampler_vocation'], FILTER_SANITIZE_NUMBER_INT);
        $filter_level = filter_var($postVars['editsampler_level'], FILTER_SANITIZE_NUMBER_INT);
        $filter_experience = filter_var($postVars['editsampler_experience'], FILTER_SANITIZE_NUMBER_INT);
        $filter_looktype = filter_var($postVars['editsampler_looktype'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookaddons = filter_var($postVars['editsampler_addons'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookbody = filter_var($postVars['editsampler_body'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookfeet = filter_var($postVars['editsampler_feet'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookhead = filter_var($postVars['editsampler_head'], FILTER_SANITIZE_NUMBER_INT);
        $filter_looklegs = filter_var($postVars['editsampler_legs'], FILTER_SANITIZE_NUMBER_INT);
        $filter_balance = filter_var($postVars['editsampler_balance'], FILTER_SANITIZE_NUMBER_INT);
        $filter_soul = filter_var($postVars['editsampler_soul'], FILTER_SANITIZE_NUMBER_INT);
        $filter_cap = filter_var($postVars['editsampler_cap'], FILTER_SANITIZE_NUMBER_INT);
        $filter_health = filter_var($postVars['editsampler_health'], FILTER_SANITIZE_NUMBER_INT);
        $filter_healthmax = filter_var($postVars['editsampler_healthmax'], FILTER_SANITIZE_NUMBER_INT);
        $filter_mana = filter_var($postVars['editsampler_mana'], FILTER_SANITIZE_NUMBER_INT);
        $filter_manamax = filter_var($postVars['editsampler_manamax'], FILTER_SANITIZE_NUMBER_INT);
        $filter_town_id = filter_var($postVars['editsampler_townid'], FILTER_SANITIZE_NUMBER_INT);
        $filter_posx = filter_var($postVars['editsampler_positionx'], FILTER_SANITIZE_NUMBER_INT);
        $filter_posy = filter_var($postVars['editsampler_positiony'], FILTER_SANITIZE_NUMBER_INT);
        $filter_posz = filter_var($postVars['editsampler_positionz'], FILTER_SANITIZE_NUMBER_INT);

        ServerConfig::updatePlayerSample('id = "' . $id . '"', [
            'vocation' => $filter_vocation,
            'level' => $filter_level,
            'experience' => $filter_experience,
            'looktype' => $filter_looktype,
            'lookaddons' => $filter_lookaddons,
            'lookbody' => $filter_lookbody,
            'lookfeet' => $filter_lookfeet,
            'lookhead' => $filter_lookhead,
            'looklegs' => $filter_looklegs,
            'balance' => $filter_balance,
            'soul' => $filter_soul,
            'cap' => $filter_cap,
            'health' => $filter_health,
            'healthmax' => $filter_healthmax,
            'mana' => $filter_mana,
            'manamax' => $filter_manamax,
            'town_id' => $filter_town_id,
            'posx' => $filter_posx,
            'posy' => $filter_posy,
            'posz' => $filter_posz,
        ]);
        $status = Alert::getSuccess('Editado com sucesso.');
        return self::viewEditSample($request, $id, $status);
    }

    public static function createSample($request)
    {
        $postVars = $request->getPostVars();

        $filter_vocation = filter_var($postVars['newsampler_vocation'], FILTER_SANITIZE_NUMBER_INT);
        $filter_level = filter_var($postVars['newsampler_level'], FILTER_SANITIZE_NUMBER_INT);
        $filter_experience = filter_var($postVars['newsampler_experience'], FILTER_SANITIZE_NUMBER_INT);
        $filter_looktype = filter_var($postVars['newsampler_looktype'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookaddons = filter_var($postVars['newsampler_addons'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookbody = filter_var($postVars['newsampler_body'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookfeet = filter_var($postVars['newsampler_feet'], FILTER_SANITIZE_NUMBER_INT);
        $filter_lookhead = filter_var($postVars['newsampler_head'], FILTER_SANITIZE_NUMBER_INT);
        $filter_looklegs = filter_var($postVars['newsampler_legs'], FILTER_SANITIZE_NUMBER_INT);
        $filter_balance = filter_var($postVars['newsampler_balance'], FILTER_SANITIZE_NUMBER_INT);
        $filter_soul = filter_var($postVars['newsampler_soul'], FILTER_SANITIZE_NUMBER_INT);
        $filter_cap = filter_var($postVars['newsampler_cap'], FILTER_SANITIZE_NUMBER_INT);
        $filter_health = filter_var($postVars['newsampler_health'], FILTER_SANITIZE_NUMBER_INT);
        $filter_healthmax = filter_var($postVars['newsampler_healthmax'], FILTER_SANITIZE_NUMBER_INT);
        $filter_mana = filter_var($postVars['newsampler_mana'], FILTER_SANITIZE_NUMBER_INT);
        $filter_manamax = filter_var($postVars['newsampler_manamax'], FILTER_SANITIZE_NUMBER_INT);
        $filter_town_id = filter_var($postVars['newsampler_townid'], FILTER_SANITIZE_NUMBER_INT);
        $filter_posx = filter_var($postVars['newsampler_positionx'], FILTER_SANITIZE_NUMBER_INT);
        $filter_posy = filter_var($postVars['newsampler_positiony'], FILTER_SANITIZE_NUMBER_INT);
        $filter_posz = filter_var($postVars['newsampler_positionz'], FILTER_SANITIZE_NUMBER_INT);

        $fieldsToCheck = [
            'vocation' => $filter_vocation,
            'level' => $filter_level,
            'experience' => $filter_experience,
            'looktype' => $filter_looktype,
            'lookaddons' => $filter_lookaddons,
            'lookbody' => $filter_lookbody,
            'lookfeet' => $filter_lookfeet,
            'lookhead' => $filter_lookhead,
            'looklegs' => $filter_looklegs,
            'balance' => $filter_balance,
            'soul' => $filter_soul,
            'cap' => $filter_cap,
            'health' => $filter_health,
            'healthmax' => $filter_healthmax,
            'mana' => $filter_mana,
            'manamax' => $filter_manamax,
            'town_id' => $filter_town_id,
            'posx' => $filter_posx,
            'posy' => $filter_posy,
            'posz' => $filter_posz,
        ];

        // Verifique se algum campo está vazio
        foreach ($fieldsToCheck as $field => $value) {
            if (is_null($value) || $value === '') {
                $status = Alert::getError("O campo {$field} não pode estar vazio.");
                return self::viewSamples($request, $status);
            }
        }

        ServerConfig::insertPlayerSample($fieldsToCheck);

        $status = Alert::getSuccess('Criado com sucesso.');
        return self::viewSamples($request, $status);
    }

    public static function viewEditSample($request, $id, $status = null)
    {
        if (empty($id)) {
            return self::viewSamples($request);
        }
        $sample = ServerConfig::getPlayerSamples('id = "' . $id . '"')->fetchObject();
        $selected_sample = [
            'id' => $sample->id,
            'vocation' => $sample->vocation,
            'outfit' => Player::getOutfitImage($sample->looktype, $sample->lookaddons, $sample->lookbody, $sample->lookfeet, $sample->lookhead, $sample->looklegs),
            'looktype' => $sample->looktype,
            'lookaddons' => $sample->lookaddons,
            'lookbody' => $sample->lookbody,
            'lookfeet' => $sample->lookfeet,
            'lookhead' => $sample->lookhead,
            'looklegs' => $sample->looklegs,
            'level' => $sample->level,
            'experience' => $sample->experience,
            'balance' => $sample->balance,
            'health' => $sample->health,
            'healthmax' => $sample->healthmax,
            'mana' => $sample->mana,
            'manamax' => $sample->manamax,
            'soul' => $sample->soul,
            'cap' => $sample->cap,
            'town_id' => $sample->town_id,
            'posx' => $sample->posx,
            'posy' => $sample->posy,
            'posz' => $sample->posz,
        ];
        $content = View::render('admin/modules/samples/edit', [
            'status' => $status,
            'sample' => $selected_sample,
        ]);
        return parent::getPanel('Samples', $content, 'samples');
    }

    public static function getSamples()
    {
        $select_samples = ServerConfig::getPlayerSamples();
        while ($sample = $select_samples->fetchObject()) {
            $players[] = [
                'id' => $sample->id,
                'vocation_id' => $sample->vocation,
                'vocation' => Player::convertVocation($sample->vocation),
                'outfit' => Player::getOutfitImage($sample->looktype, $sample->lookaddons, $sample->lookbody, $sample->lookfeet, $sample->lookhead, $sample->looklegs),
                'level' => $sample->level,
                'experience' => $sample->experience,
                'balance' => $sample->balance,
            ];
        }
        return $players;
    }

    public static function viewSamples($request, $status = null)
    {
        $content = View::render('admin/modules/samples/index', [
            'status' => $status,
            'players' => self::getSamples(),
        ]);
        return parent::getPanel('Samples', $content, 'samples');
    }
}

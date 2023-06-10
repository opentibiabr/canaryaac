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
use App\Model\Functions\Server;
use App\Model\Entity\ServerConfig as EntityServerConfig;

class Worlds extends Base{

    public static function insertWorld($request)
    {
        $postVars = $request->getPostVars();

        if(empty($postVars['world_name'])){
            $status = Alert::getError('Defina um nome.');
            return self::viewWorlds($request, $status);
        }
        $filter_name = filter_var($postVars['world_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $dbWorld = EntityServerConfig::getWorlds([ 'name' => $filter_name])->fetchObject();
        if(!empty($dbWorld)){
            $status = Alert::getError('Já existe um mundo com este nome.');
            return self::viewWorlds($request, $status);
        }

        if(empty($postVars['world_location'])){
            $status = Alert::getError('Selecione uma localização.');
            return self::viewWorlds($request, $status);
        }
        $filter_location = filter_var($postVars['world_location'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_location > 7){
            $status = Alert::getError('Location inválida.');
            return self::viewWorlds($request, $status);
        }

        $filter_pvp_type = filter_var($postVars['world_pvp_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_pvp_type > 4){
            $status = Alert::getError('Pvp Type inválido.');
            return self::viewWorlds($request, $status);
        }

        $filter_premium_type = filter_var($postVars['world_premium_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_premium_type > 1){
            $status = Alert::getError('Premium Type inválido.');
            return self::viewWorlds($request, $status);
        }

        $filter_transfer_type = filter_var($postVars['world_transfer_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_transfer_type > 1){
            $status = Alert::getError('Transfer Type inválido.');
            return self::viewWorlds($request, $status);
        }

        $filter_battle_eye = filter_var($postVars['world_battle_eye'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_battle_eye > 2){
            $status = Alert::getError('Battle-Eye inválido.');
            return self::viewWorlds($request, $status);
        }

        $filter_world_type = filter_var($postVars['world_world_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_world_type > 1){
            $status = Alert::getError('World Type inválido.');
            return self::viewWorlds($request, $status);
        }

        if (empty($postVars['world_ipaddress'])) {
            $status = Alert::getError('IP inválido.');
            return self::viewWorlds($request, $status);
        }

        if (empty($postVars['world_port'])) {
            $status = Alert::getError('Port inválido.');
            return self::viewWorlds($request, $status);
        }

        EntityServerConfig::insertWorld(
            [
                'name' => $filter_name,
                'location' => $filter_location,
                'pvp_type' => $filter_pvp_type,
                'premium_type' => $filter_premium_type,
                'transfer_type' => $filter_transfer_type,
                'battle_eye' => $filter_battle_eye,
                'world_type' => $filter_world_type,
                'ip' => $postVars['world_ipaddress'],
                'port' => $postVars['world_port'],
            ]
        );
        $status = Alert::getSuccess('World criado com sucesso.');
        return self::viewWorlds($request, $status);
    }

    public static function deleteWorld($request, $id)
    {        
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $status = Alert::getError('Erro world.');
            return self::viewWorlds($request, $status);
        }
        $dbWorld = EntityServerConfig::getWorlds([ 'id' => $id])->fetchObject();
        if(empty($dbWorld)){
            $status = Alert::getError('Nenhum world encontrado.');
            return self::viewWorlds($request, $status);
        }
        EntityServerConfig::deleteWorld([ 'id' => $id]);
        
        $status = Alert::getSuccess('Deletado com sucesso.');
        return self::viewWorlds($request, $status);
    }

    public static function updateWorld($request, $id)
    {
        $postVars = $request->getPostVars();

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $status = Alert::getError('Erro world.');
            return self::viewWorlds($request, $status);
        }

        if (empty($postVars['editworld_name'])) {
            $status = Alert::getError('Erro world name.');
            return self::viewUpdateWorld($request, $id, $status);
        }
        $filter_name = filter_var($postVars['editworld_name'], FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($postVars['editworld_ipaddress'])) {
            $status = Alert::getError('Erro ip address.');
            return self::viewUpdateWorld($request, $id, $status);
        }
        $filter_ipaddress = filter_var($postVars['editworld_ipaddress'], FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($postVars['editworld_port'])) {
            $status = Alert::getError('Erro port.');
            return self::viewUpdateWorld($request, $id, $status);
        }
        $filter_port = filter_var($postVars['editworld_port'], FILTER_SANITIZE_NUMBER_INT);
        if (!filter_var($postVars['editworld_port'], FILTER_VALIDATE_INT)) {
            $status = Alert::getError('Erro port.');
            return self::viewUpdateWorld($request, $id, $status);
        }

        $filter_battle_eye = filter_var($postVars['editworld_battle_eye'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_battle_eye > 2){
            $status = Alert::getError('Battle-Eye inválido.');
            return self::viewUpdateWorld($request, $id, $status);
        }

        $filter_location = filter_var($postVars['editworld_location'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_location > 7){
            $status = Alert::getError('Location inválida.');
            return self::viewUpdateWorld($request, $id, $status);
        }

        $filter_pvp_type = filter_var($postVars['editworld_pvp_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_pvp_type > 4){
            $status = Alert::getError('Pvp Type inválido.');
            return self::viewUpdateWorld($request, $id, $status);
        }

        $filter_premium_type = filter_var($postVars['editworld_premium_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_premium_type > 1){
            $status = Alert::getError('Premium Type inválido.');
            return self::viewUpdateWorld($request, $id, $status);
        }

        $filter_transfer_type = filter_var($postVars['editworld_transfer_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_transfer_type > 1){
            $status = Alert::getError('Transfer Type inválido.');
            return self::viewUpdateWorld($request, $id, $status);
        }

        $filter_world_type = filter_var($postVars['editworld_world_type'], FILTER_SANITIZE_NUMBER_INT);
        if($filter_world_type > 1){
            $status = Alert::getError('World Type inválido.');
            return self::viewUpdateWorld($request, $id, $status);
        }
        
        EntityServerConfig::updateWorld([ 'id' => $id], [
            'name' => $filter_name,
            'location' => $filter_location,
            'pvp_type' => $filter_pvp_type,
            'premium_type' => $filter_premium_type,
            'transfer_type' => $filter_transfer_type,
            'battle_eye' => $filter_battle_eye,
            'world_type' => $filter_world_type,
            'ip' => $filter_ipaddress,
            'port' => $filter_port,
        ]);
        $status = Alert::getSuccess('Editado com sucesso.');
        return self::viewUpdateWorld($request, $id, $status);
    }

    public static function viewUpdateWorld($request, $id, $status = null)
    {
        $content = View::render('admin/modules/worlds/edit', [
            'status' => $status,
            'world' => Server::getWorldById($id),
        ]);
        return parent::getPanel('Worlds', $content, 'worlds');
    }

    public static function viewWorlds($request, $status = null)
    {
        $content = View::render('admin/modules/worlds/index', [
            'status' => $status,
            'worlds' => Server::getWorlds(),
        ]);
        return parent::getPanel('Worlds', $content, 'worlds');
    }
}
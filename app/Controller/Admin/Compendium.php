<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\Compendium as EntityCompendium;
use App\Utils\View;

class Compendium extends Base
{

    public static function convertNewsCategory($category_id)
    {
        switch ($category_id) {
            case 3:
                return 'USEFUL INFO';
                exit;
            case 4:
                return 'SUPPORT';
                exit;
            case 13:
                return 'CLIENT FEATURES';
                exit;
            case 15:
                return 'GAME CONTENTS';
                exit;
            case 20:
                return 'MAJOR UPDATES';
                exit;
            default:
                return null;
                exit;
        }
    }

    public static function updateCompendium($request, $id)
    {
        $postVars = $request->getPostVars();
        if(empty($postVars['compendium_headline'])){
            $status = Alert::getError('Insira um titulo.');
            return self::viewCompendium($request, $status);
        }
        if(empty($postVars['compendium_category'])){
            $status = Alert::getError('Selecione uma categoria.');
            return self::viewCompendium($request, $status);
        }
        if(empty($postVars['compendium_message'])){
            $status = Alert::getError('Insira uma mensagem.');
            return self::viewCompendium($request, $status);
        }
        $filter_category = filter_var($postVars['compendium_category'], FILTER_SANITIZE_NUMBER_INT);

        if (empty(self::convertNewsCategory($filter_category))) {
            $status = Alert::getError('Selecione uma categoria válida.');
            return self::viewCompendium($request, $status);
        }

        if (isset($postVars['compendium_delete'])) {
            EntityCompendium::deleteCompendium('id = "'.$id.'"');
            $status = Alert::getSuccess('Deletado com sucesso.');
            return self::viewCompendium($request, $status);
        }

        EntityCompendium::updateCompendium('id = "'.$id.'"', [
            'category' => $filter_category,
            'headline' => $postVars['compendium_headline'],
            'message' => $postVars['compendium_message'],
        ]);
        $status = Alert::getSuccess('Atualizado com sucesso.');
        return self::viewCompendium($request, $status);
    }

    public static function insertCompendium($request)
    {
        $postVars = $request->getPostVars();
        if(empty($postVars['compendium_headline'])){
            $status = Alert::getError('Insira um titulo.');
            return self::viewCompendium($request, $status);
        }
        if(empty($postVars['compendium_category'])){
            $status = Alert::getError('Selecione uma categoria.');
            return self::viewCompendium($request, $status);
        }
        if(empty($postVars['compendium_message'])){
            $status = Alert::getError('Insira uma mensagem.');
            return self::viewCompendium($request, $status);
        }
        $filter_category = filter_var($postVars['compendium_category'], FILTER_SANITIZE_NUMBER_INT);
        if (empty(self::convertNewsCategory($filter_category))) {
            $status = Alert::getError('Selecione uma categoria válida.');
            return self::viewCompendium($request, $status);
        }
        $convert_headline = '<p>' . $postVars['compendium_headline'] . '</p>';

        EntityCompendium::insertCompendium([
            'category' => $filter_category,
            'headline' => $convert_headline,
            'message' => $postVars['compendium_message'],
            'publishdate' => strtotime(date('m-d-Y H:i:s')),
            'type' => 1, // REGULAR
        ]);
        $status = Alert::getSuccess('Criado com sucesso.');
        return self::viewCompendium($request, $status);
    }

    public static function getAllCompendium()
    {
        $select_compendium = EntityCompendium::getCompendium();
        $index = 0;
        while ($compendium = $select_compendium->fetchObject()) {
            $index++;
            $arrayCompendium[] = [
                'campaignid' => 0,
                'category' => self::convertNewsCategory($compendium->category),
                'headline' => $compendium->headline,
                'id' => $compendium->id,
                'index' => $index,
                'message' => $compendium->message,
                'publishdate' => date('M d Y H:i:s', $compendium->publishdate),
                'type' => 'REGULAR',
            ];
        }
        return $arrayCompendium ?? '';
    }

    public static function getCompendiumById($compendium_id)
    {
        $compendium = EntityCompendium::getCompendium('id = "'.$compendium_id.'"')->fetchObject();
        $arrayCompendium = [
            'id' => $compendium->id,
            'category' => $compendium->category,
            'headline' => $compendium->headline,
            'message' => $compendium->message,
            'publishdate' => date('M d Y H:i:s', $compendium->publishdate),
            'type' => 'REGULAR',
        ];
        return $arrayCompendium;
    }

    public static function viewPublishCompendium($request, $id, $status = null)
    {
        $content = View::render('admin/modules/compendium/view', [
            'status' => $status,
            'news' => self::getCompendiumById($id),
        ]);
        return parent::getPanel('Compendium', $content, 'view_compendium');
    }

    public static function viewCompendiumPublish($request, $status = null)
    {
        $content = View::render('admin/modules/compendium/new', [
            'status' => $status,
            'news' => self::getAllCompendium(),
        ]);
        return parent::getPanel('Compendium', $content, 'new_compendium');
    }

    public static function viewCompendium($request, $status = null)
    {
        $content = View::render('admin/modules/compendium/index', [
            'status' => $status,
            'news' => self::getAllCompendium(),
        ]);
        return parent::getPanel('Compendium', $content, 'view_compendium');
    }

}
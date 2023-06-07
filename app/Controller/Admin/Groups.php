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
use App\Model\Entity\Groups as EntityGroups;
use App\Model\Entity\Houses as EntityHouse;
use App\Model\Functions\Player;
use App\Model\Functions\Server;
use App\Controller\Admin\Alert;

class Groups extends Base{

    public static function getGroupsXml($request)
    {
        $postVars = $request->getPostVars();;
        $localxml = filter_var($postVars['localxml'], FILTER_SANITIZE_SPECIAL_CHARS);
        $array = simplexml_load_file($localxml);

        foreach($array as $group){
            $groups = [
                'group_id' => $group['id'],
                'name' => $group['name']
            ];
            EntityGroups::insertGroup($groups);
        }
        $status = Alert::getSuccess('XML importado com sucesso!') ?? null;
        return self::viewGroups($request, $status);
    }

    public static function deleteGroups($request)
    {
        $postVars = $request->getPostVars();
        $group_id = $postVars['groupid'];
        EntityGroups::deleteGroup('id = "'.$group_id.'"');
        
        $status = Alert::getSuccess('Group deletado com sucesso!') ?? null;

        return self::viewGroups($request, $status);
    }

    public static function getAllGroups()
    {
        $select = EntityGroups::getGroups();
        while($obAllGroups = $select->fetchObject()){
            $allGroups[] = [
                'id' => (int)$obAllGroups->id,
                'group_id' => (int)$obAllGroups->group_id,
                'name' => $obAllGroups->name
            ];
        }
        return $allGroups ?? false;
    }

    public static function viewGroups($request, $errorMessage = null)
    {
        $content = View::render('admin/modules/groups/index', [
            'status' => $errorMessage,
            'groups' => self::getAllGroups(),
            'total_houses' => (int)EntityHouse::getHouses(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
            'total_houses_rented' => (int)EntityHouse::getHouses(['owner' != 0], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
        ]);

        return parent::getPanel('Groups', $content, 'groups');
    }

}
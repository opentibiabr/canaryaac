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
use App\Model\Entity\Creatures as EntityCreatures;
use App\Model\Entity\Guilds as EntityGuild;
use App\Model\Entity\Houses as EntityHouse;
use App\Model\Functions\Player;
use App\Model\Functions\Server;
use App\Controller\Admin\Alert;

class Creatures extends Base{

    public static function getAllBosses()
    {
        $allBosses = [];
        $dbBosses = EntityCreatures::getBoss();
        while($boss = $dbBosses->fetchObject()){
            $allBosses[] = [
                'id' => $boss->id,
                'image' => $boss->tag,
                'tag' => $boss->tag,
                'name' => $boss->name,
            ];
        }
        return $allBosses;
    }

    public static function getAllCreatures()
    {
        $allCreatures = [];
        $dbCreatures = EntityCreatures::getCreatures();
        while($creature = $dbCreatures->fetchObject()){
            $allCreatures[] = [
                'id' => $creature->id,
                'image' => $creature->tag,
                'tag' => $creature->tag,
                'name' => $creature->name,
                'description' => $creature->description,
            ];
        }
        return $allCreatures;
    }

    public static function deleteCreature($request)
    {
        $postVars = $request->getPostVars();
        $creature_id = (int)$postVars['creature_id'];
        $creature_type = filter_var($postVars['creature_type'], FILTER_SANITIZE_SPECIAL_CHARS);

        if(empty($creature_id)){
            $status = Alert::getError('Erro ao deletar.') ?? null;
            return self::viewCreatures($request, $status);
        }
        if($creature_type == 'creature'){
            EntityCreatures::deleteCreature('id = "'.$creature_id.'"');
            $status = Alert::getSuccess('Creature deletada com sucesso!') ?? null;
            return self::viewCreatures($request, $status);
        }
        if($creature_type == 'boss'){
            EntityCreatures::deleteBoss('id = "'.$creature_id.'"');
            $status = Alert::getSuccess('Boss deletado com sucesso!') ?? null;
            return self::viewCreatures($request, $status);
        }

        $status = Alert::getError('Erro ao deletar.') ?? null;
        return self::viewCreatures($request, $status);
    }

    public static function insertCreature($request)
    {
        $postVars = $request->getPostVars();
        $creature_name = filter_var($postVars['creature_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $creature_tag = filter_var($postVars['creature_tag'], FILTER_SANITIZE_SPECIAL_CHARS);
        $creature_type = filter_var($postVars['creature_type'], FILTER_SANITIZE_SPECIAL_CHARS);
        $creature_description = filter_var($postVars['creature_description'], FILTER_SANITIZE_SPECIAL_CHARS);

        $status = Alert::getError('Você precisa adicionar um nome.') ?? null;
        if(empty($creature_name)){
            return self::viewCreatures($request, $status);
        }
        $status = Alert::getError('Você precisa adicionar uma tag.') ?? null;
        if(empty($creature_tag)){
            return self::viewCreatures($request, $status);
        }

        if($creature_type == 'creature'){
            $values = [
                'name' => $creature_name,
                'tag' => $creature_tag,
                'description' => $creature_description,
            ];
            EntityCreatures::insertCreature($values);
            $status = Alert::getSuccess('Creature criada com sucesso!') ?? null;
            return self::viewCreatures($request, $status);
        }
        if($creature_type == 'boss') {
            $values = [
                'name' => $creature_name,
                'tag' => $creature_tag,
            ];
            EntityCreatures::insertBoss($values);
            $status = Alert::getSuccess('Boss criado com sucesso!') ?? null;
            return self::viewCreatures($request, $status);
        }
        $status = Alert::getError('Algo deu errado.') ?? null;
        return self::viewCreatures($request, $status);
    }
    
    public static function viewCreatures($request, $errorMessage = null)
    {
        $content = View::render('admin/modules/creatures/index', [
            'status' => $errorMessage,
            'creatures' => self::getAllCreatures(),
            'bosses' => self::getAllBosses(),
            'total_creatures' => (int)EntityCreatures::getCreatures(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
            'total_bosses' => (int)EntityCreatures::getBoss(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
        ]);

        return parent::getPanel('Creatures', $content, 'creatures');
    }

}
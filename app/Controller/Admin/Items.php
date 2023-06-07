<?php
/**
 * Items Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\Items as EntityItems;
use App\Utils\View;
use DOMDocument;

class Items extends Base{

    public static function importItems($request)
    {
        $items_path = $_ENV['SERVER_PATH'] . 'data/items/items.xml';
        if(file_exists($items_path)) {
            $items = new DOMDocument();
            $items->load($items_path);
        }
        if(!$items){
            echo 'Error: cannot load <b>items.xml</b>!';
            return;
        }
        foreach($items->getElementsByTagName('item') as $item){
            if ($item->getAttribute('fromid')) {
                for ($id = $item->getAttribute('fromid'); $id <= $item->getAttribute('toid'); $id++) {
                    self::importItemAttribute($request, $id, $item);
                }
            } else {
                self::importItemAttribute($request, $item->getAttribute('id'), $item);
            }
        }
    }

    public static function importItemAttribute($request, $item_id, $item)
    {
        $item_description = '';
        $item_weight = '';
        $type = '';
        $level = 0;
        $shootType = '';
        $maxhitchance = '';
        $range = '';
        foreach( $item->getElementsByTagName('attribute') as $attribute)
        {
            if ($attribute->getAttribute('key') == 'description'){
                $item_description = $attribute->getAttribute('value');
                continue;
            }
            if ($attribute->getAttribute('key') == 'weight'){
                $item_weight = $attribute->getAttribute('value');
                continue;
            }
            if ($attribute->getAttribute('key') == 'weaponType') {
                $type = $attribute->getAttribute('value');
                
                if ($type == 'axe' || $type == 'club' || $type == 'sword') {
                    foreach( $item->getElementsByTagName('attribute') as $_attribute) {
                        if($_attribute->getAttribute('key') == 'attack') {
                            $level = $_attribute->getAttribute('value');
                            break;
                        }
                    }
                }
                if ($type == 'distance' || $type == 'wand' || $type == 'ammunition') {
                    foreach ($item->getElementsByTagName('attribute') as $_attribute) {
                        if ($_attribute->getAttribute('key') == 'shootType') {
                            $shootType = $_attribute->getAttribute('value');
                            break;
                        }
                        if ($_attribute->getAttribute('key') == 'range') {
                            $range = $_attribute->getAttribute('value');
                            break;
                        }
                        if ($_attribute->getAttribute('key') == 'maxhitchance') {
                            $maxhitchance = $_attribute->getAttribute('value');
                            break;
                        }
                    }
                }
                if ($type == 'shield') {
                    foreach( $item->getElementsByTagName('attribute') as $_attribute) {
                        if($_attribute->getAttribute('key') == 'defense') {
                            $level = $_attribute->getAttribute('value');
                            break;
                        }
                    }
                }
                continue;
            }
            if ($attribute->getAttribute('key') == 'slotType' && empty($type)) {
                $type = $attribute->getAttribute('value');
                if ($type == 'head' || $type == 'body' || $type == 'legs' || $type == 'feet') {
                    foreach( $item->getElementsByTagName('attribute') as $_attribute) {
                        if($_attribute->getAttribute('key') == 'armor') {
                            $level = $_attribute->getAttribute('value');
                            break;
                        }
                    }
                }
                else if ($type == 'backpack') {
                    foreach( $item->getElementsByTagName('attribute') as $_attribute) {
                        if($_attribute->getAttribute('key') == 'containerSize') {
                            $level = $_attribute->getAttribute('value');
                            break;
                        }
                    }
                }
                continue;
            }
        }
        EntityItems::insertItems([
            'item_id' => $item_id,
            'name' => $item->getAttribute('name'),
            'type' => $type,
            'level' => $level, #attack, defense, armor, containerSize, ..
        ]);
        return self::viewItems($request, 'Success!');
    }

    public static function getItems()
    {
        $select_items = EntityItems::getItems();
        while ($item = $select_items->fetchObject()) {
            $arrayItems[] = [
                'item_id' => $item->item_id,
                'name' => $item->name,
                'type' => $item->type,
                'level' => $item->level,
            ];
        }
        return $arrayItems ?? [];
    }

    public static function viewItems($request, $errorMessage = null)
    {
        $items_path = $_ENV['SERVER_PATH'] . 'data/items/items.xml';

        $content = View::render('admin/modules/items/index', [
            'status' => $errorMessage,
            'items_path' => $items_path,
            'itemGroup' => self::getItems(),
            'total_items' => (int)EntityItems::getItems(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
        ]);

        return parent::getPanel('Items', $content, 'items');
    }

}
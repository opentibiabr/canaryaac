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
use App\DatabaseManager\Pagination;
use App\Controller\Api\Characters;
use DOMDocument;

class Items extends Base
{
    public static function importItems($request)
    {
        $items_path = $_ENV['SERVER_PATH'] . 'data/items/items.xml';
        if (file_exists($items_path)) {
            $items = new DOMDocument();
            $items->load($items_path);
        }
        if (!$items) {
            echo 'Error: cannot load <b>items.xml</b>!';
            return;
        }

        $insertData = [];
        $batchSize = 1000; // Define the number of items to be inserted at once
        foreach ($items->getElementsByTagName('item') as $item) {
            if ($item->getAttribute('fromid')) {
                for ($id = $item->getAttribute('fromid'); $id <= $item->getAttribute('toid'); $id++) {
                    $insertData[] = self::importItemAttribute($id, $item);
                }
            } else {
                $insertData[] = self::importItemAttribute($item->getAttribute('id'), $item);
            }

            // If we have enough items for a batch, insert them and clear the insertData array
            if (count($insertData) >= $batchSize) {
                EntityItems::insertItems($insertData);
                $insertData = [];
            }
        }

        // Insert any remaining items
        if (!empty($insertData)) {
            EntityItems::insertItems($insertData);
        }

        // Inicie a sessão se ainda não foi iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Defina a mensagem de sucesso na sessão após a importação bem-sucedida
        $_SESSION['success_message'] = "Items importados com sucesso!";
        header('Location: ' . URL . '/admin/items');
        exit;
    }


    public static function importItemAttribute($item_id, $item)
    {
        $item_description = '';
        $item_weight = '';
        $type = '';
        $attack = 0;
        $extradef = 0;
        $defense = 0;
        $armor = 0;
        $magicpoints = 0;
        $shootType = '';
        $slotType = '';
        $containersize = 0;
        $range = 0;

        foreach ($item->getElementsByTagName('attribute') as $attribute) {
            $key = $attribute->getAttribute('key');
            $value = $attribute->getAttribute('value');

            switch ($key) {
                // case 'description':
                //     $item_description = $value;
                //     break;
                // case 'weight':
                //     $item_weight = $value;
                //     break;
                case 'weaponType':
                    $type = $value;
                    break;
                case 'attack':
                    $attack = $value;
                    break;
                case 'extradef':
                    $extradef = $value;
                    break;
                case 'defense':
                    $defense = $value;
                    break;
                case 'magicpoints':
                    $magicpoints = $value;
                    break;
                case 'shootType':
                    $shootType = $value;
                    break;
                case 'slotType':
                    $slotType = $value;
                    break;
                case 'range':
                    $range = $value;
                    break;
                case 'containersize':
                    $containersize = $value;
                    break;
                case 'armor':
                    $armor = $value;
            }
        }

        return [
            'item_id' => $item_id,
            'name' => $item->getAttribute('name'),
            // 'description' => $item_description,
            // 'weight' => $item_weight,
            'shootType' => $shootType,
            'type' => $type,
            'armor' => $armor,
            'attack' => $attack,
            'extradef' => $extradef,
            'defense' => $defense,
            'slotType' => $slotType,
            'magicpoints' => $magicpoints,
            'containersize' => $containersize,
            '`range`' => $range,
        ];
    }


    public static function getItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $totalAmount = EntityItems::getItems(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        $obPagination = new Pagination($totalAmount, $currentPage, 1000);
        $results = EntityItems::getItems(null, null, $obPagination->getLimit());
        while ($obAllItems = $results->fetchObject(EntityItems::class)) {
            $allItems[] = [
                'item_id' => (int) $obAllItems->item_id,
                'name' => $obAllItems->name,
                'shootType' => $obAllItems->shootType,
                'type' => $obAllItems->type,
                'armor' => $obAllItems->armor,
                'attack' => $obAllItems->attack,
                'extradef' => $obAllItems->extradef,
                'defense' => $obAllItems->defense,
                'slotType' => $obAllItems->slotType,
                'magicpoints' => $obAllItems->magicpoints,
                'containersize' => $obAllItems->containersize,
                'range' => $obAllItems->range
            ];
        }
        return $allItems ?? false;
    }

    public static function deleteAllItems($request)
    {
        EntityItems::deleteItems('canary_items');

        // Inicie a sessão se ainda não foi iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Defina a mensagem de sucesso na sessão após a importação bem-sucedida
        $_SESSION['success_message'] = "Items deletados com sucesso!";
        return self::viewItems($request, 'Success!');
    }

    public static function viewItems($request, $errorMessage = null)
    {
        $successMessage = null;
        if (isset($_SESSION['success_message'])) {
            $successMessage = $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Limpar a mensagem da sessão
        }

        $items_path = $_ENV['SERVER_PATH'] . 'data/items/items.xml';

        $content = View::render('admin/modules/items/index', [
            'status' => $errorMessage,
            'success_message' => $successMessage,
            // Passar a mensagem de sucesso para o template
            'items_path' => $items_path,
            'itemGroup' => self::getItems($request, $obPagination),
            'total_items' => (int) EntityItems::getItems(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd,
            'pagination' => self::getPagination($request, $obPagination)
        ]);

        return parent::getPanel('Items', $content, 'items');
    }
}

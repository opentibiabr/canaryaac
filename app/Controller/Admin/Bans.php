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
use App\Model\Entity\Bans as EntityBans;
use App\DatabaseManager\Pagination;

class Bans extends Base{

    public static function getAllBans($request,&$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $totalAmount = EntityBans::getAccountBans(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        $obPagination = new Pagination($totalAmount, $currentPage, 10);
        $results = EntityBans::getAccountBans(null, null, $obPagination->getLimit());
        while($obAllBans = $results->fetchObject(EntityBans::class)){
            $allBans[] = [
                'account_id' => (int)$obAllBans->account_id,
                'reason' => $obAllBans->reason,
                'banned_at' => $obAllBans->banned_at,
                'expires_at' => $obAllBans->expires_at,
                'banned_by' => $obAllBans->banned_by
            ];
        }
        return $allBans ?? false;
    }

    public static function getBans($request)
    {
        $content = View::render('admin/modules/bans/index', [
            'bans' => self::getAllBans($request, $obPagination),
            'pagination' => self::getPagination($request, $obPagination)
        ]);

        return parent::getPanel('Banishments', $content, 'bans');
    }

}
<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Api;

use App\Http\Request;

class Api
{
    /**
     * Método responsável por retornar os detalhes da API
     *
     * @param Request $request
     * @return array
     */
    public static function getDetails($request)
    {
        return [
            'name' => 'API CanaryAAC',
            'version' => 'v1.0.0',
            'author' => 'Lucas Giovanni',
            'email' => 'contato@lucasgiovanni.com'
        ];
    }

    protected static function getPagination($request, $obPagination)
    {
        $queryParams = $request->getQueryParams();
        $pages = $obPagination->getPages();

        return [
            'current' => isset($queryParams['page']) ? (int) $queryParams['page'] : 1,
            'total' => !empty($pages) ? count($pages) : 1
        ];
    }
}

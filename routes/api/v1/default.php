<?php

use App\Http\Response;
use App\Controller\Api;

// Rota raiz da API v1
$obRouter->get('/api/v1', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Api::getDetails($request), 'application/json');
    }
]);

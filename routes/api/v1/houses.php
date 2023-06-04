<?php

use App\Http\Response;
use App\Controller\Api;

$obRouter->get('/api/v1/houses', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Houses::getHouses($request), 'application/json');
    }
]);

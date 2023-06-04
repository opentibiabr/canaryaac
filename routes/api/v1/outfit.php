<?php

use App\Http\Response;
use App\Controller\Api;

$obRouter->get('/api/v1/outfit', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Outfit::getOutfit($request), 'application/json');
    }
]);

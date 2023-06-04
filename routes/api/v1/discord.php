<?php

use App\Http\Response;
use App\Controller\Api;

$obRouter->post('/api/v1/discord/searchcharacter', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Characters::searchCharacterDiscordBOT($request), 'application/json');
    }
]);

$obRouter->get('/api/v1/discord/boosted', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Boosted::getBoostedDiscordBOT($request), 'application/json');
    }
]);

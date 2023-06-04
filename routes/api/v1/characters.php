<?php

use App\Http\Response;
use App\Controller\Api;

$obRouter->get('/api/v1/characters', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Characters::getCharacters($request), 'application/json');
    }
]);

$obRouter->post('/api/v1/searchcharacters', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Characters::searchCharacter($request), 'application/json');
    }
]);

$obRouter->get('/api/v1/lastdeaths', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\LastDeaths::getLastDeaths($request), 'application/json');
    }
]);

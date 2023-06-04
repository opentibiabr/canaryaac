<?php

use App\Http\Response;
use App\Controller\Api;

$obRouter->get('/api/v1/guilds', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Guilds::getGuilds($request), 'application/json');
    }
]);

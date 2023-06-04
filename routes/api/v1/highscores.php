<?php

use App\Http\Response;
use App\Controller\Api;

$obRouter->get('/api/v1/highscores', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\Highscores::getHighscores($request), 'application/json');
    }
]);

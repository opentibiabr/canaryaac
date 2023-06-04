<?php

use App\Http\Response;
use App\Controller\Api;

$obRouter->post('/api/v1/check_charactername', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\CheckCharacterName::getCharacterName($request), 'application/json');
    }
]);

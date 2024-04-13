<?php

global $obRouter;

use App\Http\Response;
use App\Controller\Api;

$obRouter->get('/api/v1/login', [
    'middlewares' => [
        'api'
    ],
    function($request){
        return new Response(200, Api\Login::getLogin($request), 'application/json');
    }
]);

$obRouter->post('/api/v1/login', [
    'middlewares' => [
        'api'
    ],
    function($request){
        return new Response(200, Api\Login::getLogin($request), 'application/json');
    }
]);
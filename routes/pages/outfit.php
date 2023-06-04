<?php

use App\Http\Response;

use App\Controller\Pages\Outfit;

$obRouter->get('/outfit', [
    function ($request) {
        return new Response(200, Outfit::viewAnimatedOutfit($request), 'image/gif');
    }
]);
$obRouter->get('/outfit/generatecache', [
    function ($request) {
        return new Response(200, Outfit::generateCache($request));
    }
]);

<?php

use App\Http\Response;

use App\Controller\Admin\Home;
use App\Controller\Admin\Players;
use App\Controller\Admin\Accounts;
use App\Controller\Admin\Achievements;
use App\Controller\Admin\Bans;
use App\Controller\Admin\Client;
use App\Controller\Admin\Compendium;
use App\Controller\Admin\Countdowns;
use App\Controller\Admin\Houses;
use App\Controller\Admin\Guilds;
use App\Controller\Admin\Creatures;
use App\Controller\Admin\Donates;
use App\Controller\Admin\Groups;
use App\Controller\Admin\Polls;
use App\Controller\Admin\Publications;
use App\Controller\Admin\Samples;
use App\Controller\Admin\Settings;
use App\Controller\Admin\Items;
use App\Controller\Admin\Upload;
use App\Controller\Admin\Worlds;

$obRouter->get('/admin', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Home::getHome($request));
    }
]);
$obRouter->get('/admin/home', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Home::getHome($request));
    }
]);

$obRouter->get('/admin/settings', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Settings::viewSettings($request));
    }
]);
$obRouter->post('/admin/settings', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Settings::insertWorld($request));
    }
]);

$obRouter->get('/admin/upload', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Upload::viewUpload($request));
    }
]);
$obRouter->post('/admin/upload', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Upload::upload($request));
    }
]);

$obRouter->get('/admin/publications', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Publications::viewPublications($request));
    }
]);
$obRouter->get('/admin/publications/news', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Publications::viewPublishNews($request));
    }
]);
$obRouter->post('/admin/publications/news', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Publications::insertNews($request));
    }
]);
$obRouter->get('/admin/publications/newsticker', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Publications::viewPublishNewsticker($request));
    }
]);
$obRouter->post('/admin/publications/newsticker', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Publications::insertNewsticker($request));
    }
]);
$obRouter->get('/admin/publications/featuredarticle', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Publications::viewPublishFeaturedArticle($request));
    }
]);
$obRouter->post('/admin/publications/featuredarticle', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Publications::insertFeaturedArticle($request));
    }
]);

$obRouter->get('/admin/donates', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Donates::viewDonates($request));
    }
]);
$obRouter->post('/admin/donates', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Donates::updateDonates($request));
    }
]);
$obRouter->get('/admin/donates/{reference}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $reference) {
        return new Response(200, Donates::viewPaymentByReference($request, $reference));
    }
]);


$obRouter->get('/admin/samples', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Samples::viewSamples($request));
    }
]);
$obRouter->post('/admin/samples', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Samples::createSample($request));
    }
]);
$obRouter->get('/admin/samples/{id}/edit', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Samples::viewEditSample($request, $id));
    }
]);
$obRouter->post('/admin/samples/{id}/edit', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Samples::editSample($request, $id));
    }
]);
$obRouter->get('/admin/countdowns', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Countdowns::viewCountdowns($request));
    }
]);
$obRouter->post('/admin/countdowns', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Countdowns::insertCountdown($request));
    }
]);
$obRouter->get('/admin/client', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Client::viewCreateClient($request));
    }
]);
$obRouter->post('/admin/client', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Client::editClient($request));
    }
]);
$obRouter->get('/admin/worlds', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Worlds::viewWorlds($request));
    }
]);
$obRouter->post('/admin/worlds', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Worlds::insertWorld($request));
    }
]);
$obRouter->get('/admin/worlds/{id}/edit', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Worlds::viewUpdateWorld($request, $id));
    }
]);
$obRouter->post('/admin/worlds/{id}/edit', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Worlds::updateWorld($request, $id));
    }
]);
$obRouter->get('/admin/worlds/{id}/delete', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Worlds::deleteWorld($request, $id));
    }
]);
$obRouter->get('/admin/polls', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Polls::viewPolls($request));
    }
]);
$obRouter->get('/admin/polls/new', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Polls::viewInsertNewPoll($request));
    }
]);
$obRouter->post('/admin/polls/new', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Polls::insertNewPoll($request));
    }
]);
$obRouter->get('/admin/polls/{id}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Polls::viewPollById($request, $id));
    }
]);
$obRouter->get('/admin/polls/{id}/delete', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Polls::deletePoll($request, $id));
    }
]);
$obRouter->get('/admin/bans', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Bans::getBans($request));
    }
]);

$obRouter->get('/admin/achievements', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Achievements::viewAchievements($request));
    }
]);
$obRouter->post('/admin/achievements', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Achievements::importAchievements($request));
    }
]);

$obRouter->get('/admin/compendium', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Compendium::viewCompendium($request));
    }
]);
$obRouter->get('/admin/compendium/{id}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Compendium::viewPublishCompendium($request, $id));
    }
]);
$obRouter->post('/admin/compendium/{id}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Compendium::updateCompendium($request, $id));
    }
]);
$obRouter->get('/admin/compendium/new', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Compendium::viewCompendiumPublish($request));
    }
]);
$obRouter->post('/admin/compendium/new', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Compendium::insertCompendium($request));
    }
]);


$obRouter->get('/admin/groups', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Groups::viewGroups($request));
    }
]);
$obRouter->post('/admin/groups/import', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Groups::getGroupsXml($request));
    }
]);
$obRouter->post('/admin/groups/delete', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Groups::deleteGroups($request));
    }
]);

$obRouter->get('/admin/items', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Items::viewItems($request));
    }
]);
$obRouter->post('/admin/items/import', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Items::importItems($request));
    }
]);

$obRouter->get('/admin/houses', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Houses::getHouses($request));
    }
]);
$obRouter->post('/admin/houses/import', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Houses::getHousesXml($request));
    }
]);
$obRouter->post('/admin/houses/delete', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Houses::deleteHouses($request));
    }
]);

$obRouter->get('/admin/guilds', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Guilds::viewGuilds($request));
    }
]);
$obRouter->get('/admin/guilds/{id}', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Guilds::viewEditGuild($request, $id));
    }
]);
$obRouter->post('/admin/guilds/{id}', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Guilds::viewEditGuild($request, $id));
    }
]);

$obRouter->get('/admin/creatures', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Creatures::viewCreatures($request));
    }
]);
$obRouter->post('/admin/creatures', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Creatures::insertCreature($request));
    }
]);
$obRouter->post('/admin/creatures/delete', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Creatures::insertCreature($request));
    }
]);

$obRouter->get('/admin/players', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Players::getPlayers($request));
    }
]);
$obRouter->get('/admin/players/{id}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Players::viewPlayer($request, $id));
    }
]);
$obRouter->post('/admin/players/{id}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Players::updatePlayer($request, $id));
    }
]);

$obRouter->get('/admin/accounts', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request) {
        return new Response(200, Accounts::getAccounts($request));
    }
]);
$obRouter->get('/admin/accounts/{id}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Accounts::viewAccount($request, $id));
    }
]);
$obRouter->post('/admin/accounts/{id}/view', [
    'middlewares' => [
        'required-admin-login',
        'role-permission'
    ],
    function ($request, $id) {
        return new Response(200, Accounts::updateAccount($request, $id));
    }
]);

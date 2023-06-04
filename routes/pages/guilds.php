<?php

use App\Http\Response;

use App\Controller\Pages\Guilds;

$obRouter->get('/community/guilds', [
    function ($request) {
        return new Response(200, Guilds\ViewGuild::viewAllGuilds($request));
    }
]);
$obRouter->get('/community/guilds/{name}/view', [
    function ($request, $name) {
        return new Response(200, Guilds\ViewGuild::viewGuild($request, $name));
    }
]);
$obRouter->get('/community/guilds/found', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Guilds\Found::viewFoundGuild($request));
    }
]);
$obRouter->post('/community/guilds/found', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Guilds\Found::insertFoundGuild($request));
    }
]);
$obRouter->get('/community/guilds/{name}/guildwars', [
    function ($request, $name) {
        return new Response(200, Guilds\DeclareWar::viewGuildWars($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/declarewar', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\DeclareWar::viewDeclareWar($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/declarewar', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\DeclareWar::insertDeclareWar($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/guildwars/{war}/accept', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name, $war) {
        return new Response(200, Guilds\DeclareWar::acceptWar($request, $name, $war));
    }
]);
$obRouter->get('/community/guilds/{name}/guildwars/{warid}/reject', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name, $warid) {
        return new Response(200, Guilds\DeclareWar::rejectWar($request, $name, $warid));
    }
]);
$obRouter->get('/community/guilds/{name}/guildwars/{warid}/cancel', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name, $warid) {
        return new Response(200, Guilds\DeclareWar::cancelWar($request, $name, $warid));
    }
]);
$obRouter->get('/community/guilds/{name}/guildevents', [
    function ($request, $name) {
        return new Response(200, Guilds\GuildEvent::viewGuildEvents($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/guildevents/create', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\GuildEvent::viewCreateGuildEvents($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/guildevents/create', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\GuildEvent::insertGuildEvent($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/applications', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\Applications::viewApplications($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/applications', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\Applications::actionApplications($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/activitylog', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\ActivityLog::viewActivityLog($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/editdescription', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\EditDescription::viewEditDescription($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/editdescription', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\EditDescription::updateEditDescription($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/disbandguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\DisbandGuild::viewDisbandGuild($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/disbandguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\DisbandGuild::deleteDisbandGuild($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/resignleadership', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\ResignLeadership::viewResignLeadership($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/resignleadership', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\ResignLeadership::updateResignLeadership($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/editranks', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\EditRanks::viewEditRanks($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/editranks', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\EditRanks::updateEditRanks($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/editmembers', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\EditMembers::viewEditMembers($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/editmembers', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\EditMembers::updateEditMembers($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/leaveguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\LeaveGuild::viewLeaveGuild($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/leaveguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\LeaveGuild::deleteLeaveGuild($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/joinguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\JoinGuild::viewJoinGuild($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/joinguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\JoinGuild::insertJoinGuild($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/invitecharacter', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\InviteCharacter::viewInviteCharacter($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/invitecharacter', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\InviteCharacter::insertInviteCharacter($request, $name));
    }
]);
$obRouter->get('/community/guilds/{name}/applytothisguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\ApplyToThisGuild::viewApplyToThisGuild($request, $name));
    }
]);
$obRouter->post('/community/guilds/{name}/applytothisguild', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Guilds\ApplyToThisGuild::insertApplyToThisGuild($request, $name));
    }
]);

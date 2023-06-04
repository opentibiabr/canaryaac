<?php

use App\Http\Response;
use App\Controller\Pages\Account;
use App\Controller\Pages\AccountLost;

$obRouter->get('/account', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Index::getAccount($request));
    }
]);
$obRouter->get('/account/login', [
    'middlewares' => [
        'required-logout'
    ],
    function ($request) {
        return new Response(200, Account\Login::getLogin($request));
    }
]);
$obRouter->post('/account/login', [
    'middlewares' => [
        'required-logout'
    ],
    function ($request) {
        return new Response(200, Account\Login::setLogin($request));
    }
]);
$obRouter->get('/account/logout', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Login::setLogout($request));
    }
]);

$obRouter->get('/account/createcharacter', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\CreateCharacter::viewCreateCharacter($request));
    }
]);
$obRouter->post('/account/createcharacter', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\CreateCharacter::insertCharacter($request));
    }
]);

$obRouter->get('/account/manage', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Manage::getAccount($request));
    }
]);

$obRouter->get('/account/registration', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Registration::getRegistration($request));
    }
]);
$obRouter->post('/account/registration', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Registration::insertRegister($request));
    }
]);

$obRouter->get('/account/lostaccount', [
    'middlewares' => [
        'required-logout'
    ],
    function ($request) {
        return new Response(200, Account\Lost::getLostAccount($request));
    }
]);
$obRouter->post('/account/lostaccount', [
    'middlewares' => [
        'required-logout'
    ],
    function ($request) {
        return new Response(200, Account\Lost::selectAccount($request));
    }
]);
$obRouter->post('/account/lostaccount/recoverykey', [
    'middlewares' => [
        'required-logout'
    ],
    function ($request) {
        return new Response(200, Account\Lost::viewRecoveryKey($request));
    }
]);
$obRouter->get('/account/character/{name}/edit', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Account\CharacterEdit::viewCharacterEdit($request, $name));
    }
]);
$obRouter->post('/account/character/{name}/edit', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Account\CharacterEdit::updateCharacter($request, $name));
    }
]);
$obRouter->get('/account/character/{name}/delete', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Account\CharacterDelete::viewCharacterDelete($request, $name));
    }
]);
$obRouter->post('/account/character/{name}/delete', [
    'middlewares' => [
        'required-login'
    ],
    function ($request, $name) {
        return new Response(200, Account\CharacterDelete::deleteCharacter($request, $name));
    }
]);

$obRouter->get('/account/changepassword', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\ChangePassword::viewChangePassword($request));
    }
]);
$obRouter->post('/account/changepassword', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\ChangePassword::updatePassword($request));
    }
]);
$obRouter->get('/account/changeemail', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\ChangeEmail::viewChangeEmail($request));
    }
]);
$obRouter->post('/account/changeemail', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\ChangeEmail::updateEmail($request));
    }
]);

$obRouter->get('/account/authentication', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewAuthentication($request));
    }
]);
$obRouter->post('/account/authentication/confirm', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewConfirmAuthentication($request));
    }
]);
$obRouter->post('/account/authentication/connect', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewConnectAuthentication($request));
    }
]);
$obRouter->post('/account/authentication/finish', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewConfirmConnect($request));
    }
]);
$obRouter->get('/account/authentication/unlink', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewUnlinkAuthentication($request));
    }
]);
$obRouter->post('/account/authentication/unlink/confirm', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewUnlinkConfirmToken($request));
    }
]);
$obRouter->get('/account/authentication/unlink/recoverykey', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewUnlinkbyRecoveryKey($request));
    }
]);
$obRouter->post('/account/authentication/unlink/recoverykey/confirm', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\Authentication::viewUnlinkbyRecoveryKeyConfirm($request));
    }
]);

$obRouter->get('/account/changemain', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\ChangeMainCharacter::viewChangeMain($request));
    }
]);
$obRouter->post('/account/changemain/confirm', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\ChangeMainCharacter::viewConfirmCharacter($request));
    }
]);
$obRouter->post('/account/changemain/changed', [
    'middlewares' => [
        'required-login'
    ],
    function ($request) {
        return new Response(200, Account\ChangeMainCharacter::viewChangedMain($request));
    }
]);

$obRouter->get('/createaccount', [
    'middlewares' => [
        'required-logout'
    ],
    function ($request) {
        return new Response(200, Account\Create::getCreateAccount($request));
    }
]);
$obRouter->post('/createaccount', [
    'middlewares' => [
        'required-logout'
    ],
    function ($request) {
        return new Response(200, Account\Create::createAccount($request));
    }
]);

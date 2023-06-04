<?php
require __DIR__.'/../vendor/autoload.php';

use App\Utils\View;
use Dotenv\Dotenv;
use App\DatabaseManager\Database;
use App\Http\Middleware\Queue as MiddlewareQueue;

// Load the .env files
Dotenv::createImmutable(__DIR__.'/../')->load();

// Defines the URL constant
define('URL', $_ENV['URL']);
define('OUTFITS_FOLDER', $_ENV['OUTFITS_FOLDER']);
define('SITE_NAME', !empty($_ENV['SITE_NAME']) ? $_ENV['SITE_NAME'] : 'Canary AAC');

// Configure Database settings
Database::config(
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_PORT']
);

// Sets the default value of variables
View::init([
    'URL' => URL,
    'CACHE' => URL.'/resources/cache',
    'IMAGE_PATH' => URL.'/resources/images',
    'STYLES_PATH' => URL.'/resources/styles',
    'JS_PATH' => URL.'/resources/javascripts',
]);

// Defines middleware mapping
MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'required-logout' => \App\Http\Middleware\RequireLogout::class,
    'required-login' => \App\Http\Middleware\RequireLogin::class,
    'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
    'role-permission' => \App\Http\Middleware\RolePermission::class,
    'api' => \App\Http\Middleware\Api::class
]);

// Defines default middleware mapping (runs on all routes)
MiddlewareQueue::setDefault([
    'maintenance',
]);
<?php
/**
 * Login Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Session\Admin;

class Login
{
    public static function init()
    {
        if(session_status() != PHP_SESSION_ACTIVE) {
            session_start([
                'name' => 'CanaryAAC'
            ]);
        }
    }

    public static function login($obAccount)
    {
        self::init();
        $_SESSION['account']['user'] = [
            'id' => $obAccount->id,
            'name' => $obAccount->name,
            'email' => $obAccount->email
        ];
        $_SESSION['login_timeout'] = time();
        return true;
    }

    public static function isLogged()
    {
        self::init();
        if (isset($_SESSION['login_timeout'])) {
            if (time() - $_SESSION['login_timeout'] > 1800) {
                unset($_SESSION['login_timeout']);
                unset($_SESSION['account']['user']);
                return false;
            } else {
                session_regenerate_id(true);
                return isset($_SESSION['account']['user']['id']);
            }
        } else {
            unset($_SESSION['account']['user']);
            return false;
        }
    }

    public static function idLogged()
    {
        self::init();
        return $_SESSION['account']['user']['id'];
    }

    public static function logout()
    {
        self::init();
        unset($_SESSION['account']['user']);
        unset($_SESSION['login_timeout']);
        return true;
    }
}

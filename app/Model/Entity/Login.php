<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Entity;

use App\DatabaseManager\Database;

class Login{
    
    public $id;

    public $email;

    public $password;

    public function createAccount()
    {
        $this->id = (new Database('accounts'))->insert([
            'account' => $this->id,
            'email' => $this->email,
            'password' => $this->password
        ]);
        return true;
    }

    public function updateAccount()
    {
        return (new Database('accounts'))->update([ 'id' => $this->id], [
            'account' => $this->id,
            'email' => $this->email,
            'password' => $this->password
        ]);
    }

    public function deleteAccount()
    {
        return (new Database('accounts'))->delete([ 'id' => $this->id]);
    }

    public static function getAccountbyId($id)
    {
        return self::getAccounts([ 'id' => $id])->fetchObject(self::class);
    }

    public static function getLoginbyEmail($email)
    {
        return self::getAccounts([ 'email' => $email])->fetchObject(self::class);
    }

    public static function getAccounts($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('accounts'))->select($where, $order, $limit, $fields);
    }
}
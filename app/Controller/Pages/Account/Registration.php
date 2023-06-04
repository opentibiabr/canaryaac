<?php
/**
 * Registration Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages\Account;

use \App\Utils\View;
use App\Controller\Pages\Base;
use App\Model\Entity\Account as EntityAccount;
use App\Session\Admin\Login as SessionAdminLogin;

class Registration extends Base{

    public static function generateKey()
    {
        $caracteres = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E','F','G','H','I','J','K','L','M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $key = '';
        $max = count($caracteres)-1;
        for($i = 0; $i < 16; $i++){
            $key .= (!($i % 4) && $i ? '-' : '').$caracteres[rand(0, $max)];
        }
        return $key;
    }

    public static function brazilianPhoneParser($phoneString, $forceOnlyNumber = true)
    {
        $phoneString = preg_replace('/[()]/', '', $phoneString);
        if (preg_match('/^(?:(?:\+|00)?(55)\s?)?(?:\(?([0-0]?[0-9]{1}[0-9]{1})\)?\s?)??(?:((?:9\d|[2-9])\d{3}\-?\d{4}))$/', $phoneString, $matches) === false) {
            return null;
        }

        $ddi = $matches[1] ?? '';
        $ddd = preg_replace('/^0/', '', $matches[2] ?? '');
        $number = $matches[3] ?? '';
        if ($forceOnlyNumber === true) {
            $number = preg_replace('/-/', '', $number);
        }
        return ['ddi' => $ddi, 'ddd' => $ddd , 'number' => $number];
    }

    public static function insertRegister($request)
    {
        $LoggedId = SessionAdminLogin::idLogged();
        $postVars = $request->getPostVars();

        $firstname = $postVars['firstname'];
        $lastname = $postVars['lastname'];
        $street = $postVars['street'];
        $housenr = $postVars['housenr'];
        $additional = $postVars['additional'];
        $zip = $postVars['zip'];
        $city = $postVars['city'];
        $countryid = $postVars['countryid'];
        $province = $postVars['province'];
        $mobilenumber = $postVars['mobilenumber'];
        $password = $postVars['password'];

        $filterPassword = filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
        $convertPassword = sha1($filterPassword);

        $filter_street = filter_var($street, FILTER_SANITIZE_SPECIAL_CHARS);
        
        $filter_additional = filter_var($additional, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_zip = filter_var($zip, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_city = filter_var($city, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_countryid = filter_var($countryid, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_province = filter_var($province, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_mobilenumber = filter_var($mobilenumber, FILTER_SANITIZE_SPECIAL_CHARS);


        $filter_firstname = filter_var($firstname, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_lastname = filter_var($lastname, FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_housenr = filter_var($housenr, FILTER_SANITIZE_SPECIAL_CHARS);


        
        if(empty($filter_firstname)){
            return self::getRegistration($request, 'You need to set a firstname.');
        }
        
        if(empty($filter_lastname)){
            return self::getRegistration($request, 'You need to set a lastname.');
        }

        
        if(!filter_var($filter_housenr, FILTER_VALIDATE_INT)){
            return self::getRegistration($request, 'You need to set a lastname.');
        }

        
        if(self::brazilianPhoneParser($filter_mobilenumber) == false){
            return self::getRegistration($request, 'Invalid phone.');
        }
        

        $accountLogged = EntityAccount::getAccount([ 'id' => $LoggedId])->fetchObject();
        if($accountLogged->password != $convertPassword){
            return self::getRegistration($request, 'Error');
        }

        $InsertRegistration = [
            'account_id' => $LoggedId,
            'recovery' => self::generateKey(),
            'firstname' => $filter_firstname,
            'lastname' => $filter_lastname,
            'address' => $filter_street,
            'housenumber' => $filter_housenr,
            'additional' => $filter_additional,
            'postalcode' => $filter_zip,
            'city' => $filter_city,
            'country' => $filter_countryid,
            'state' => $filter_province,
            'mobile' => $filter_mobilenumber,
        ];
        $UpdateRegistration = [
            'account_id' => $LoggedId,
            'firstname' => $filter_firstname,
            'lastname' => $filter_lastname,
            'address' => $filter_street,
            'housenumber' => $filter_housenr,
            'additional' => $filter_additional,
            'postalcode' => $filter_zip,
            'city' => $filter_city,
            'country' => $filter_countryid,
            'state' => $filter_province,
            'mobile' => $filter_mobilenumber,
        ];

        $selectRegister = EntityAccount::getAccountRegistration([ 'account_id' => $LoggedId])->fetchObject();
        if($selectRegister == true){
            $update = EntityAccount::updateRegister('account_id = "'.$LoggedId.'"', $UpdateRegistration);
            return self::getRegistration($request, 'Updated successfully.');
        }else{
            $insert = EntityAccount::insertRegister($InsertRegistration);
        }

        $recoveryKeyReturn = EntityAccount::getAccountRegistration([ 'account_id' => $LoggedId])->fetchObject();

        $content = View::render('pages/account/registration_confirm', [
            'recoverykey' => $recoveryKeyReturn->recovery,
        ]);
        return parent::getBase('Account Registration', $content);
    }

    public static function getAccRegister()
    {
        if(SessionAdminLogin::isLogged() == true){
            $LoggedId = SessionAdminLogin::idLogged();
            $selectRegister = EntityAccount::getAccountRegistration([ 'account_id' => $LoggedId])->fetchObject();
            if($selectRegister == true){
                $account = [
                    'status' => true,
                    'firstname' => $selectRegister->firstname,
                    'lastname' => $selectRegister->lastname,
                    'address' => $selectRegister->address,
                    'housenumber' => $selectRegister->housenumber,
                    'additional' => $selectRegister->additional,
                    'postalcode' => $selectRegister->postalcode,
                    'city' => $selectRegister->city,
                    'country' => $selectRegister->country,
                    'state' => $selectRegister->state,
                    'mobile' => $selectRegister->mobile,
                ];
            }else{
                $account = [
                    'status' => false,
                ];
            }
        }
        return $account;
    }

    public static function getRegistration($request, $status = null)
    {
        $content = View::render('pages/account/registration', [
            'account' => self::getAccRegister(),
            'status' => $status,
        ]);
        return parent::getBase('Account Registration', $content, 'account');
    }

}
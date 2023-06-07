<?php
/**
 * CheckCharacterName Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Api;

use App\Model\Entity\Player as EntityPlayer;
use Exception;

class CheckCharacterName extends Api{

    public static function checkCharacterName($request)
    {
        $postVars = $request->getPostVars();
        if (empty($postVars['name'])) {
            throw new Exception('error', 404);
        }
        $filter_name = filter_var($postVars['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $select = EntityPlayer::getPlayer([ 'name' => $filter_name])->fetchObject();
        if (empty($select)) {
            throw new Exception('success', 200);
        } else {
            throw new Exception('error', 404);
        }
    }

    public static function getCharacterName($request)
    {
        return self::checkCharacterName($request);
    }
    
}
<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\ServerConfig as EntityServerConfig;

class Downloads extends Base
{
    public static function viewDownloads()
    {
        $dbServer = EntityServerConfig::getInfoWebsite()->fetchObject();
        $content = View::render('pages/downloads', [
            'download_link' => $dbServer->downloads,
        ]);
        return parent::getBase('Downloads', $content, 'downloads');
    }
}

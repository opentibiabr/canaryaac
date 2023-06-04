<?php
/**
 * Settings Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Functions\Server as FunctionsServer;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use DateTimeZone;

class Settings extends Base
{
    public static function insertWorld($request)
    {
        $postVars = $request->getPostVars();
        if (isset($postVars['website_update'])) {
            if (empty($postVars['website_title'])) {
                $status = Alert::getError('Defina um nome.');
                return self::viewSettings($request, $status);
            }
            $filter_title = filter_var($postVars['website_title'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($postVars['website_download'])) {
                $status = Alert::getError('Defina um link de download.');
                return self::viewSettings($request, $status);
            }
            $filter_download = filter_var($postVars['website_download'], FILTER_SANITIZE_SPECIAL_CHARS);
            if (!filter_var($filter_download, FILTER_VALIDATE_URL)) {
                $status = Alert::getError('Defina uma URL válida.');
                return self::viewSettings($request, $status);
            }

            if (empty($postVars['website_vocation'])) {
                $filter_vocation = 0;
            } else {
                $filter_vocation = 1;
            }
            if ($filter_vocation > 1) {
                $status = Alert::getError('Defina se está ativo as vocações.');
                return self::viewSettings($request, $status);
            }

            if (empty($postVars['website_donates'])) {
                $filter_donates = 0;
            } else {
                $filter_donates = 1;
            }
            if ($filter_donates > 1) {
                $status = Alert::getError('Defina se está ativo os donates.');
                return self::viewSettings($request, $status);
            }

            if (empty($postVars['website_maxplayers'])) {
                $status = Alert::getError('Defina o máximo de players por conta.');
                return self::viewSettings($request, $status);
            }
            $filter_maxplayers = filter_var($postVars['website_maxplayers'], FILTER_SANITIZE_NUMBER_INT);
            if (!filter_var($filter_maxplayers, FILTER_VALIDATE_INT)) {
                $status = Alert::getError('Defina o máximo de players por conta.');
                return self::viewSettings($request, $status);
            }

            if (empty($postVars['website_levelguild'])) {
                $status = Alert::getError('Defina o level minimo para Guilds.');
                return self::viewSettings($request, $status);
            }
            $filter_levelguild = filter_var($postVars['website_levelguild'], FILTER_SANITIZE_NUMBER_INT);
            if (!filter_var($filter_levelguild, FILTER_VALIDATE_INT)) {
                $status = Alert::getError('Defina o level minimo para Guilds.');
                return self::viewSettings($request, $status);
            }

            if (empty($postVars['website_timezone'])) {
                $status = Alert::getError('Defina o timezone.');
                return self::viewSettings($request, $status);
            }
            $filter_timezone = filter_var($postVars['website_timezone'], FILTER_SANITIZE_SPECIAL_CHARS);

            EntityServerConfig::updateInfoWebsite('id = 1', [
                'title' => $filter_title,
                'downloads' => $filter_download,
                'player_voc' => $filter_vocation,
                'player_max' => $filter_maxplayers,
                'player_guild' => $filter_levelguild,
                'donates' => $filter_donates,
                'timezone' => $filter_timezone
            ]);
            $status = Alert::getSuccess('Information successfully updated.');
            $sweetAlert = SweetAlert::Types('Success', 'Information successfully updated.', 'success', 'btn btn-success');
            return self::viewSettings($request, $status, $sweetAlert);
        }
    }

    public static function viewSettings($request, $status = null, $sweetAlert = null)
    {
        $timeZones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $dbServer = EntityServerConfig::getInfoWebsite()->fetchObject();
        $content = View::render('admin/modules/settings/index', [
            'status' => $status,
            'sweetAlert' => $sweetAlert,
            'title' => $dbServer->title,
            'download_link' => $dbServer->downloads,
            'player_voc' => $dbServer->player_voc,
            'player_max' => $dbServer->player_max,
            'player_guild' => $dbServer->player_guild,
            'webtimezone' => $dbServer->timezone,
            'all_timezones' => $timeZones,
            'active_donates' => $dbServer->donates,
            'worlds' => FunctionsServer::getWorlds(),
        ]);
        return parent::getPanel('Settings', $content, 'settings');
    }
}

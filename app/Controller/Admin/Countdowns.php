<?php
/**
 * Countdowns Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Functions\Upload as FunctionsUpload;
use App\Model\Entity\Countdowns as EntityCountdowns;
use App\Utils\View;

class Countdowns extends Base
{
    public static function insertCountdown($request)
    {
        $postFiles = $request->getPostFiles();
        $postVars = $request->getPostVars();

        if (empty($postVars['countdown_date_end'])) {
            $status = SweetAlert::Types('Error!', '', 'error', 'btn btn-danger');
            return self::viewCountdowns($request, $status);
        }
        if (empty($postVars['countdown_time_end'])) {
            $status = SweetAlert::Types('Error!', '', 'error', 'btn btn-danger');
            return self::viewCountdowns($request, $status);
        }
        $final_date_end = $postVars['countdown_date_end'] . ' ' . $postVars['countdown_time_end'];

        if (isset($postFiles['countdown_themebox'])) {
            $upload = new FunctionsUpload($postFiles['countdown_themebox']);

            if ($upload->getExtension() != 'png') {
                $status = SweetAlert::Types('Error!', 'Invalid file extension.', 'error', 'btn btn-danger');
                return self::viewCountdowns($request, $status);
                exit;
            }
            $success = $upload->upload('images/global/themeboxes/anniversary', false);
            /*
            if ($success) {
                $status = SweetAlert::Types('Success!', $upload->getBaseName(), 'success', 'btn btn-success');;
                return self::viewCountdowns($request, $status);
            }
            */
        }


        EntityCountdowns::insertCountdowns([
            'date_start' => strtotime(date('Y-m-d H:i:s')),
            'date_end' => strtotime($final_date_end),
            'themebox' => $upload->getUrl(),
        ]);

        $status = SweetAlert::Types('Success!', $upload->getBaseName(), 'success', 'btn btn-success');
        return self::viewCountdowns($request, $status);
    }

    public static function getAllCountdowns()
    {
        $select_countdowns = EntityCountdowns::getCountdowns(null, 'date_end DESC');
        while ($countdown = $select_countdowns->fetchObject()) {
            $arrayCountdown[] = [
                'date_start' => date('M d Y H:i', $countdown->date_start),
                'date_end' => date('M d Y H:i', $countdown->date_end),
                'themebox' => $countdown->themebox
            ];
        }
        return $arrayCountdown ?? '';
    }

    public static function getCurrentCountdown()
    {
        $countdown = EntityCountdowns::getCountdowns(null, 'date_end DESC', 1)->fetchObject();
        if (empty($countdown)) {
            return '';
        }
        $arrayCountdown = [
            'date_start' => date('M d Y H:i', $countdown->date_start),
            'date_end' => date('M d Y H:i', $countdown->date_end),
            'themebox' => $countdown->themebox
        ];
        return $arrayCountdown;
    }

    public static function viewCountdowns($request, $status = null)
    {
        $content = View::render('admin/modules/countdowns/index', [
            'sweetAlert' => $status,
            'countdowns' => self::getAllCountdowns(),
            'current_countdown' => self::getCurrentCountdown()
        ]);
        return parent::getPanel('Countdowns', $content, 'countdowns');
    }
}

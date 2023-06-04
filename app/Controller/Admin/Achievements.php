<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\Achievements as EntityAchievements;
use App\Model\Functions\Achievements as FunctionsAchievements;
use App\Utils\View;

class Achievements extends Base
{
    public static function importAchievements($request)
    {
        $postVars = $request->getPostVars();
        if (empty($postVars['achievements_name'])) {
            $status = Alert::getError('Insira um nome.');
            return self::viewAchievements($request, $status);
        }
        if (empty($postVars['achievements_grade'])) {
            $status = Alert::getError('Selecione uma grade.');
            return self::viewAchievements($request, $status);
        }
        if (empty($postVars['achievements_points'])) {
            $status = Alert::getError('Adicione uma quantidade de pontos.');
            return self::viewAchievements($request, $status);
        }
        if (empty($postVars['achievements_description'])) {
            $status = Alert::getError('Insira uma descrição.');
            return self::viewAchievements($request, $status);
        }
        if (empty($postVars['achievements_storage'])) {
            $status = Alert::getError('Adicione uma storage.');
            return self::viewAchievements($request, $status);
        }

        if (!filter_var($postVars['achievements_grade'], FILTER_VALIDATE_INT)) {
            $status = Alert::getError('Grade inválida.');
            return self::viewAchievements($request, $status);
        }
        if ($postVars['achievements_grade'] > 4) {
            $status = Alert::getError('Grade inválida.');
            return self::viewAchievements($request, $status);
        }

        if (!filter_var($postVars['achievements_points'], FILTER_VALIDATE_INT)) {
            $status = Alert::getError('Points inválido.');
            return self::viewAchievements($request, $status);
        }
        if (!filter_var($postVars['achievements_storage'], FILTER_VALIDATE_INT)) {
            $status = Alert::getError('Storage inválida.');
            return self::viewAchievements($request, $status);
        }
        if (isset($postVars['achievements_secret'])) {
            if (!filter_var($postVars['achievements_secret'], FILTER_VALIDATE_INT)) {
                $status = Alert::getError('Secret inválida.');
                return self::viewAchievements($request, $status);
            }
            $filter_secret = filter_var($postVars['achievements_secret'], FILTER_SANITIZE_NUMBER_INT);
        }

        $filter_name = filter_var($postVars['achievements_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_description = filter_var($postVars['achievements_description'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filter_grade = filter_var($postVars['achievements_grade'], FILTER_SANITIZE_NUMBER_INT);
        $filter_points = filter_var($postVars['achievements_points'], FILTER_SANITIZE_NUMBER_INT);
        $filter_storage = filter_var($postVars['achievements_storage'], FILTER_SANITIZE_NUMBER_INT);

        EntityAchievements::insertAchievements([
            'name' => $postVars['achievements_name'],
            'description' => $postVars['achievements_description'],
            'grade' => $filter_grade,
            'points' => $filter_points,
            'storage' => $filter_storage,
            'secret' => $filter_secret ?? 0,
        ]);
        $status = Alert::getSuccess('Achievement criado com sucesso.');
        return self::viewAchievements($request, $status);
    }

    public static function viewAchievements($request, $status = null)
    {
        $content = View::render('admin/modules/achievements/index', [
            'status' => $status,
            'achievements' => FunctionsAchievements::getAllAchievements(),
        ]);
        return parent::getPanel('Achievements', $content, 'achievements');
    }
}

<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\Achievements as EntityAchievements;

class Achievements{

    public static function getAllAchievements()
    {
        $select_achievements = EntityAchievements::getAchievements();
        while ($achievements = $select_achievements->fetchObject()) {
            $base_storage = 300000 + $achievements->storage;

            if ($achievements->secret == 1) {
                $achievements_secret = 'True';
            } else {
                $achievements_secret = 'False';
            }

            $arrayAchievements[] = [
                'id' => $achievements->id,
                'name' => $achievements->name,
                'grade' => $achievements->grade,
                'secret' => $achievements_secret,
                'points' => $achievements->points,
                'description' => $achievements->description,
                'storage' => $base_storage,
            ];
        }
        return $arrayAchievements ?? '';
    }

    public static function getAchievementByGrade($grade_id = 1)
    {
        $select_achievements = EntityAchievements::getAchievements([ 'grade' => $grade_id]);
        while ($achievements = $select_achievements->fetchObject()) {
            $base_storage = 300000 + $achievements->storage;

            if ($achievements->secret == 1) {
                $achievements_secret = 'True';
            } else {
                $achievements_secret = 'False';
            }

            $arrayAchievements[] = [
                'id' => $achievements->id,
                'name' => $achievements->name,
                'grade' => $achievements->grade,
                'secret' => $achievements_secret,
                'points' => $achievements->points,
                'description' => $achievements->description,
                'storage' => $base_storage,
            ];
        }
        return $arrayAchievements;
    }

    public static function getAchievementPlayer($grade_id = 1, $player_id)
    {
        $select_achievements = EntityAchievements::getAchievements([ 'grade' => $grade_id]);
        while ($achievements = $select_achievements->fetchObject()) {
            $base_storage = 300000 + $achievements->storage;

            $select_playerStorage = Player::getPlayerStorage($player_id, $base_storage, 1);
            if ($select_playerStorage == true) {}

            

            if ($achievements->secret == 1) {
                $achievements_secret = 'True';
            } else {
                $achievements_secret = 'False';
            }

            $arrayAchievements[] = [
                'id' => $achievements->id,
                'name' => $achievements->name,
                'grade' => $achievements->grade,
                'secret' => $achievements_secret,
                'points' => $achievements->points,
                'description' => $achievements->description,
                'storage' => $base_storage,
            ];
        }
        return $arrayAchievements;
    }

}
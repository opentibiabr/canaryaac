<?php
/**
 * ThemeBox Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Entity\Polls as EntityPolls;
use App\Model\Functions\Player as FunctionsPlayer;
use App\Model\Entity\Countdowns as EntityCountdowns;
use App\Model\Entity\Player;

class ThemeBox
{
    public static function getRashidLocation()
    {
        $daysOfWeek = array(
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        );

        $curDate = date('Y-m-d');

        $dayNumber = date('w', strtotime($curDate));

        $rashidLocation = '';

        switch($daysOfWeek[$dayNumber]){
            case 'Monday':
                $rashidLocation = 'Carlin';
                break;
            case 'Tuesday':
                $rashidLocation = 'Svargrond';
                break;
            case 'Wednesday':
                $rashidLocation = 'Liberty Bay';
                break;
            case 'Thursday':
                $rashidLocation = 'Port Hope';
                break;
            case 'Friday':
                $rashidLocation = 'Ankrahmun';
                break;
            case 'Saturday':
                $rashidLocation = 'Darashia';
                break;
            case 'Sunday':
                $rashidLocation = 'Edron';
                break;
            default:
                $rashidLocation = 'Unknown';
                break;                
        }

        return $rashidLocation;
    }
    
    public static function getHighscoresTop5()
    {
        $ribbon = 0;
        $select_players = Player::getPlayer([ 'deletion =' => "0", 'group_id <=' => "3"], 'level DESC', 5);
        while ($player = $select_players->fetchObject()) {
            $ribbon++;
            $arrayPlayers[] = [
                'name' => $player->name,
                'level' => $player->level,
                'vocation' => FunctionsPlayer::convertVocation($player->vocation),
                'outfit' => FunctionsPlayer::getOutfit($player->id),
                'online' => FunctionsPlayer::isOnline($player->id),
                'ribbon' => URL . '/resources/images/global/themeboxes/highscores/rank_' . $ribbon . '.png'
            ];
        }
        return $arrayPlayers ?? '';
    }

    public static function getCurrentPoll()
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $currentDate = strtotime(date('Y-m-d 23:59:59'));
        $poll = EntityPolls::getPolls(null, 'date_end DESC', 1)->fetchObject();
        if (empty($poll)) {
            return '';
        }

        if ($poll->date_end > $currentDate) {
            $arrayPolls = [
                'id' => $poll->id,
                'player_id' => $poll->player_id,
                'title' => $poll->title,
                'description' => $poll->description,
                'date_start' => date('M d Y', $poll->date_start),
                'date_end' => date('M d Y', $poll->date_end)
            ];
        }
        return $arrayPolls ?? '';
    }

    public static function getCurrentCountdown()
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);
        
        $countdown = EntityCountdowns::getCountdowns(null, 'date_end DESC', 1)->fetchObject();
        if (empty($countdown)) {
            return '';
        }
        if ($countdown->date_end >= strtotime(date('Y-m-d H:i:s'))) {
            $arrayCountdown = [
                'date_start' => date('M d Y H:i', $countdown->date_start),
                'date_end' => $countdown->date_end,
                'themebox' => $countdown->themebox
            ];
        }
        return $arrayCountdown ?? '';
    }

}

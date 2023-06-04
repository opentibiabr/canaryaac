<?php
/**
 * Polls Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\Polls as EntityPolls;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Session\Admin\Login as SessionPlayerLogin;

class Polls
{

    public static function getCurrentPolls()
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $currentDate = strtotime(date('Y-m-d 23:59:59'));
        $select_Polls = EntityPolls::getPolls(null, 'date_end DESC');
        $arrayPolls = [];
        while ($poll = $select_Polls->fetchObject()) {
            if ($poll->date_end >= $currentDate) {
                $arrayPolls[] = [
                    'id' => $poll->id,
                    'player_id' => $poll->player_id,
                    'title' => $poll->title,
                    'description' => $poll->description,
                    'date_start' => date('M d Y', $poll->date_start),
                    'date_end' => date('M d Y', $poll->date_end)
                ];
            }
        }
        return $arrayPolls;
    }

    public static function getEndPolls()
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $currentDate = strtotime(date('Y-m-d 23:59:59'));
        $select_Polls = EntityPolls::getPolls(null, 'date_end DESC');
        $arrayPolls = [];
        while ($poll = $select_Polls->fetchObject()) {
            if ($poll->date_end < $currentDate) {
                $arrayPolls[] = [
                    'id' => $poll->id,
                    'player_id' => $poll->player_id,
                    'title' => $poll->title,
                    'description' => $poll->description,
                    'date_start' => date('M d Y', $poll->date_start),
                    'date_end' => date('M d Y', $poll->date_end)
                ];
            }
        }
        return $arrayPolls;
    }

    public static function getTotalVotesbyPollId($poll_id)
    {
        $filter_PollId = filter_var($poll_id, FILTER_SANITIZE_NUMBER_INT);
        $total_votes = 0;
        $select_questions = EntityPolls::getPollQuestions([ 'poll_id' => $filter_PollId]);
        while ($question = $select_questions->fetchObject()) {
            $total_votes += $question->votes;
        }
        return $total_votes ?? 0;
    }

    public static function getQuestionsByPollIdAdmin($poll_id)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $filter_PollId = filter_var($poll_id, FILTER_SANITIZE_NUMBER_INT);
        $select_questions = EntityPolls::getPollQuestions([ 'poll_id' => $filter_PollId]);
        if (empty($select_questions)) {
            return null;
        }
        while ($question = $select_questions->fetchObject()) {

            $total_votes = self::getTotalVotesbyPollId($filter_PollId);
            if ($total_votes != 0) {
                $percent = $question->votes * 100 / $total_votes;
            } else {
                $percent = 0;
            }
            $arrayQuestions[] = [
                'id' => $question->id,
                'question' => $question->question,
                'description' => $question->description,
                'votes' => $question->votes,
                'total_votes' => $total_votes,
                'percent' => $percent
            ];
        }
        return $arrayQuestions ?? '';
    }

    public static function getQuestionsByPollId($poll_id)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        if (SessionPlayerLogin::isLogged()) {
            $account_id = SessionPlayerLogin::idLogged();
        }

        $select_questions = EntityPolls::getPollQuestions([ 'poll_id' => $poll_id]);
        while ($question = $select_questions->fetchObject()) {
            $answer = EntityPolls::getPollAnswer([ 'account_id' => $account_id, 'poll_id' => $question->poll_id])->fetchObject();
            if ($answer) {
                if ($answer->question_id == $question->id) {
                    $arrayQuestions[] = [
                        'id' => $question->id,
                        'question' => $question->question,
                        'description' => $question->description,
                        'votes' => $question->votes,
                        'answer' => true
                    ];
                } else {
                    $arrayQuestions[] = [
                        'id' => $question->id,
                        'question' => $question->question,
                        'description' => $question->description,
                        'votes' => $question->votes,
                        'answer' => false
                    ];
                }
            } else {
                $arrayQuestions[] = [
                    'id' => $question->id,
                    'question' => $question->question,
                    'description' => $question->description,
                    'votes' => $question->votes,
                    'answer' => false
                ];
            }
        }
        return $arrayQuestions ?? '';
    }

    public static function verifyAccountQuestion($account_id, $question_id)
    {
        $answer = EntityPolls::getPollAnswer([ 'account_id' => $account_id, 'question_id' => $question_id])->fetchObject();
        if (empty($answer)) {
            return false;
        } else {
            return true;
        }
    }

    public static function verifyAccountAnswer($account_id, $poll_id)
    {
        $answer = EntityPolls::getPollAnswer([ 'account_id' => $account_id,'poll_id' => $poll_id])->fetchObject();
        if (empty($answer)) {
            return false;
        } else {
            return true;
        }
    }

}
<?php
/**
 * Polls Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Entity;

use App\DatabaseManager\Database;

class Polls
{
    public static function getPolls($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_polls'))->select($where, $order, $limit, $fields);
    }

    public static function insertPoll($values = null)
    {
        return (new Database('canary_polls'))->insert($values);
    }

    public static function updatePoll($where = null, $values = null)
    {
        return (new Database('canary_polls'))->update($where, $values);
    }

    public static function deletePoll($where = null)
    {
        return (new Database('canary_polls'))->delete($where);
    }

    public static function getPollQuestions($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_polls_questions'))->select($where, $order, $limit, $fields);
    }

    public static function insertPollQuestions($values = null)
    {
        return (new Database('canary_polls_questions'))->insert($values);
    }

    public static function updatePollQuestions($where = null, $values = null)
    {
        return (new Database('canary_polls_questions'))->update($where, $values);
    }

    public static function deletePollQuestions($where = null)
    {
        return (new Database('canary_polls_questions'))->delete($where);
    }

    public static function getPollAnswer($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('canary_polls_answers'))->select($where, $order, $limit, $fields);
    }

    public static function insertPollAnswer($values = null)
    {
        return (new Database('canary_polls_answers'))->insert($values);
    }

    public static function updatePollAnswer($where = null, $values = null)
    {
        return (new Database('canary_polls_answers'))->update($where, $values);
    }

    public static function deletePollAnswer($where = null)
    {
        return (new Database('canary_polls_answers'))->delete($where);
    }
}

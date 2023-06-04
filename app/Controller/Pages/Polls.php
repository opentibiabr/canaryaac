<?php
/**
 * Polls Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\DatabaseManager\Pagination;
use App\Model\Entity\Polls as EntityPolls;
use App\Model\Functions\Polls as FunctionsPolls;
use App\Session\Admin\Login as SessionPlayerLogin;
use App\Model\Entity\ServerConfig as EntityServerConfig;

use App\Utils\View;

class Polls extends Base
{
    public static function insertAnswer($request, $id)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $postVars = $request->getPostVars();
        $AccountId = SessionPlayerLogin::idLogged();

        $select_poll = EntityPolls::getPolls('id = "'.$id.'"')->fetchObject();
        if (empty($select_poll)) {
            $request->getRouter()->redirect('/community/polls');
        }

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return self::viewPollById($request, $id);
        }
        if (empty($postVars['poll_question'])) {
            return self::viewPollById($request, $id);
        }
        if (!filter_var($postVars['poll_question'], FILTER_VALIDATE_INT)) {
            return self::viewPollById($request, $id);
        }
        $select_questions = EntityPolls::getPollQuestions('id = "'.$postVars['poll_question'].'"')->fetchObject();
        if (empty($select_questions)) {
            $request->getRouter()->redirect('/community/polls');
        }
        $total_votes = $select_questions->votes + 1;
        EntityPolls::insertPollAnswer([
            'poll_id' => $id,
            'account_id' => $AccountId,
            'question_id' => $postVars['poll_question'],
            'date' => strtotime(date('Y-m-d')),
        ]);
        EntityPolls::updatePollQuestions('id = "'.$postVars['poll_question'].'"', [
            'votes' => $total_votes
        ]);
        $content = View::render('pages/polls/submitted', [

        ]);
        return parent::getBase('Polls', $content, 'polls');
    }

    public static function viewPollById($request, $id)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        if(!filter_var($id, FILTER_VALIDATE_INT)) {
            $request->getRouter()->redirect('/community/polls');
        }
        $AccountId = SessionPlayerLogin::idLogged();

        $select_poll = EntityPolls::getPolls('id = "'.$id.'"')->fetchObject();
        if (empty($select_poll)) {
            $request->getRouter()->redirect('/community/polls');
        }

        $currentDate = strtotime(date('Y-m-d'));
        if ($select_poll->date_end < $currentDate) {
            $active_poll = true;
        }

        $arrayPolls = [
            'id' => $select_poll->id,
            'player_id' => $select_poll->player_id,
            'title' => $select_poll->title,
            'description' => $select_poll->description,
            'date_start' => date('M d Y', $select_poll->date_start),
            'date_end' => date('M d Y', $select_poll->date_end)
        ];

        $content = View::render('pages/polls/view', [
            'poll' => $arrayPolls,
            'status' => $active_poll ?? false,
            'questions' => FunctionsPolls::getQuestionsByPollId($id),
            'answer' => FunctionsPolls::verifyAccountAnswer($AccountId, $id),
        ]);
        return parent::getBase('Polls', $content, 'polls');
    }

    public static function viewPolls($request)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $queryParams = $request->getQueryParams();
        $totaAmount = EntityPolls::getPolls(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
        $currentPage = $queryParams['page'] ?? 1;
        $obPagination = new Pagination($totaAmount, $currentPage, 10);
        $currentDate = strtotime(date('Y-m-d'));
        $select_Polls = EntityPolls::getPolls('date_end < "'.$currentDate.'"', null, $obPagination->getLimit());

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

        $content = View::render('pages/polls/index', [
            'current_polls' => FunctionsPolls::getCurrentPolls(),
            'end_polls' => $arrayPolls ?? '',
            'pagination' => self::getPagination($request, $obPagination),
        ]);
        return parent::getBase('Polls', $content, 'polls');
    }
}

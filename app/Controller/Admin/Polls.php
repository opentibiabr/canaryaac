<?php
/**
 * Polls Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Model\Entity\Polls as EntityPolls;
use App\Model\Functions\Polls as FunctionsPolls;
use App\Controller\Admin\SweetAlert;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Utils\View;

class Polls extends Base
{
    public static function deletePoll($request, $id)
    {
        if (empty($id)) {
            $request->getRouter()->redirect('/admin/polls');
        }
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $request->getRouter()->redirect('/admin/polls');
        }

        $select_poll = EntityPolls::getPolls('id = "' . $id . '"')->fetchObject();
        if (empty($select_poll)) {
            $request->getRouter()->redirect('/admin/polls');
        }

        EntityPolls::deletePoll('id = "' . $id . '"');
        EntityPolls::deletePollQuestions('poll_id = "' . $id . '"');
        EntityPolls::deletePollAnswer('poll_id = "' . $id . '"');
        $request->getRouter()->redirect('/admin/polls');
    }

    public static function insertNewPoll($request)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);
        $postVars = $request->getPostVars();

        if (empty($postVars['poll_title'])) {
            $status = SweetAlert::Types('Error!', '', 'danger', 'btn btn-danger');
            return self::viewInsertNewPoll($request, $status);
        }
        if (empty($postVars['poll_date_end'])) {
            $status = SweetAlert::Types('Error!', '', 'danger', 'btn btn-danger');
            return self::viewInsertNewPoll($request, $status);
        }
        if (empty($postVars['questions'])) {
            $status = SweetAlert::Types('Error!', '', 'danger', 'btn btn-danger');
            return self::viewInsertNewPoll($request, $status);
        }

        $return_poll_id = EntityPolls::insertPoll([
            'title' => $postVars['poll_title'],
            'description' => $postVars['poll_description'],
            'date_start' => strtotime(date('Y-m-d')),
            'date_end' => strtotime($postVars['poll_date_end']),
        ]);

        foreach ($postVars['questions'] as $key => $value) {
            if (empty($value['question_title'])) {
                $status = SweetAlert::Types('Error!', '', 'danger', 'btn btn-danger');
                return self::viewInsertNewPoll($request, $status);
            }

            EntityPolls::insertPollQuestions([
                'poll_id' => $return_poll_id,
                'question' => $value['question_title'],
                'description' => $value['question_description'],
                'votes' => 0
            ]);
        }
        $status = SweetAlert::Types('Success!', '', 'success', 'btn btn-success');
        return self::viewInsertNewPoll($request, $status);
    }

    public static function viewInsertNewPoll($request, $status = null)
    {
        $content = View::render('admin/modules/polls/new', [
            'sweetAlert' => $status,
        ]);
        return parent::getPanel('Polls', $content, 'polls');
    }

    public static function getPollById($request, $poll_id)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $select_Polls = EntityPolls::getPolls('id = "' . $poll_id . '"')->fetchObject();
        if (empty($select_Polls)) {
            $request->getRouter()->redirect('/admin/polls');
        }
        $arrayPoll = [
            'id' => $select_Polls->id,
            'title' => $select_Polls->title,
            'description' => $select_Polls->description,
            'date_start' => date('M d Y', $select_Polls->date_start),
            'date_end' => date('M d Y', $select_Polls->date_end)
        ];
        return $arrayPoll;
    }

    public static function viewPollById($request, $id, $status = null)
    {
        $content = View::render('admin/modules/polls/view', [
            'sweetAlert' => $status,
            'poll' => self::getPollById($request, $id),
            'questions' => FunctionsPolls::getQuestionsByPollIdAdmin($id)
        ]);
        return parent::getPanel('Polls', $content, 'polls');
    }

    public static function viewPolls($request, $status = null)
    {
        $content = View::render('admin/modules/polls/index', [
            'sweetAlert' => $status,
            'current_polls' => FunctionsPolls::getCurrentPolls(),
            'end_polls' => FunctionsPolls::getEndPolls(),
        ]);
        return parent::getPanel('Polls', $content, 'polls');
    }
}

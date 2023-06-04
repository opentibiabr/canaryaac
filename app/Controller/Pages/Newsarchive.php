<?php
/**
 * Newsarchive Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\News as EntityNews;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Functions\News as FunctionsNews;
use \App\Utils\View;

class Newsarchive extends Base{

    public static function viewNewsArchiveById($request, $id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)){
            $request->getRouter()->redirect('/newsarchive');
        }
        $news = EntityNews::getNews([ 'id' => $id])->fetchObject();
        if (!$news) {
            $request->getRouter()->redirect('/newsarchive');
        }
        $arrayNews = [
            'id' => $news->id,
            'title' => $news->title,
            'body' => $news->body,
            'type' => $news->type,
            'date' => date('M d Y', strtotime($news->date)),
            'time' => date('H:i', strtotime($news->date)),
            'category' => FunctionsNews::convertCategoryName($news->category),
            'category_img' => FunctionsNews::convertCategoryBigImage($news->category),
            'player_id' => $news->player_id,
            'article_text' => $news->article_text,
            'article_image' => $news->article_image,
            'hidden' => $news->hidden,
        ];
        $content = View::render('pages/newsarchive_view', [
            'new' => $arrayNews,
        ]);
        return parent::getBase('Newsarchive', $content, 'newsarchive');
    }

    public static function getAllNews($request)
    {
        $dateformat = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($dateformat->timezone);
        $postVars = $request->getPostVars();

        if (empty($postVars['filter_begin_day'])) {
            $begin_date = date("Y-m-d", strtotime(date("Y-m-d")."-1 month"));
        } else {
            $filter_begin_day = filter_var($postVars['filter_begin_day'], FILTER_SANITIZE_SPECIAL_CHARS);
            $filter_begin_month = filter_var($postVars['filter_begin_month'], FILTER_SANITIZE_SPECIAL_CHARS);
            $filter_begin_year = filter_var($postVars['filter_begin_year'], FILTER_SANITIZE_SPECIAL_CHARS);
            $begin_date = $filter_begin_year . '-' . $filter_begin_month . '-' . $filter_begin_day;
        }
        if (empty($postVars['filter_begin_day'])) {
            $end_date = date("Y-m-d");
        } else {
            $filter_end_day = filter_var($postVars['filter_end_day'], FILTER_SANITIZE_SPECIAL_CHARS);
            $filter_end_month = filter_var($postVars['filter_end_month'], FILTER_SANITIZE_SPECIAL_CHARS);
            $filter_end_year = filter_var($postVars['filter_end_year'], FILTER_SANITIZE_SPECIAL_CHARS);
            $end_date = $filter_end_year . '-' . $filter_end_month . '-' . $filter_end_day;
        }
        $active_type_ticker = 0;
        if (empty($postVars['filter_ticker'])) {
            $postVars['filter_ticker'] = null;
        }
        if ($postVars['filter_ticker'] == 'ticker') {
            $active_type_ticker = 3;
        }
        $active_type_article = 0;
        if (empty($postVars['filter_article'])) {
            $postVars['filter_article'] = null;
        }
        if ($postVars['filter_article'] == 'article') {
            $active_type_article = 2;
        }
        $active_type_news = 0;
        if (empty($postVars['filter_news'])) {
            $postVars['filter_news'] = null;
        }
        if ($postVars['filter_news'] == 'news') {
            $active_type_news = 1;
        }

        $selectNews = EntityNews::getNews(['date BETWEEN' => [$begin_date, $end_date]], 'date ASC');
        while ($news = $selectNews->fetchObject()) {
            if ($news->type == $active_type_ticker) {
                $arrayNews[] = [
                    'id' => $news->id,
                    'title' => $news->title,
                    'body' => $news->body,
                    'type' => FunctionsNews::convertTypeName($news->type),
                    'date' => date('M d Y', strtotime($news->date)),
                    'time' => date('H:i', strtotime($news->date)),
                    'category' => FunctionsNews::convertCategoryName($news->category),
                    'category_img' => FunctionsNews::convertCategoryImage($news->category),
                    'player_id' => $news->player_id,
                    'article_text' => $news->article_text,
                    'article_image' => $news->article_image,
                    'hidden' => $news->hidden,
                ];
            }
            if ($news->type == $active_type_article) {
                $arrayNews[] = [
                    'id' => $news->id,
                    'title' => $news->title,
                    'body' => $news->body,
                    'type' => FunctionsNews::convertTypeName($news->type),
                    'date' => date('M d Y', strtotime($news->date)),
                    'time' => date('H:i', strtotime($news->date)),
                    'category' => FunctionsNews::convertCategoryName($news->category),
                    'category_img' => FunctionsNews::convertCategoryImage($news->category),
                    'player_id' => $news->player_id,
                    'article_text' => $news->article_text,
                    'article_image' => $news->article_image,
                    'hidden' => $news->hidden,
                ];
            }
            if ($news->type == $active_type_news) {
                $arrayNews[] = [
                    'id' => $news->id,
                    'title' => $news->title,
                    'body' => $news->body,
                    'type' => FunctionsNews::convertTypeName($news->type),
                    'date' => date('M d Y', strtotime($news->date)),
                    'time' => date('H:i', strtotime($news->date)),
                    'category' => FunctionsNews::convertCategoryName($news->category),
                    'category_img' => FunctionsNews::convertCategoryImage($news->category),
                    'player_id' => $news->player_id,
                    'article_text' => $news->article_text,
                    'article_image' => $news->article_image,
                    'hidden' => $news->hidden,
                ];
            }
        }
        $filters = [
            'filter_ticker' => $postVars['filter_ticker'] ?? 'ticker',
            'filter_article' => $postVars['filter_article'] ?? 'article',
            'filter_news' => $postVars['filter_news'] ?? 'news',
            'filter_cipsoft' => $postVars['filter_cipsoft'] ?? 0,
            'filter_community' => $postVars['filter_community'] ?? 0,
            'filter_development' => $postVars['filter_development'] ?? 0,
            'filter_support' => $postVars['filter_support'] ?? 0,
            'filter_technical' => $postVars['filter_technical'] ?? 0,
        ];
        $returnNews = [
            'filters' => $filters,
            'news' => $arrayNews ?? '',
            'date' => [
                'to_day' => $postVars['filter_end_day'] ?? date('d'),
                'to_month' => $postVars['filter_end_month'] ?? date('m'),
                'to_year' => $postVars['filter_end_year'] ?? date('Y'),
                'from_day' => $postVars['filter_begin_day'] ?? date('d'),
                'from_month' => $postVars['filter_begin_month'] ?? date('m', strtotime('-1 month')),
                'from_year' => $postVars['filter_begin_year'] ?? date('Y'),
            ],
        ];
        return $returnNews ?? '';
    }

    public static function viewNewsArchive($request)
    {
        $dateformat = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($dateformat->timezone);
        $allNews = self::getAllNews($request);
        $content = View::render('pages/newsarchive', [
            'news' => $allNews['news'],
            'filters' => $allNews['filters'],
            'date' => $allNews['date'],
        ]);
        return parent::getBase('Newsarchive', $content, 'newsarchive');
    }
    
}
<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\News as EntityNews;
use App\Model\Functions\News;
use \App\Utils\View;
use App\Model\Functions\Server;

class Lastnews extends Base{

    public static function getNewsTicker()
    {
        $selectNewsTicker = EntityNews::getNews([ 'type' => 2], 'id DESC', '5');
        $newsarticle = [];
        while($NewsTicker = $selectNewsTicker->fetchObject()){
            $newsarticle[] = [
                'title' => $NewsTicker->title,
                'body' => $NewsTicker->body,
                'type' => $NewsTicker->type,
                'date' => date('M d Y', strtotime($NewsTicker->date)),
                'time' => date('H:i', strtotime($NewsTicker->date)),
                'category' => $NewsTicker->category,
                'category_img' => News::convertCategoryImage($NewsTicker->category),
                'player_id' => $NewsTicker->player_id,
                'article_text' => $NewsTicker->article_text,
                'article_image' => $NewsTicker->article_image,
                'hidden' => $NewsTicker->hidden,
            ];
        }
        return $newsarticle;
    }

    public static function getFeaturedArticle()
    {
        $selectFeaturedArticle = EntityNews::getNews([ 'type' => 3], 'id DESC', '1');
        $featuredarticle = [];
        while($FeaturedArticle = $selectFeaturedArticle->fetchObject()){
            $featuredarticle[] = [
                'title' => $FeaturedArticle->title,
                'body' => $FeaturedArticle->body,
                'type' => $FeaturedArticle->type,
                'date' => date('M d Y', strtotime($FeaturedArticle->date)),
                'time' => date('H:i', strtotime($FeaturedArticle->date)),
                'category' => $FeaturedArticle->category,
                'player_id' => $FeaturedArticle->player_id,
                'article_text' => $FeaturedArticle->article_text,
                'article_image' => $FeaturedArticle->article_image,
                'hidden' => $FeaturedArticle->hidden,
            ];
        }
        return $featuredarticle;
    }

    public static function getNews()
    {
        $selectForum = EntityNews::getNews([ 'type' => 1], 'id DESC', '5');
        $news = [];
        while($ForumNews = $selectForum->fetchObject()){
            $news[] = [
                'title' => $ForumNews->title,
                'body' => $ForumNews->body,
                'type' => $ForumNews->type,
                'date' => date('M d Y', strtotime($ForumNews->date)),
                'time' => date('H:i', strtotime($ForumNews->date)),
                'category' => $ForumNews->category,
                'category_img' => News::convertCategoryBigImage($ForumNews->category),
                'player_id' => $ForumNews->player_id,
                'article_text' => $ForumNews->article_text,
                'article_image' => $ForumNews->article_image,
                'hidden' => $ForumNews->hidden,
            ];
        }
        return $news;
    }

    public static function getLastnews()
    {
        $content = View::render('pages/lastnews', [
            'boostedcreature' => Server::getBoostedCreature(),
            'newsticker' => self::getNewsTicker(),
            'featuredarticle' => self::getFeaturedArticle(),
            'news' => self::getNews(),
        ]);
        return parent::getBase('Lastnews', $content, 'latestnews');
    }
}
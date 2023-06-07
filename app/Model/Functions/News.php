<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use App\Model\Entity\News as EntityNews;
use App\Model\Entity\Player;

class News
{

    public static function convertCategoryImage($category_id)
    {
        switch($category_id)
        {
            case 0:
                return URL . '/resources/images/global/content/newsicon_cipsoft_small.gif';
                exit;
            case 1:
                return URL . '/resources/images/global/content/newsicon_cipsoft_small.gif';
                exit;
            case 2:
                return URL . '/resources/images/global/content/newsicon_community_small.gif';
                exit;
            case 3:
                return URL . '/resources/images/global/content/newsicon_development_small.gif';
                exit;
            case 4:
                return URL . '/resources/images/global/content/newsicon_support_small.gif';
                exit;
            case 5:
                return URL . '/resources/images/global/content/newsicon_technical_small.gif';
                exit;
            default:
                return URL . '/resources/images/global/content/newsicon_cipsoft_small.gif';
                exit;
        }
    }

    public static function convertCategoryBigImage($category_id)
    {
        switch($category_id)
        {
            case 0:
                return URL . '/resources/images/global/content/newsicon_cipsoft_big.gif';
                exit;
            case 1:
                return URL . '/resources/images/global/content/newsicon_cipsoft_big.gif';
                exit;
            case 2:
                return URL . '/resources/images/global/content/newsicon_community_big.gif';
                exit;
            case 3:
                return URL . '/resources/images/global/content/newsicon_development_big.gif';
                exit;
            case 4:
                return URL . '/resources/images/global/content/newsicon_support_big.gif';
                exit;
            case 5:
                return URL . '/resources/images/global/content/newsicon_technical_big.gif';
                exit;
            default:
                return URL . '/resources/images/global/content/newsicon_cipsoft_big.gif';
                exit;
        }
    }

    public static function convertTypeName($type_id)
    {
        switch ($type_id)
        {
            case 0:
                return 'None';
                exit;
            case 1:
                return 'News';
                exit;
            case 2:
                return 'Featured Article';
                exit;
            case 3:
                return 'News Ticker';
                exit;
            default:
                return 'None';
        }
    }

    public static function convertCategoryName($category_id)
    {
        switch($category_id)
        {
            case 0:
                return 'Cipsoft';
                exit;
            case 1:
                return 'Cipsoft';
                exit;
            case 2:
                return 'Community';
                exit;
            case 3:
                return 'Development';
                exit;
            case 4:
                return 'Support';
                exit;
            case 5:
                return 'Technical Issues';
                exit;
            default:
                return 'Cipsoft';
                exit;
        }
    }

    public static function getPlayerName($player_id)
    {
        $select_player = Player::getPlayer([ 'id' => $player_id])->fetchObject();
        return $select_player->name;
    }

    public static function getNewsTicker()
    {
        $selectNewsTicker = EntityNews::getNews([ 'type' => 2]);
        $newsarticle = [];
        while($NewsTicker = $selectNewsTicker->fetchObject()){
            $newsarticle[] = [
                'id' => $NewsTicker->id,
                'title' => $NewsTicker->title,
                'body' => $NewsTicker->body,
                'type' => $NewsTicker->type,
                'date' => date('M d Y', strtotime($NewsTicker->date)),
                'time' => date('H:i', strtotime($NewsTicker->date)),
                'category' => $NewsTicker->category,
                'category_img' => self::convertCategoryImage($NewsTicker->id),
                'category_name' => self::convertCategoryName($NewsTicker->id),
                'player_id' => $NewsTicker->player_id,
                'player_name' => self::getPlayerName($NewsTicker->player_id),
                'article_text' => $NewsTicker->article_text,
                'article_image' => $NewsTicker->article_image,
                'hidden' => $NewsTicker->hidden,
            ];
        }
        return $newsarticle;
    }

    public static function getFeaturedArticle()
    {
        $selectFeaturedArticle = EntityNews::getNews([ 'type' => 3]);
        $featuredarticle = [];
        while($FeaturedArticle = $selectFeaturedArticle->fetchObject()){
            $featuredarticle[] = [
                'id' => $FeaturedArticle->id,
                'title' => $FeaturedArticle->title,
                'body' => $FeaturedArticle->body,
                'type' => $FeaturedArticle->type,
                'date' => date('M d Y', strtotime($FeaturedArticle->date)),
                'time' => date('H:i', strtotime($FeaturedArticle->date)),
                'category' => $FeaturedArticle->category,
                'category_img' => self::convertCategoryImage($FeaturedArticle->id),
                'category_name' => self::convertCategoryName($FeaturedArticle->id),
                'player_id' => $FeaturedArticle->player_id,
                'player_name' => self::getPlayerName($FeaturedArticle->player_id),
                'article_text' => $FeaturedArticle->article_text,
                'article_image' => $FeaturedArticle->article_image,
                'hidden' => $FeaturedArticle->hidden,
            ];
        }
        return $featuredarticle;
    }

    public static function getNews()
    {
        $selectForum = EntityNews::getNews([ 'type' => 1]);
        $news = [];
        while($ForumNews = $selectForum->fetchObject()){
            $news[] = [
                'id' => $ForumNews->id,
                'title' => $ForumNews->title,
                'body' => $ForumNews->body,
                'type' => $ForumNews->type,
                'date' => date('M d Y', strtotime($ForumNews->date)),
                'time' => date('H:i', strtotime($ForumNews->date)),
                'category' => $ForumNews->category,
                'category_img' => self::convertCategoryImage($ForumNews->id),
                'category_name' => self::convertCategoryName($ForumNews->id),
                'player_id' => $ForumNews->player_id,
                'player_name' => self::getPlayerName($ForumNews->player_id),
                'article_text' => $ForumNews->article_text,
                'article_image' => $ForumNews->article_image,
                'hidden' => $ForumNews->hidden,
            ];
        }
        return $news;
    }

}
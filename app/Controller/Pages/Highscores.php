<?php
/**
 * Highscores Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Highscores as EntityHighscores;
use App\Model\Functions\Player;
use App\DatabaseManager\Pagination;

class Highscores extends Base{

    public static function convertCategory($category)
    {
        switch($category){
            case 0:
                $input_category = 'skill_axe';
                break;
            case 1:
                $input_category = 'skill_club';
                break;
            case 2:
                $input_category = 'skill_dist';
                break;
            case 3:
                $input_category = 'level';
                break;
            case 4:
                $input_category = 'skill_fishing';
                break;
            case 5:
                $input_category = 'skill_fist';
                break;
            case 6:
                $input_category = 'maglevel';
                break;
            case 7:
                $input_category = 'skill_shielding';
                break;
            case 8:
                $input_category = 'skill_sword';
                break;
        }
        return $input_category;
    }

    public static function getPlayers($request,&$obPagination)
    {
        $player = [];
        $queryParams = $request->getQueryParams();

        $input_profession = filter_var($queryParams['profession'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        if($input_profession > 5){
            $input_profession = 5;
        }
        if(empty($input_profession)){
            $input_profession = 5;
        }

        $input_category = filter_var($queryParams['category'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        if($input_category > 8){
            $input_category = 3;
        }
        if(empty($input_category)){
            $input_category = 3;
        }
        $input_category = self::convertCategory($input_category);
        
        

        if($input_profession == 5){
            $totaAmount = EntityHighscores::getHighscoresEntity(['group_id' <= 3], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        }else{
            $totaAmount = EntityHighscores::getHighscoresEntity(['vocation' => $input_profession, 'group_id' <= 3], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;

        }

        $currentPage = $queryParams['page'] ?? 1;
        $obPagination = new Pagination($totaAmount, $currentPage, 50);

        if($input_profession == 5){
            $results = EntityHighscores::getHighscoresEntity(['group_id' <= 3], $input_category . ' DESC', $obPagination->getLimit());
        }else{
            $results = EntityHighscores::getHighscoresEntity(['vocation' => $input_profession, 'group_id' <= 3], $input_category . ' DESC', $obPagination->getLimit());
        }
        
        while($obRank = $results->fetchObject(EntityHighscores::class)){
            $player[] = [
                'name' => $obRank->name,
                'vocation' => Player::convertVocation($obRank->vocation),
                'level' => $obRank->level,
                'experience' => $obRank->experience,
                'skill_axe' =>$obRank->skill_axe,
                'skill_club' =>$obRank->skill_club,
                'skill_dist' =>$obRank->skill_dist,
                'skill_fishing' =>$obRank->skill_fishing,
                'skill_fist' =>$obRank->skill_fist,
                'maglevel' =>$obRank->maglevel,
                'skill_shielding' =>$obRank->skill_shielding,
                'skill_sword' =>$obRank->skill_sword,
                'online' => Player::isOnline($obRank->id),
            ];
        }

        $ranks = [
            'category' => $input_category,
            'profession' => $input_profession,
            'allplayers' => $player,
        ];
        return $ranks;
    }

    public static function getHighscores($request)
    {
        $content = View::render('pages/community/highscores', [
            'players' => self::getPlayers($request, $obPagination),
            'pagination' => self::getPagination($request, $obPagination),
        ]);
        return parent::getBase('Highscores', $content, 'highscores');
    }
}
<?php
/**
 * Home Class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Guilds as EntityGuild;
use App\Model\Entity\Houses as EntityHouses;
use App\Model\Entity\Market as EntityMarket;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Functions\Payments as PaymentsFunctions;
use App\Model\Functions\Server as FunctionsServer;

class Home extends Base{

    public static function getTotalVocations()
    {
        $voc_sorcerer = EntityPlayer::getPlayer(['vocation' => [1, 5]], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        $voc_druid = (int)EntityPlayer::getPlayer(['vocation' => [2, 6]], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        $voc_knight = (int)EntityPlayer::getPlayer(['vocation' => [4, 8]], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;
        $voc_paladin = (int)EntityPlayer::getPlayer(['vocation' => [3, 7]], null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd;

        $vocations = [
            'sorcerer' => $voc_sorcerer,
            'druid' => $voc_druid,
            'knight' => $voc_knight,
            'paladin' => $voc_paladin,
        ];
        return $vocations;
    }

    public static function statsDonates()
    {
        return [
            'jan' => PaymentsFunctions::getPaymentBetweenDate(1),
            'feb' => PaymentsFunctions::getPaymentBetweenDate(2),
            'mar' => PaymentsFunctions::getPaymentBetweenDate(3),
            'apr' => PaymentsFunctions::getPaymentBetweenDate(4),
            'may' => PaymentsFunctions::getPaymentBetweenDate(5),
            'jun' => PaymentsFunctions::getPaymentBetweenDate(6),
            'jul' => PaymentsFunctions::getPaymentBetweenDate(7),
            'aug' => PaymentsFunctions::getPaymentBetweenDate(8),
            'sep' => PaymentsFunctions::getPaymentBetweenDate(9),
            'oct' => PaymentsFunctions::getPaymentBetweenDate(10),
            'nov' => PaymentsFunctions::getPaymentBetweenDate(11),
            'dec' => PaymentsFunctions::getPaymentBetweenDate(12)
        ];
    }

    public static function getHome($request)
    {
        $content = View::render('admin/modules/home/index', [
            'boosted_boss' => FunctionsServer::getBoostedBoss(),
            'boosted_creature' => FunctionsServer::getBoostedCreature(),
            'total_guilds' => (int)EntityGuild::getGuilds(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
            'total_houses' => (int)EntityHouses::getHouses(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd,
            'total_marketoffers' => (int)EntityMarket::getMarketOffers(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
            'total_accounts' => (int)EntityPlayer::getAccount(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
            'total_players' => (int)EntityPlayer::getPlayer(null, null, null, ['COUNT(*) as qtd'])->fetchObject()->qtd,
            'total_vocations' => self::getTotalVocations(),
            'donate_stats' => self::statsDonates()
        ]);

        return parent::getPanel('Home', $content, 'home');
    }

}
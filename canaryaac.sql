
ALTER TABLE `accounts` ADD `page_access` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `accounts` MODIFY COLUMN `creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `accounts` DROP INDEX `accounts_unique`;

INSERT INTO `accounts` (`id`, `name`, `password`, `email`, `page_access`, `premdays`, `lastday`, `type`, `coins`, `creation`, `recruiter`) VALUES
(null, '', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'admin@canaryaac.com', 3, 0, 0, 5, 2000, '2022-08-03 08:44:10', 0);

DELETE FROM `accounts` WHERE `accounts`.`id` = 1;

UPDATE `accounts` SET `id` = '1' WHERE `accounts`.`id` = 2;

-- --------------------------------------------------------

ALTER TABLE `players` ADD `main` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `players` ADD `world` int(11) NOT NULL DEFAULT 0;

DELETE FROM `players` WHERE `players`.`id` = 1;
DELETE FROM `players` WHERE `players`.`id` = 2;
DELETE FROM `players` WHERE `players`.`id` = 3;
DELETE FROM `players` WHERE `players`.`id` = 4;
DELETE FROM `players` WHERE `players`.`id` = 5;

UPDATE `players` SET `account_id` = '2' WHERE `players`.`id` = 6;
UPDATE `players` SET `main` = '1' WHERE `players`.`id` = 6;
UPDATE `players` SET `world` = '1' WHERE `players`.`id` = 6;

ALTER TABLE `players` CHANGE `conditions` `conditions` BLOB NOT NULL DEFAULT '';

-- --------------------------------------------------------

ALTER TABLE `guilds` ADD `description` text NOT NULL;
ALTER TABLE `guilds` ADD `logo_name` varchar(100) NOT NULL;
ALTER TABLE `guilds` ADD `world_id` int(11) NOT NULL DEFAULT 0;

ALTER TABLE `guild_membership` ADD `date` int(11) NOT NULL;

-- --------------------------------------------------------

ALTER TABLE `guild_wars` ADD `price1` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `guild_wars` ADD `price2` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `guild_wars` ADD `frags` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `guild_wars` ADD `comment` text NOT NULL;

-- --------------------------------------------------------

ALTER TABLE `houses` ADD `house_id` INT NOT NULL AFTER `id`;
ALTER TABLE `houses` ADD `world_id` INT NOT NULL AFTER `house_id`;

-- --------------------------------------------------------

ALTER TABLE `server_config` ADD `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `account_authentication` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `secret` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `account_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `recovery` varchar(23) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `address` varchar(50) NOT NULL,
  `housenumber` int(10) NOT NULL,
  `additional` varchar(50) NOT NULL,
  `postalcode` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `boosted_boss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `looktype` int(11) NOT NULL DEFAULT 136,
  `lookfeet` int(11) NOT NULL DEFAULT 0,
  `looklegs` int(11) NOT NULL DEFAULT 0,
  `lookhead` int(11) NOT NULL DEFAULT 0,
  `lookbody` int(11) NOT NULL DEFAULT 0,
  `lookaddons` int(11) NOT NULL DEFAULT 0,
  `lookmount` int(11) NOT NULL DEFAULT 0,
  `date` varchar(250) NOT NULL DEFAULT '',
  `boostname` text DEFAULT NULL,
  `raceid` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `boosted_creature` (
    `boostname` TEXT,
    `date` varchar(250) NOT NULL DEFAULT '',
    `raceid` varchar(250) NOT NULL DEFAULT '',
    `looktype` int(11) NOT NULL DEFAULT "136",
    `lookfeet` int(11) NOT NULL DEFAULT 0,
    `looklegs` int(11) NOT NULL DEFAULT 0,
    `lookhead` int(11) NOT NULL DEFAULT 0,
    `lookbody` int(11) NOT NULL DEFAULT 0,
    `lookaddons` int(11) NOT NULL DEFAULT 0,
    `lookmount` int(11) DEFAULT "0",
    PRIMARY KEY (`date`)
) AS SELECT 0 AS date, "default" AS boostname, 0 AS raceid;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_countdowns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_start` int(11) NOT NULL,
  `date_end` int(11) NOT NULL,
  `themebox` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(250) NOT NULL,
  `description` varchar(500) NOT NULL,
  `date_start` int(11) NOT NULL,
  `date_end` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_polls_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `question` varchar(250) NOT NULL,
  `description` varchar(500) NOT NULL,
  `votes` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_polls_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `grade` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `storage` int(11) NOT NULL,
  `secret` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_boss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_compendium` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `headline` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `publishdate` int(11) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_creatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `canary_groups` (`id`, `group_id`, `name`) VALUES
(1, 1, 'player'),
(2, 2, 'tutor'),
(3, 3, 'senior tutor'),
(4, 4, 'gamemaster'),
(5, 5, 'community manager'),
(6, 6, 'god');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `body` text CHARACTER SET utf8 NOT NULL,
  `type` tinyint(4) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` tinyint(4) NOT NULL,
  `player_id` int(11) NOT NULL,
  `last_modified_by` int(11) NOT NULL DEFAULT 0,
  `last_modified_date` datetime NOT NULL,
  `comments` varchar(50) CHARACTER SET utf8 NOT NULL,
  `article_text` varchar(300) CHARACTER SET utf8 NOT NULL,
  `article_image` varchar(100) CHARACTER SET utf8 NOT NULL,
  `hidden` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `method` varchar(100) NOT NULL,
  `reference` varchar(200) NOT NULL,
  `total_coins` int(11) NOT NULL,
  `final_price` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coins` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `canary_products` (`id`, `coins`) VALUES
(1, 250),
(2, 750),
(3, 1500),
(4, 3000),
(5, 4500),
(6, 15000);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_samples` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vocation` int(11) NOT NULL,
  `experience` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `health` int(11) NOT NULL,
  `healthmax` int(11) NOT NULL,
  `maglevel` int(11) NOT NULL,
  `mana` int(11) NOT NULL,
  `manamax` int(11) NOT NULL,
  `manaspent` int(11) NOT NULL,
  `soul` int(11) NOT NULL,
  `town_id` int(11) NOT NULL,
  `posx` int(11) NOT NULL,
  `posy` int(11) NOT NULL,
  `posz` int(11) NOT NULL,
  `cap` int(11) NOT NULL,
  `balance` int(11) NOT NULL,
  `lookbody` int(11) NOT NULL,
  `lookfeet` int(11) NOT NULL,
  `lookhead` int(11) NOT NULL,
  `looklegs` int(11) NOT NULL,
  `looktype` int(11) NOT NULL,
  `lookaddons` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `canary_samples` (`id`, `vocation`, `experience`, `level`, `health`, `healthmax`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `town_id`, `posx`, `posy`, `posz`, `cap`, `balance`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons`) VALUES
(1, 0, 4200, 8, 185, 185, 0, 90, 90, 0, 0, 2, 0, 0, 0, 470, 0, 113, 115, 95, 39, 129, 0),
(3, 2, 4200, 8, 185, 185, 0, 90, 90, 0, 0, 2, 0, 0, 0, 470, 0, 113, 115, 95, 39, 130, 0),
(4, 3, 4200, 8, 185, 185, 0, 90, 90, 0, 0, 2, 0, 0, 0, 470, 0, 113, 115, 95, 39, 129, 0),
(5, 4, 4200, 8, 185, 185, 0, 90, 90, 0, 0, 2, 0, 0, 0, 470, 0, 113, 115, 95, 39, 131, 0),
(6, 1, 4200, 8, 185, 185, 0, 90, 90, 0, 0, 2, 0, 0, 0, 470, 0, 113, 115, 95, 39, 130, 0);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_towns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `town_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(80) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `canary_towns` (`id`, `town_id`, `name`) VALUES
(1, 0, 'No Town'),
(2, 1, 'Dawnport Tutorial'),
(3, 2, 'Dawnport'),
(4, 3, 'Rookgaard'),
(5, 4, 'Island of Destiny'),
(6, 5, 'AbDendriel'),
(7, 6, 'Carlin'),
(8, 7, 'Kazordoon'),
(9, 8, 'Thais'),
(10, 9, 'Venore'),
(11, 10, 'Ankrahmun'),
(12, 11, 'Edron'),
(13, 12, 'Farmine'),
(14, 13, 'Darashia'),
(15, 14, 'Liberty Bay'),
(16, 15, 'Port Hope'),
(17, 16, 'Svargrond'),
(18, 17, 'Yalahar'),
(19, 18, 'Gray Beach'),
(20, 19, 'Krailos'),
(21, 20, 'Rathleton'),
(22, 21, 'Roshamuul'),
(23, 22, 'Issavi'),
(24, 23, 'Cobra Bastion'),
(25, 24, 'Bounac'),
(26, 25, 'Feyrist'),
(27, 26, 'Gnomprona'),
(28, 27, 'Marapur');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(150) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `path` varchar(500) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_website` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timezone` varchar(150) NOT NULL,
  `title` varchar(70) CHARACTER SET utf8 NOT NULL,
  `downloads` varchar(250) NOT NULL,
  `discord` varchar(250) NOT NULL,
  `player_voc` int(11) NOT NULL COMMENT '0 off and 1 on',
  `player_max` int(11) NOT NULL COMMENT 'players por conta',
  `player_guild` int(11) NOT NULL COMMENT 'level',
  `donates` int(11) NOT NULL COMMENT '0 off and 1 on',
  `coin_price` decimal(10,2) NOT NULL,
  `mercadopago` int(11) NOT NULL,
  `pagseguro` int(11) NOT NULL,
  `paypal` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `canary_website` (`id`, `timezone`, `title`, `downloads`, `player_voc`, `player_max`, `player_guild`, `donates`, `coin_price`, `mercadopago`, `pagseguro`, `paypal`) VALUES
(1, 'America/Sao_Paulo', 'CanaryAAC v1', 'http://www.google.com', 1, 10, 100, 1, '0.10', 1, 1, 1);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_worldquests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storage` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `canary_worlds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `creation` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `location` int(11) NOT NULL DEFAULT 0,
  `pvp_type` int(11) NOT NULL DEFAULT 0,
  `premium_type` int(11) NOT NULL DEFAULT 0,
  `transfer_type` int(11) NOT NULL DEFAULT 0,
  `battle_eye` int(11) NOT NULL DEFAULT 0,
  `world_type` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(18) NOT NULL,
  `port` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `canary_worlds` (`id`, `name`, `creation`, `location`, `pvp_type`, `premium_type`, `transfer_type`, `battle_eye`, `world_type`, `ip`, `port`) VALUES
(null, 'Canary', '2022-09-01 06:00:00', 7, 0, 0, 0, 0, 0, '127.0.0.1', 7172);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `guild_applications` (
  `player_id` int(11) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `guild_id` int(11) NOT NULL DEFAULT 0,
  `text` text CHARACTER SET utf8 NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `guild_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guild_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date` int(15) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `player_display` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL DEFAULT 0,
  `account` int(11) NOT NULL DEFAULT 0,
  `outfit` int(11) NOT NULL DEFAULT 0,
  `inventory` int(11) NOT NULL DEFAULT 0,
  `health_mana` int(11) NOT NULL DEFAULT 0,
  `skills` int(11) NOT NULL DEFAULT 0,
  `bonus` int(11) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `player_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `view` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

INSERT INTO `players`
(`id`, `name`, `group_id`, `account_id`, `level`, `vocation`, `health`, `healthmax`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `town_id`, `conditions`, `cap`, `sex`, `skill_club`, `skill_club_tries`, `skill_sword`, `skill_sword_tries`, `skill_axe`, `skill_axe_tries`, `skill_dist`, `skill_dist_tries`, `world`) VALUES
(1, 'Rook Sample', 1, 1, 2, 0, 155, 155, 100, 113, 115, 95, 39, 129, 2, 60, 60, 5936, 1, '', 410, 1, 12, 155, 12, 155, 12, 155, 12, 93, 1),
(2, 'Sorcerer Sample', 1, 1, 8, 1, 185, 185, 4200, 113, 115, 95, 39, 129, 0, 90, 90, 0, 8, '', 470, 1, 10, 0, 10, 0, 10, 0, 10, 0, 1),
(3, 'Druid Sample', 1, 1, 8, 2, 185, 185, 4200, 113, 115, 95, 39, 129, 0, 90, 90, 0, 8, '', 470, 1, 10, 0, 10, 0, 10, 0, 10, 0, 1),
(4, 'Paladin Sample', 1, 1, 8, 3, 185, 185, 4200, 113, 115, 95, 39, 129, 0, 90, 90, 0, 8, '', 470, 1, 10, 0, 10, 0, 10, 0, 10, 0, 1),
(5, 'Knight Sample', 1, 1, 8, 4, 185, 185, 4200, 113, 115, 95, 39, 129, 0, 90, 90, 0, 8, '', 470, 1, 10, 0, 10, 0, 10, 0, 10, 0, 1),
(6, 'GOD', 6, 1, 2, 0, 155, 155, 100, 113, 115, 95, 39, 75, 0, 60, 60, 0, 8, '', 410, 1, 10, 0, 10, 0, 10, 0, 10, 0, 1);

-- --------------------------------------------------------

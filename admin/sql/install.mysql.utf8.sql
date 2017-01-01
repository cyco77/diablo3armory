
CREATE TABLE IF NOT EXISTS `#__diablo3armory_battletag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `server` varchar(2) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `paragonlevel` int(11) NOT NULL DEFAULT '0',
  `paragonlevelhardcore` int(11) NOT NULL DEFAULT '0',
  `paragonlevelseason` int(11) NOT NULL DEFAULT '0',
  `paragonlevelseasonhardcore` int(11) NOT NULL,
  `cache` MEDIUMBLOB,
  `cachetime` varchar(255) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__diablo3armory_hero` (
  `id` int(11) NOT NULL,
  `battletag` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL,
  `cache` MEDIUMBLOB,
  `cachetime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
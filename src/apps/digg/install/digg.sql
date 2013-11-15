DROP TABLE IF EXISTS `yuncms_digg`;
CREATE TABLE IF NOT EXISTS `yuncms_digg` (
  `id` varchar(15) NOT NULL DEFAULT '0',
  `contentid` mediumint(8) NOT NULL,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(80) NOT NULL,
  `url` varchar(100) NOT NULL,
  `total` mediumint(8) unsigned NOT NULL,
  `supports` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `againsts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `supports` (`supports`)
) ENGINE=MyISAM;
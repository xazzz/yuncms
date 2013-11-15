DROP TABLE IF EXISTS `yuncms_digg_log`;
CREATE TABLE IF NOT EXISTS `yuncms_digg_log` (
  `contentid` char(15) NOT NULL DEFAULT '0',
  `flag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` char(20) NOT NULL,
  `ip` char(15) NOT NULL,
  `datetime` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `contentid` (`contentid`,`flag`,`datetime`),
  KEY `userid` (`userid`,`contentid`),
  KEY `ip` (`ip`,`contentid`)
) ENGINE=MyISAM;
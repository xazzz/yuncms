CREATE TABLE IF NOT EXISTS `yuncms_form_table` (
  `dataid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL,
  `username` varchar(20) DEFAULT '',
  `datetime` int(10) unsigned NOT NULL,
  `ip` char(15) NOT NULL,
  PRIMARY KEY (`dataid`)
) ENGINE=MyISAM;
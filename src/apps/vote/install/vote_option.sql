DROP TABLE IF EXISTS `yuncms_vote_option`;
CREATE TABLE `yuncms_vote_option` (
  `optionid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `subjectid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `option` varchar(255) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `listorder` tinyint(2) unsigned DEFAULT '0',
  PRIMARY KEY (`optionid`),
  KEY `subjectid` (`subjectid`)
) ENGINE=MyISAM;

INSERT INTO `yuncms_vote_option` VALUES(1, 1, '搜索引擎', '', 0);
INSERT INTO `yuncms_vote_option` VALUES(2, 1, '朋友介绍', '', 0);
INSERT INTO `yuncms_vote_option` VALUES(3, 1, '网站链接', '', 0);
INSERT INTO `yuncms_vote_option` VALUES(4, 1, '其他方式', '', 0);
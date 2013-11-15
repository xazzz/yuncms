<?php
/**
 * Admin表模型
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: admin_model.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 * ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `yun_admin` (
  `userid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `roleid` smallint(5) DEFAULT '0',
  `encrypt` varchar(6) NOT NULL,
  `mobile` varchar(11) DEFAULT '',
  `email` varchar(40) DEFAULT '',
  `realname` varchar(50) DEFAULT '',
  `lastloginip` varchar(15) DEFAULT '',
  `lastlogintime` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`userid`),
  KEY `username` (`username`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class admin_model extends Model {
	public $table_name = '';

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'admin';
		parent::__construct ();
	}
}
<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
/**
 * 公告表
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-5-31
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: announce_model.php 632 2013-07-29 06:52:11Z 85825770@qq.com $
 */
class announce_model extends Model {

	/**
	 * 表名
	 *
	 * @var string
	 */
	public $table_name;

	/**
	 * 架构函数
	 */
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'announce';
		parent::__construct ();
	}
}
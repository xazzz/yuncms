<?php
/**
 * Times 表
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: times_model.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class times_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'times';
		parent::__construct ();
	}
}
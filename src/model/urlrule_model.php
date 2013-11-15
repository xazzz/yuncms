<?php
/**
 * URL规则表
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-28
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: urlrule_model.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class urlrule_model extends Model {
	public $table_name = '';

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'urlrule';
		parent::__construct ();
	}
}
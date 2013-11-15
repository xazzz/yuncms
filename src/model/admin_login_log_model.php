<?php
/**
 * 后台登陆日志表
 * @author Tongle Xu <xutongle@gmail.com> 2013-3-28
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: admin_login_log_model.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class admin_login_log_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'admin_login_log';
		parent::__construct ();
	}
}
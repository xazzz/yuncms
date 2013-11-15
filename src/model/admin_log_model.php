<?php
/**
 * 后台操作日志表
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: admin_log_model.php 635 2013-07-29 07:36:44Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class admin_log_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'admin_log';
		parent::__construct ();
	}
}
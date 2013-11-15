<?php
/**
 * 快捷菜单模型
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: admin_panel_model.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class admin_panel_model extends Model {

	public $table_name = '';

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'admin_panel';
		parent::__construct ();
	}
}
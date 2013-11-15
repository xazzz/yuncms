<?php
/**
 * 栏目权限表
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-28
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: category_priv_model.php 685 2013-07-30 03:53:05Z 85825770@qq.com $
 */
class category_priv_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'category_priv';
		parent::__construct();
	}
}
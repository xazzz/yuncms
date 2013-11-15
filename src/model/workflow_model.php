<?php
/**
 * 工作流程表
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-28
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: workflow_model.php 684 2013-07-30 03:52:17Z 85825770@qq.com $
 */
class workflow_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'workflow';
		parent::__construct ();
	}

}
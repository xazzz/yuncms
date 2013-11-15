<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-8
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: member_verify_model.php 958 2012-07-02 01:48:43Z
 *          85825770@qq.com $
 */
class member_verify_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'member_verify';
		parent::__construct ();
	}
}
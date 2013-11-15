<?php
/**
 * 订单表
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: order_model.php 719 2013-08-02 10:06:53Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class order_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'order';
		parent::__construct ();
	}
}
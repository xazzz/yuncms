<?php
/**
 * 支付方式表
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: payment_cfg_model.php 719 2013-08-02 10:06:53Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class payment_cfg_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'payment_cfg';
		parent::__construct ();
	}

	/**
	 * 获取支付插件信息
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function get_id($id) {
		$payment = $this->where ( array ('id' => $id ) )->find ();
		return $payment;
	}

	/**
	 * 获取所有支付插件
	 */
	public function get_all() {
		$payment = $this->select ();
		return $payment;
	}
}
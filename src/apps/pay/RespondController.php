<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: RespondController.php 672 2013-07-30 03:31:18Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class RespondController {
	private $pay_db, $account_db, $member_db;
	public function __construct() {
		Loader::helper ( 'pay:global' );
	}

	/**
	 * return_url get形式响应
	 */
	public function respond_get() {
		if (isset($_GET ['code'])) {
			$payment = $this->get_by_code ( $_GET ['code'] );
			if (! $payment) showmessage ( L ( 'payment_failed' ) );
			$cfg = unserialize_config ( $payment ['config'] );
			$pay_name = ucwords ( $payment ['pay_code'] );
			Loader::lib ( 'pay:pay_factory', false );
			$payment_handler = new pay_factory ( $pay_name, $cfg );
			$return_data = $payment_handler->receive ();
			if ($return_data) {
				if ($return_data ['order_status'] == 0) {
					$this->update_member_amount_by_sn ( $return_data ['order_id'] );
				}
				$this->update_recode_status_by_sn ( $return_data ['order_id'], $return_data ['order_status'] );
				showmessage ( L ( 'pay_success' ), SITE_URL . 'index.php?app=pay&controller=deposit' );
			} else {
				showmessage ( L ( 'pay_failed' ), SITE_URL . 'index.php?app=pay&controller=deposit' );
			}
		} else {
			showmessage ( L ( 'pay_success' ) );
		}
	}

	/**
	 * 服务器端 POST形式响应
	 */
	public function respond_post() {
		$_POST ['code'] = isset($_POST ['code']) ? $_POST ['code'] : $_GET ['code'];
		if ($_POST ['code']) {
			$payment = $this->get_by_code ( $_POST ['code'] );
			if (! $payment) error_log ( date ( 'm-d H:i:s', TIME ) . '| POST: payment is null |' . "\r\n", 3, CACHE_PATH . 'pay_error_log.php' );
			;
			$cfg = unserialize_config ( $payment ['config'] );
			$pay_name = ucwords ( $payment ['pay_code'] );
			Loader::lib ( 'pay:pay_factory', false );
			$payment_handler = new pay_factory ( $pay_name, $cfg );
			$return_data = $payment_handler->notify ();
			if ($return_data) {
				if ($return_data ['order_status'] == 0) {
					$this->update_member_amount_by_sn ( $return_data ['order_id'] );
				}
				$this->update_recode_status_by_sn ( $return_data ['order_id'], $return_data ['order_status'] );
				$result = TRUE;
			} else {
				$result = FALSE;
			}
			$payment_handler->response ( $result );
		}
	}

	/**
	 * 更新订单状态
	 *
	 * @param unknown_type $trade_sn 订单ID
	 * @param unknown_type $status 订单状态
	 */
	private function update_recode_status_by_sn($trade_sn, $status) {
		$trade_sn = trim ( $trade_sn );
		$status = trim ( intval ( $status ) );
		$data = array ();
		$this->account_db = Loader::model ( 'pay_account_model' );
		$status = return_status ( $status );
		$data = array ('status' => $status );
		return $this->account_db->where ( array ('trade_sn' => $trade_sn ) )->update ( $data );
	}

	/**
	 * 更新用户账户余额
	 *
	 * @param unknown_type $trade_sn
	 */
	private function update_member_amount_by_sn($trade_sn) {
		$data = $userinfo = array ();
		$this->member_db = Loader::model ( 'member_model' );
		$orderinfo = $this->get_userinfo_by_sn ( $trade_sn );
		$userinfo = $this->member_db->getby_userid ($orderinfo ['userid'] );
		if ($orderinfo) {
			$money = floatval ( $orderinfo ['money'] );
			$amount = $userinfo ['amount'] + $money;
			$data = array ('amount' => $amount );
			return $this->member_db->where(array ('userid' => $orderinfo ['userid'] ))->update ( $data );
		} else {
			error_log ( date ( 'm-d H:i:s', TIME ) . '|  POST: rechange failed! trade_sn:' . $$trade_sn . ' |' . "\r\n", 3, DATA_PATH . 'pay_error_log.php' );
			return false;
		}
	}

	/**
	 * 通过订单ID抓取用户信息
	 *
	 * @param unknown_type $trade_sn
	 */
	private function get_userinfo_by_sn($trade_sn) {
		$trade_sn = trim ( $trade_sn );
		$this->account_db = Loader::model ( 'pay_account_model' );
		$result = $this->account_db->where ( array ('trade_sn' => $trade_sn ) )->find();
		$status_arr = array ('succ','failed','error','timeout','cancel' );
		return ($result && ! in_array ( $result ['status'], $status_arr )) ? $result : false;
	}

	/**
	 * 通过支付代码获取支付信息
	 *
	 * @param unknown_type $code
	 */
	private function get_by_code($code) {
		$result = array ();
		$code = trim ( $code );
		$this->pay_db = Loader::model ( 'pay_payment_model' );
		$result = $this->pay_db->where ( array ('pay_code' => $code ) )->find();
		return $result;
	}
}
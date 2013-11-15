<?php
/**
 * 支付流水表
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: payment_model.php 716 2013-08-02 10:05:17Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
define ( "PAY_FAILED", - 1 );
define ( "PAY_TIMEOUT", 0 );
define ( "PAY_SUCCESS", 1 );
define ( "PAY_CANCEL", 2 );
define ( "PAY_ERROR", 3 );
define ( "PAY_PROGRESS", 4 );
define ( "PAY_INVALID", 5 );
define ( "PAY_MANUAL", 0 );
class payment_model extends Model {
	public $payment;
	public $payemnt_cfg_db;

	/**
	 * 构造方法
	 */
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'payment';
		parent::__construct ();
		$this->payemnt_cfg_db = Loader::model ( 'payment_cfg_model' );
	}
	public function get_columns() {
		$ret = array ();
		$ret ['pay_code'] ['default'] = "";
		$ret ['status'] ['default'] = "";
		return $ret;
	}
	public function get_filter($p) {
		$return ['payment'] = $this->get_methods ();
		return $return;
	}
	public function get_methods($type = "") {
		$where = array ('enabled' => 1 );
		if ($type == "online") {
			$where ['pay_type'] = array ('not in','OFFLINE,DEPOSIT' );
		}
		return Loader::model ( 'payment_cfg_model' )->where ( $where )->order ( 'pay_order desc' )->select ();
	}
	public function get_all_methods($type = "") {
		return Loader::model ( 'payment_cfg_model' )->order ( 'pay_order desc' )->select ();
	}

	/**
	 * 生成支付号
	 *
	 * @return string 生成的支付号
	 */
	public function gen_id() {
		$i = rand ( 0, 9999 );
		do {
			if (9999 == $i) {
				$i = 0;
			}
			++ $i;
			$payment_id = time () . str_pad ( $i, 4, "0", STR_PAD_LEFT );
			$row = $this->field ( 'payment_id' )->where ( array ('payment_id' => $payment_id ) )->find ();
		} while ( $row );
		return $payment_id;
	}

	/**
	 * 创建支付流水
	 */
	public function to_create() {
		$info = array ();
		$this->payment ['payment_id'] = $this->gen_id ();
		$this->payment ['addtime'] = time ();
		$this->payment ['paytime'] = time ();
		$this->payment ['ip'] = IP;
		$this->insert ( $this->payment );
		return $this->payment ['payment_id'];
	}

	/**
	 * 创建支付流水号并去支付
	 */
	public function do_pay($method = "", $order_id = '') {
		// 加载订单模型

		// 创建支付插件实例
		$payobj = Loader::plugin ( 'payment.' . $this->payment ['type'] . '.' . $this->payment ['type'] );
		$pay_vars = get_object_vars ( $payobj );

		// 创建支付实例
		if ($this->to_create ()) {
			$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n                \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n                <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\" lang=\"en-US\" dir=\"ltr\">\n                <head>\n</header><body><div>Redirecting...</div>";
			$payObj->_payment = $this->payment ['payment'];
			$toSubmit = $payobj->toSubmit ( $this->get_payment_info ( $method ) );
			if ("utf8" != strtolower ( $payobj->charset )) {
				$toSubmit = array_iconv ( $toSubmit, CHARSET, $payobj->charset );
			}

			$html .= "<form id=\"payment\" action=\"" . $payobj->submitUrl . "\" method=\"" . $payobj->method . "\">";
			$buffer = '';
			foreach ( $toSubmit as $k => $v ) {
				if ($k != "ikey") {
					$html .= "<input name=\"" . urldecode ( $k ) . "\" type=\"hidden\" value=\"" . htmlspecialchars ( $v ) . "\" />";
					if ($v) {
						$buffer .= urldecode ( $k ) . "=" . $v . "&";
					}
				}
			}
			if (strtoupper ( $this->payment ['type'] ) == "TENPAYTRAD") {
				$buffer = substr ( $buffer, 0, strlen ( $buffer ) - 1 );
				$md5_sign = strtoupper ( md5 ( $buffer . "&key=" . $toSubmit ['ikey'] ) );
				$url = $payObj->submitUrl . "?" . $buffer . "&sign=" . $md5_sign;
				echo "<script language='javascript'>";
				echo "window.location.href='" . $url . "';";
				echo "</script>";
			}
			$html .= "\n</form>\n<script language=\"javascript\">\ndocument.getElementById('payment').submit();\n</script>\n</html>";
		} else {
			$html = "<html>\n<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html;charset=utf-8\\\"/>\n<script language=\"javascript\">\nalert('创建支付流水号错误！');\n//location.href=document.referrer;\n</script>\n</html>";
		}
		echo $html;
	}

	/**
	 * 设置支付状态
	 *
	 * @param int $payment_id
	 * @param unknown $status
	 * @param unknown $payInfo
	 * @return boolean
	 */
	public function set_pay_status($payment_id, $status, &$pay_info = array()) {
		if (! $payment_id) {
			trigger_error ( L ( 'ticket_number_transfer_error' ), E_USER_ERROR );
			return false;
			exit ();
		}
		$aPayInfo = $this->getby_payment_id ( $payment_id );
		if (! $aPayInfo) {
			trigger_error ( L ( "payment_record_was_not_found,_may_be_error_parameters" ), E_USER_ERROR );
			return false;
			exit ();
		}
		if ($aPayInfo ['status'] == "succ") {
			return true;
		}
		if ($aPayInfo ['status'] == "progress" && $status == PAY_PROGRESS) {
			return true;
		}
		if ($aPayInfo ['pay_type'] == "recharge" && $aPayInfo ['bank'] == "deposit") {
			//$pay_info .= "memo";
			$status = PAY_FAILED;
		}
		if (isset($pay_info ['cur_money']) && $aPayInfo ['cur_money'] != $pay_info ['money']) {
			$status = PAY_ERROR;
			//$pay_info .= "memo";
		}
		switch ($status) {
			// case PAY_IGNORE :
			// return false;
			case PAY_FAILED :
				$pay_info ['status'] = "failed";
				break;
			case PAY_TIMEOUT :
				$pay_info ['status'] = "timeout";
				break;
			case PAY_PROGRESS :
				$aPayInfo ['pay_assure'] = true;
				$aPayInfo ['pay_progress'] = "PAY_PROGRESS";
				$pay_info ['status'] = "progress";
				break;
			case PAY_SUCCESS :
				$pay_info ['status'] = "succ";
				break;
			case PAY_CANCEL :
				$pay_info ['status'] = "cancel";
				break;
			case PAY_ERROR :
				$pay_info ['status'] = "error";
				break;
			case PAY_REFUND_SUCCESS : // 退款
				$Rs = $this->field ( 'order_id' )->where ( array ('payment_id' => $payment_id ) )->find ();
				if ($Rs) {
					$_POST ['order_id'] = $Rs ['order_id'];
					return true;
				} else {
					return false;
				}
		}
		$pay_info ['paytime'] = time ();
		$aRs = $this->where ( array ('payment_id' => $payment_id,'status' => array ('neq', 'succ' ) ) )->find ();
		$status = $this->where ( array ('payment_id' => $payment_id ) )->update ( $pay_info );
		if ($status) {
			if (($status == PAY_PROGRESS || $status == PAY_SUCCESS)) {
				return false;
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 支付成功
	 *
	 * @param unknown $info
	 * @param unknown $message
	 * @return boolean
	 */
	public function on_success($info, &$message) {
		if ($info ['pay_type'] == "recharge") {
			$oCur = & $this->system->loadModel ( "system/cur" );
			$aCur = $info ['currency'] ( $info ['currency'] );
			$info ['money'] = $info ['money'] - $info ['paycost'];
			if ($aCur ['def_cur'] == "false") {
				$info /= "money";
			}
			$info ['money'] = $info ['money'] ( $info ['money'], false );
			$message .= "预存款充值：支付单号{" . $info ['payment_id'] . "}";
			$advance = $this->system->loadModel ( "member/advance" );
			if (! $info ['pay_assure']) {
				return $info ['paymethod'] ( $info ['member_id'], $info ['money'], $message, $message, $info ['payment_id'], "", $info ['paymethod'], "在线充值" );
			} else {
				return true;
			}
		} else {
			$order = & $this->system->loadModel ( "trading/order" );
			return $order->payed ( $info, $message );
		}
	}

	/**
	 * 支付回调继续
	 *
	 * @param unknown $paymentId
	 * @param unknown $status
	 * @param unknown $info
	 */
	public function progress($payment_id, $status, $info) {
		$send_pay = array ();
		$send_pay ['payment'] = $payment_id;
		$send_pay ['amount'] = $info ['money'];
		$send_pay ['order_id'] = $info ['trade_no'];
		$send_pay ['pay_status'] = $status;
		$url = U ( 'payment/paycenter/result' );
		$pay_status = $this->set_pay_status ( $payment_id, $status, $info );
		$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n       \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n       <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\" lang=\"en-US\" dir=\"ltr\">\n       <head></header><body>Redirecting...";
		$html .= "<form id=\"payment\" action=\"" . $url . "\" method=\"post\"><input type=\"hidden\" name=\"payment_id\" value=\"" . $payment_id . "\">";
		$html .= "      </form>\n      <script language=\"javascript\">\n      document.getElementById('payment').submit();\n      </script>\n    </html>";
		echo $html;
	}

	/**
	 * 获取收款账户
	 */
	public function get_account() {
		return $this->field ( 'DISTINCT bank, account' )->where ( array ('status' => 'succ' ) )->select ();
	}

	/**
	 * 获取支付信息
	 *
	 * @param string $method
	 */
	public function get_payment_info($method = "") {
		$m = Loader::model ( "member_model" );
		if (isset ( $this->payment ['order_id'] ) && empty ( $this->payment ['order_id'] )) {
			$o = Loader::model ( 'order_model' );
			$order = $o->getby_order_id ( $this->payment ['order_id'] );
			$member = $m->getby_userid ( $order ['userid'] );
			$payment ['M_Remark'] = $order ['memo'];
			$payment ['M_Language'] = "zh_CN";
			$payment ['R_Name'] = $order ['ship_name'];
			$payment ['R_Address'] = $order ['ship_addr'];
			$payment ['R_Postcode'] = $order ['ship_zip'];
			$payment ['R_Telephone'] = $order ['ship_tel'];
			$payment ['R_Mobile'] = $order ['ship_mobile'];
			$payment ['R_Email'] = $order ['ship_email'];
			$payment ['P_Name'] = $member ['name'];
			$payment ['P_Address'] = $member ['addr'];
			$payment ['P_PostCode'] = $member ['zip'];
			$payment ['P_Telephone'] = $member ['tel'];
			$payment ['P_Mobile'] = $member ['mobile'];
			$payment ['P_Email'] = $member ['email'];
			$payment ['payExtend'] = unserialize ( $order ['extend'] );
		}
		$payment ['M_OrderId'] = $this->payment ['payment_id'];
		$payment ['M_Amount'] = $this->payment ['money'];
		$payment ['M_Def_Amount'] = $this->payment ['money'];
		$payment ['K_key'] = C ( 'config', "auth_key" );
		$payment ['M_Method'] = $method;
		if ($this->payment ['pay_type'] == "recharge") {
			$member = $m->getby_userid ( $this->payment ['userid'] );
			$payment ['R_Name'] = $member ['nickname'] ? $member ['nickname'] : $member ['username'];
			$payment ['R_Telephone'] = $member ['mobile'] ? $member ['mobile'] : "13888888888";
		}
		return $payment;
	}

	/**
	 * 获取支付插件信息
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function get_payment_id($id) {
		$payment = Loader::model ( 'payment_cfg_model' )->where ( array ('id' => $id ) )->find ();
		return $payment;
	}
}
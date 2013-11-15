<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
/**
 * 预存款支付插件
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: deposit.php 717 2013-08-02 10:05:41Z 85825770@qq.com $
 */
class deposit extends Payment {

	public $charset = 'utf-8';
	public $submitUrl = '';

	/**
	 * (non-PHPdoc)
	 * @see Payment::toSubmit()
	 */
	public function toSubmit($payment) {
		$text = "orderid=" . $payment ['M_OrderId'] . "&amount=" . $payment ['M_Amount'] . "&merchant_url=" . $this->callbackUrl . "&merchant_key=" . $payment ['K_key'];
		$mac = strtoupper ( md5 ( $text ) ); // 对参数串进行私钥加密取得值
		$return ['orderid'] = $payment ['M_OrderId']; // $order->M_OrderId
		$return ['amount'] = $payment ['M_Amount']; // $order->M_Amount
		$return ['merchant_url'] = $this->callbackUrl;
		$return ['mac'] = $mac;
		$this->submitUrl = $this->callbackUrl;
		return $return;
	}

	/**
	 * (non-PHPdoc)
	 * @see Payment::callback()
	 */
	public function callback($in, &$payment_id, &$money, &$message) {
		$orderid = trim($in['orderid']);            //交易号
        $amount = trim($in['amount']);                //交易金额
        $merchant_url = trim($in['merchant_url']);
        $mymac = trim($in['mac']);

        $payment_id = $orderid;
        $money = $amount;
		$key = C ( 'config', "auth_key" );
		$text = "orderid=" . $orderid . "&amount=" . $amount . "&merchant_url=" . $merchant_url . "&merchant_key=" . $key;
		$mac = strtoupper ( md5 ( $text ) );
		if (strtoupper ( $mac ) == strtoupper ( $mymac )) {
			return PAY_SUCCESS;
		} else {
			$message = '支付验证失败';
			return PAY_ERROR;
		}
	}
}
?>

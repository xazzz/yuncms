<?php
/**
 * alipay.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: alipay.php 717 2013-08-02 10:05:41Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class alipay extends Payment {
	public $charset = 'utf8';
	public $submitUrl = 'https://www.alipay.com/cooperate/gateway.do?_input_charset=utf-8'; //
	public function __construct() {
		parent::__construct ();
		$regIp = isset ( $_SERVER ['SERVER_ADDR'] ) ? $_SERVER ['SERVER_ADDR'] : $_SERVER ['HTTP_HOST'];
		$this->intro = '<b style="font-family:verdana;font-size:13px;padding:3px;color:#000"><br>ShopEx联合支付宝推出优惠套餐：无预付/年费，单笔费率1.5%，无流量限制。</b><div style="padding:10px 0 0 388px"><a  href="javascript:void(0)" onclick="document.ALIPAYFORM.submit();"><img src="../plugins/payment/images/alipaysq.png"></a></div><div>如果您已经和支付宝签约了其他套餐，同样可以点击上面申请按钮重新签约，即可享受新的套餐。<br>如果不需要更换套餐，请将签约合作者身份ID等信息在下面填写即可，<a href="http://www.shopex.cn/help/ShopEx48/help_shopex48-1235733634-11323.html" target="_blank">点击这里查看使用帮助</a><form name="ALIPAYFORM" method="GET" action="http://top.shopex.cn/recordpayagent.php" target="_blank"><input type="hidden" name="postmethod" value="GET"><input type="hidden" name="payagentname" value="支付宝"><input type="hidden" name="payagentkey" value="ALIPAY"><input type="hidden" name="market_type" value="from_agent_contract"><input type="hidden" name="customer_external_id" value="C433530444855584111X"><input type="hidden" name="pro_codes" value="6AECD60F4D75A7FB"><input type="hidden" name="regIp" value="' . $regIp . '"><input type="hidden" name="domain" value="' . SITE_URL . '"></form></div>';
	}
	public function toSubmit($payment) {
		$merId = $this->getConf ( $payment ['M_OrderId'], 'member_id' ); // 帐号
		$pKey = $this->getConf ( $payment ['M_OrderId'], 'PrivateKey' );
		$key = $pKey == '' ? 'afsvq2mqwc7j0i69uzvukqexrzd0jq6h' : $pKey; // 私钥值
		$ret_url = $this->callbackUrl;
		$server_url = $this->serverCallbackUrl;

		$return ['logistics_type'] = "POST";
		$return ['logistics_payment'] = "BUYER_PAY";
		$return ['logistics_fee'] = '0.00';

		$return ['_input_charset'] = "utf-8";
		ksort ( $return );
		reset ( $return );
		$mac = "";
		foreach ( $return as $k => $v ) {
			$mac .= "&{$k}={$v}";
		}
		$mac = substr ( $mac, 1 );
		$return ['sign'] = md5 ( $mac . $key ); // 验证信息
		$return ['sign_type'] = 'MD5'; // 验证信息
		                               // $return['ikey']=$key;
		unset ( $return ['_input_charset'] );
		return $return;
	}
	public function callback($in, &$paymentId, &$money, &$message, &$tradeno) {
		$merId = $this->getConf ( $in ['out_trade_no'], 'member_id' ); // 帐号
		$pKey = $this->getConf ( $in ['out_trade_no'], 'PrivateKey' );
		$key = $pKey == '' ? 'afsvq2mqwc7j0i69uzvukqexrzd0jq6h' : $pKey; // 私钥值
		ksort ( $in );
		// 检测参数合法性
		$temp = array ();
		foreach ( $in as $k => $v ) {
			if ($k != 'sign' && $k != 'sign_type') {
				$temp [] = $k . '=' . $v;
			}
		}
		$testStr = implode ( '&', $temp ) . $key;
		if ($in ['sign'] == md5 ( $testStr )) {
			$paymentId = $in ['out_trade_no'];
			// 支付单号
			$money = $in ['total_fee'];
			$message = $in ['body'];
			$tradeno = $in ['trade_no'];
			switch ($in ['trade_status']) {
				case 'TRADE_FINISHED' :
					if ($in ['is_success'] == 'T') {
						return PAY_SUCCESS;
					} else {
						return PAY_FAILED;
					}
					break;
				case 'TRADE_SUCCESS' :
					if ($in ['is_success'] == 'T') {
						return PAY_SUCCESS;
					} else {
						return PAY_FAILED;
					}
					break;
				case 'WAIT_SELLER_SEND_GOODS' :
					if ($in ['is_success'] == 'T') {
						return PAY_PROGRESS;
					} else {
						return PAY_FAILED;
					}
					break;
				case 'TRADE_SUCCES' : // 高级用户
					if ($in ['is_success'] == 'T') {
						return PAY_SUCCESS;
					} else {
						return PAY_FAILED;
					}
					break;
			}
		} else {
			$message = 'Invalid Sign';
			return PAY_ERROR;
		}
	}
	function serverCallback($in, &$paymentId, &$money, &$message) {
		exit ( 'reserved' );
	}
}
<?php
/**
 * Payment.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: Payment.php 720 2013-08-02 10:07:30Z 85825770@qq.com $
 */
class Payment {
	public $method = "post";
	public $charset = "utf8";
	public $submitUrl = null;
	public $callbackUrl = null;
	public $_config = array ();
	public $_payment = 0;

	/**
	 * 构造方法
	 */
	public function __construct() {
		$this->callbackUrl = SITE_URL . 'index.php?app=payment&controller=respond&code=' . get_class ( $this );
		$this->serverCallbackUrl = SITE_URL . 'index.php?app=payment&controller=respond&code=' . get_class ( $this ).'_server';
	}

	/**
	 * 提交支付请求
	 *
	 * @return boolean
	 */
	public function toSubmit() {
		return false;
	}

	/**
	 * 响应支付请求
	 *
	 * @return boolean
	 */
	public function callback() {
		return false;
	}

	/**
	 * 获取配置
	 *
	 * @param int $paymentid
	 * @param unknown $key
	 * @param string $value
	 * @return multitype:
	 */
	public function getConf($paymentid, $key, $value = null) {
		if (count ( $this->_config ) == 0) {
			$p = Loader::model ( 'payment_model' );
			if ($this->_payment) {
				$payment = $p->getby_id ( $paymentid );
				$this->_payment = $payment ['payment'];
			}
			$payment_cfg = $p->get_payment_id ( $this->_payment );
			$this->_config = unserialize ( $payment_cfg ['config'] );
		}
		return $this->_config [$key];
	}
}
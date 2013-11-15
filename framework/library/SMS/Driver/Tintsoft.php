<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-4-11
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: Tintsoft.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class SMS_Driver_Tintsoft extends SMS {
	public function __construct() {
		$this->client = new HttpClient ();
	}
	public function set($options) {
		$this->uuid = C ( 'version', 'uuid' );
		$this->sign = C ( 'version', 'sign' );
	}

	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 *
	 * @see SMS::send()
	 */
	public function send() {
	}

	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 *
	 * @see SMS::get_balance()
	 */
	public function get_balance() {
	}
}
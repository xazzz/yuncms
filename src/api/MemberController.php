<?php
/**
 * 会员AJAX接口
 * @author Tongle Xu <xutongle@gmail.com> 2013-4-8
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: MemberController.php 691 2013-07-30 04:12:44Z 85825770@qq.com $
 */
class MemberController {
	public $api;
	public function __construct() {
		$this->api = Loader::lib ( 'member:member_interface' );
	}

	/**
	 * AJAX检测用户名是否可用
	 */
	public function checkname() {
		$username = isset ( $_GET ['username'] ) && trim ( $_GET ['username'] ) ? trim ( $_GET ['username'] ) : exit ( '0' );
		if (CHARSET != 'utf-8') {
			$username = iconv ( 'utf-8', CHARSET, $username );
			$username = addslashes ( $username );
		}
		$rs = $this->api->check_username ( $username );
		if ($rs == 1) exit ( '1' );
		exit ( '0' );
	}

	/**
	 * 检测Email地址是否可用
	 */
	public function checkemail() {
		$email = isset ( $_GET ['email'] ) && trim ( $_GET ['email'] ) ? trim ( $_GET ['email'] ) : exit ( '0' );
		$userid = isset ( $_GET ['userid'] ) && intval ( $_GET ['userid'] ) ? intval ( $_GET ['userid'] ) : null;
		$status = $this->api->check_email ( $email, $userid );
		if ($status == 1) {
			exit ( '1' );
		} else {
			exit ( '0' );
		}
	}

	public function checknickname(){
		$nickname = isset ( $_GET ['nickname'] ) && trim ( $_GET ['nickname'] ) ? trim ( $_GET ['nickname'] ) : exit ( '0' );
		$userid = isset ( $_GET ['userid'] ) && intval ( $_GET ['userid'] ) ? intval ( $_GET ['userid'] ) : null;
		$status = $this->api->check_nickname ( $nickname, $userid );
		if ($status == 1) {
			exit ( '1' );
		} else {
			exit ( '0' );
		}
	}
}
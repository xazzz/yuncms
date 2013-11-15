<?php
/**
 * 会员前端控制器
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: Member.php 720 2013-08-02 10:07:30Z 85825770@qq.com $
 */
class Web_Member {
	public $db, $memberinfo;
	public $avatar = null;
	public $uc = null;

	public function __construct() {
		self::check_ip ();
		if (method_exists ( $this, '_initialize' )) $this->_initialize (); // 控制器初始化
		$http_user_agent = str_replace ( '7.0', '8.0', $_SERVER ['HTTP_USER_AGENT'] );
		$this->auth_key = md5 ( C ( 'config', 'auth_key' ) . $http_user_agent );
		$this->db = Loader::model ( 'member_model' );
		// ajax验证信息不需要登录
		if (substr ( ACTION, 0, 7 ) != 'public_') {
			$this->check_member ();
		}
		if (ucenter_exists ()) $this->uc = Loader::lib ( 'member:uc_client' );
	}

	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_member() {
		$yuncms_auth = cookie ( 'auth' );
		if (APP == 'member' && CONTROLLER == 'Passport') {
			return true;
		} else {
			// 判断是否存在auth cookie
			if (!empty($yuncms_auth)) {
				$yuncms_auth = String::authcode ( $yuncms_auth, 'DECODE', $this->auth_key );
				list ( $userid, $password ) = explode ( "\t", $yuncms_auth );
				// 验证用户，获取用户信息
				$this->memberinfo = $this->db->getby_userid ( $userid );
				// 获取用户模型信息
				$this->db->set_model ( $this->memberinfo ['modelid'] );
				$_member_modelinfo = $this->db->getby_userid ( $userid );
				$_member_modelinfo = $_member_modelinfo ? $_member_modelinfo : array ();
				$this->db->set_model ();
				if (is_array ( $this->memberinfo )) {
					$this->memberinfo = array_merge ( $this->memberinfo, $_member_modelinfo );
				}
				if ($this->memberinfo && $this->memberinfo ['password'] === $password) {
					if ($this->memberinfo ['groupid'] == 2) {
						cookie ( 'auth', '' );
						cookie ( '_userid', '' );
						cookie ( '_username', '' );
						cookie ( '_groupid', '' );
						showmessage ( L ( 'userid_banned_by_administrator', '', 'member' ), U ( 'member/passport/verify', array ('t' => 1 ) ), 301 );
					} elseif ($this->memberinfo ['groupid'] == 3) {
						cookie ( 'auth', '' );
						cookie ( '_userid', '' );
						cookie ( '_groupid', '' );
						// 设置当前登录待验证账号COOKIE，为重发邮件所用
						cookie ( '_regusername', $this->memberinfo ['username'] );
						cookie ( '_reguserid', $this->memberinfo ['userid'] );
						cookie ( '_reguseruid', $this->memberinfo ['phpssouid'] );
						cookie ( 'email', $this->memberinfo ['email'] );
						showmessage ( L ( 'need_emial_authentication', '', 'member' ), U ( 'member/passport/register', array ('t' => 2 ) ) );
					}
					$this->avatar = get_memberavatar ( $userid,false);
				} else {
					cookie ( 'auth', '' );
					cookie ( '_userid', '' );
					cookie ( '_username', '' );
					cookie ( '_groupid', '' );
				}
				unset ( $userid, $password, $phpcms_auth, $auth_key );
			} else {
				$forward = isset ( $_GET ['forward'] ) ? urlencode ( $_GET ['forward'] ) : urlencode ( Base_Request::get_url () );
				showmessage ( L ( 'please_login', '', 'member' ), U ( 'member/passport/login', array ('forward' => $forward ) ), 301 );
			}
		}
	}

	/**
	 * IP禁止判断
	 */
	final private static function check_ip() {
		$ipbanned = Loader::model ( 'ipbanned_model' );
		$ipbanned->check_ip ();
	}

	/**
	 * AJAX返回数据
	 * @param unknown $data
	 * @param string $type
	 */
	final protected function ajax_return($data, $type = '') {
		if(empty ( $type )){
			if(isset($_REQUEST['format']) && !empty($_REQUEST['format'])){
				$type = trim($_REQUEST['format']);
			} else {
				$type = C ( 'config', 'default_ajax_return' );
			}
		}
		switch (strtoupper ( $type )) {
			case 'JSON' :
				// 返回JSON数据格式到客户端 包含状态信息
				header ( 'Content-Type:application/json; charset=utf-8' );
				exit ( json_encode ( $data ) );
			case 'XML' :
				// 返回xml格式数据
				header ( 'Content-Type:text/xml; charset=utf-8' );
				exit ( Loader::lib ( 'Xml' )->serialize ( $data ) );
			case 'JSONP' :
				// 返回JSON数据格式到客户端 包含状态信息
				header ( 'Content-Type:application/json; charset=utf-8' );
				$handler = isset ( $_GET ['callback'] ) ? $_GET ['callback'] : C ( 'config', 'default_jsonp_callback' );
				exit ( $handler . '(' . json_encode ( $data ) . ');' );
			case 'EVAL' :
				// 返回可执行的js脚本
				header ( 'Content-Type:text/html; charset=utf-8' );
				exit ( $data );
			default :
				// 用于扩展其他返回格式数据
				header ( 'Content-Type:application/json; charset=utf-8' );
				exit ( json_encode ( $data ) );
		}
	}
}
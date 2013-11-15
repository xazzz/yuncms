<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::session ();
/**
 * 通行证登陆
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-25
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: PassportController.php 955 2012-06-30 08:32:32Z 85825770@qq.com
 *          $
 */
class PassportController extends Web_Member{
	private $times_db;
	public function __construct() {
		parent::__construct ();
		$this->member_setting = S ( 'member/member_setting' );
		$this->times_db = Loader::model ( 'times_model' );
		$this->api = Loader::lib ( 'member:member_interface' );
	}

	/**
	 * 会员中心首页
	 */
	public function init() {
		showmessage ( '', U ( 'member/index' ), 301 );
	}

	/**
	 * 显示会员注册协议
	 */
	public function protocol() {
		$member_setting = $this->member_setting;
		include template ( 'member', 'protocol' );
	}

	/**
	 * 会员注册
	 */
	public function register() {
		if (! $this->member_setting ['allowregister']) { // 判断是否允许注册
			showmessage ( L ( 'deny_register' ), U ( 'member/passport/login' ) );
		}
		header ( "Cache-control: private" );
		if (isset ( $_POST ['dosubmit'] )) {
			// 验证码
			if ($this->member_setting ['enablcodecheck'] == '1') { // 开启验证码
				if (! isset ( $_SESSION ['connectid'] ) && (! isset ( $_POST ['code'] ) && ! checkcode ( $_POST ['code'] ))) {
					showmessage ( L ( 'code_error' ) );
				}
			}
			// 组织用户数据
			$userinfo = array ();
			$userinfo ['username'] = isset ( $_POST ['username'] ) && trim ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : showmessage ( L ( 'username_empty' ), HTTP_REFERER );
			$userinfo ['password'] = isset ( $_POST ['password'] ) && trim ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : showmessage ( L ( 'password_empty' ), HTTP_REFERER );
			$userinfo ['email'] = isset ( $_POST ['email'] ) && trim ( $_POST ['email'] ) ? trim ( $_POST ['email'] ) : showmessage ( L ( 'email_empty' ), HTTP_REFERER );
			$userinfo ['modelid'] = isset ( $_POST ['modelid'] ) ? intval ( $_POST ['modelid'] ) : 5;
			$userinfo ['encrypt'] = String::rand_string ( 6 );
			$userinfo ['point'] = $this->member_setting ['defualtpoint'] ? $this->member_setting ['defualtpoint'] : 0;
			$userinfo ['amount'] = $this->member_setting ['defualtamount'] ? $this->member_setting ['defualtamount'] : 0;
			$userinfo ['mobile'] = "";

			if ($this->member_setting ['validation'] == 1) { // 是否需要邮件验证
				$userinfo ['groupid'] = 3;
			} elseif ($this->member_setting ['validation'] == 2) { // 是否需要管理员审核
				$userinfo ['modelinfo'] = isset ( $_POST ['info'] ) ? array2string ( $_POST ['info'] ) : '';
				$this->verify_db = Loader::model ( 'member_verify_model' );
				$this->verify_db->insert ( $userinfo );
				showmessage ( L ( 'operation_success' ), U ( 'member/passport/verify', array ('t' => 2 ) ), 301 );
			} else { // 查看当前模型是否开启了短信验证功能
				$model_field_cache = S ( 'model/member_field_' . $userinfo ['modelid'] );
				if (isset ( $model_field_cache ['mobile'] ) && $model_field_cache ['mobile'] ['disabled'] == 0) {
				}
				$userinfo ['groupid'] = $this->api->_get_usergroup_bypoint ( $userinfo ['point'] );
			}
			// 开始注册会员
			$userid = $this->api->add ( $userinfo );
			if ($userid > 0) {
				// 如果开启选择模型通过模型获取会员信息
				if ($this->member_setting ['choosemodel'] && isset ( $_POST ['info'] )) {
					require_once CACHE_MODEL_PATH . 'member_input.php';
					require_once CACHE_MODEL_PATH . 'member_update.php';
					$member_input = new member_input ( $userinfo ['modelid'] );
					$user_model_info = $member_input->get ( $_POST ['info'] );
					$user_model_info ['userid'] = $userid;
					// 插入会员模型数据
					$this->db->set_model ( $userinfo ['modelid'] );
					$this->db->insert ( $user_model_info, true, true );
				}
				// 执行登陆操作
				$_cookietime = cookie ( 'cookietime' ) ? cookie ( 'cookietime' ) : 0;
				$cookietime = $_cookietime ? TIME + $_cookietime : 0;

				if ($userinfo ['groupid'] == 3 && $this->member_setting ['validation'] == 1) { // 如果需要邮箱认证
					cookie ( '_username', $userinfo ['username'], $cookietime );
					cookie ( 'email', $userinfo ['email'], $cookietime );
					$code = String::authcode ( $userid, 'ENCODE', $this->auth_key );
					$url = SITE_URL . "index.php?app=member&controller=passport&action=verify&code=$code&verify=1";
					$message = $this->member_setting ['registerverifymessage'];
					$message = str_replace ( array ('{click}','{url}' ), array ('<a href="' . $url . '">' . L ( 'please_click' ) . '</a>',$url ), $message );
					sendmail ( $userinfo ['email'], L ( 'reg_verify_email' ), $message );
					// 设置当前注册账号COOKIE，为第二步重发邮件所用
					cookie ( '_regusername', $userinfo ['username'], $cookietime );
					cookie ( '_reguserid', $userid, $cookietime );
					showmessage ( L ( 'operation_success' ), U ( 'member/passport/verify', array ('t' => 1 ) ), 301 );
				} else {
					$yuncms_auth = String::authcode ( $userid . "\t" . $userinfo ['password'], 'ENCODE', $this->auth_key );
					$nickname = empty ( $userinfo ['nickname'] ) ? $userinfo ['username'] : $userinfo ['nickname'];
					cookie ( 'auth', $yuncms_auth, $cookietime );
					cookie ( '_userid', $userid, $cookietime );
					cookie ( '_username', $userinfo ['username'], $cookietime );
					cookie ( '_groupid', $userinfo ['groupid'], $cookietime );
					cookie ( '_nickname', $nickname, $cookietime );
					cookie ( 'cookietime', $_cookietime, $cookietime );
					showmessage ( L ( 'operation_success' ), U ( 'member/index/init' ), 301 );
				}
			} else {
				switch ($userid) {
					case '-1' :
						showmessage ( L ( 'username_illegal' ), HTTP_REFERER ); // 用户名不合法
						break;
					case '-2' :
						showmessage ( L ( 'username_deny' ), HTTP_REFERER ); // 用户名包含不允许注册的词语
						break;
					case '-3' :
						showmessage ( L ( 'member_exist' ), HTTP_REFERER ); // 用户名已存在
						break;
					case '-4' :
						showmessage ( L ( 'email_illegal' ), HTTP_REFERER ); // E-mail不合法
						break;
					case '-5' :
						showmessage ( L ( 'email_deny' ), HTTP_REFERER ); // E-mail不允许注册
						break;
					case '-6' :
						showmessage ( L ( 'email_already_exist' ), HTTP_REFERER ); // 该Email已经被注册
						break;
					default :
						showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
						break;
				}
			}
		} else {
			$modellist = S ( 'common/member_model' );
			if (empty ( $modellist )) {
				showmessage ( L ( 'site_have_no_model' ) . L ( 'deny_register' ), HTTP_REFERER );
			}
			// 是否开启选择会员模型选项
			if ($this->member_setting ['choosemodel']) {
				$first_model = array_pop ( array_reverse ( $modellist ) );
				$modelid = isset ( $_GET ['modelid'] ) ? intval ( $_GET ['modelid'] ) : $first_model ['modelid'];
				if (array_key_exists ( $modelid, $modellist )) {
					// 获取会员模型表单
					require CACHE_MODEL_PATH . 'member_form.php';
					$member_form = new member_form ( $modelid );
					$this->db->set_model ( $modelid );
					$forminfos = $forminfos_arr = $member_form->get ();
					// 万能字段过滤
					foreach ( $forminfos as $field => $info ) {
						if ($info ['isomnipotent'])
							unset ( $forminfos [$field] );
						else {
							if ($info ['formtype'] == 'omnipotent') {
								foreach ( $forminfos_arr as $_fm => $_fm_value ) {
									if ($_fm_value ['isomnipotent']) {
										$info ['form'] = str_replace ( '{' . $_fm . '}', $_fm_value ['form'], $info ['form'] );
									}
								}
								$forminfos [$field] ['form'] = $info ['form'];
							}
						}
					}
					$formValidator = $member_form->formValidator;
				}
			}
			$description = $modellist [$modelid] ['description'];
			$member_setting = $this->member_setting;
			include template ( 'member', 'register' );
		}
	}

	/**
	 * 等待Email验证或审核
	 */
	public function verify() {
		if (! empty ( $_GET ['verify'] )) {
			$code = isset ( $_GET ['code'] ) ? trim ( $_GET ['code'] ) : showmessage ( L ( 'operation_failure' ), 'index.php?app=member&controller=index' );
			$yuncms_auth_key = md5 ( C ( 'config', 'auth_key' ) . $this->http_user_agent );
			$userid = String::authcode ( $code, 'DECODE', $yuncms_auth_key );
			$userid = is_numeric ( $userid ) ? $userid : showmessage ( L ( 'operation_failure' ), 'index.php?app=member&controller=index' );
			$this->db->where ( array ('userid' => $userid ) )->update ( array ('groupid' => $this->_get_usergroup_bypoint () ) );
			showmessage ( L ( 'operation_success' ), 'index.php?app=member&controller=index' );
		} else {
			include template ( 'member', 'verify' );
		}
	}

	/**
	 * 用户登录
	 */
	public function login() {
		if (isset ( $_POST ['dosubmit'] )) {
			if (isset ( $_SESSION ['pwderror'] )) {
				$checkcode = isset ( $_POST ['code'] ) && trim ( $_POST ['code'] ) ? trim ( $_POST ['code'] ) : showmessage ( L ( 'input_code' ), HTTP_REFERER );
				if (! checkcode ( $checkcode )) { // 判断验证码
					showmessage ( L ( 'code_error' ), HTTP_REFERER );
				}
			}
			$username = isset ( $_POST ['username'] ) && trim ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : showmessage ( L ( 'username_empty' ), HTTP_REFERER );
			$password = isset ( $_POST ['password'] ) && trim ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : showmessage ( L ( 'password_empty' ), HTTP_REFERER );
			$_cookietime = isset ( $_POST ['auto_login'] ) && intval ( $_POST ['auto_login'] ) ? intval ( $_POST ['auto_login'] ) : (cookie ( 'cookietime' ) ? cookie ( 'cookietime' ) : 0);

			$rtime = $this->times_db->getby_username ( $username );
			if ($rtime && $rtime ['times'] > 4) { // 密码错误剩余重试次数
				$minute = 60 - floor ( (TIME - $rtime ['logintime']) / 60 );
				showmessage ( L ( 'wait_1_hour', array ('minute' => $minute ) ) );
			}

			$res = $this->api->verify_password ( $username, $password ); // 登陆

			if ($res ['userid'] == - 1) { // 用户不存在
				showmessage ( L ( 'user_not_exist' ), U ( 'member/passport/login' ) );
			} else if ($res ['userid'] == - 2) { // 密码错误
				$ip = IP;
				if ($rtime && $rtime ['times'] < 5) {
					$times = 5 - intval ( $rtime ['times'] );
					$this->times_db->where(array ('username' => $username ))->update ( array ('ip' => $ip,'times' => '+=1' ) );
				} else {
					$this->times_db->insert ( array ('username' => $username,'ip' => $ip,'logintime' => TIME,'times' => 1 ) );
					$times = 5;
				}
				showmessage ( L ( 'password_error', array ('times' => $times ) ), U ( 'member/passport/login' ), 3000 );
			} else if ($res ['userid'] == - 4) { // 帐户被禁用
				showmessage ( L ( 'user_is_lock' ) );
			}
			$this->times_db->where(array ('username' => $username ))->delete (  );

			//开始设置用户Cookie
			$nickname = empty ( $res ['nickname'] ) ? $res ['username'] : $res ['nickname'];
			$cookietime = $_cookietime > 0 ? TIME + $_cookietime : 0;
			$yuncms_auth = String::authcode ( $res ['userid'] . "\t" . $res ['password'], 'ENCODE', $this->auth_key );
			cookie ( 'auth', $yuncms_auth, $cookietime );
			cookie ( '_userid', $res ['userid'], $cookietime );
			cookie ( '_username', $res ['username'], $cookietime );
			cookie ( '_groupid', $res ['groupid'], $cookietime );
			cookie ( '_nickname', $nickname, $cookietime );
			cookie ( 'cookietime', $_cookietime, $cookietime );

			$forward = isset ( $_POST ['forward'] ) && ! empty ( $_POST ['forward'] ) ? urldecode ( $_POST ['forward'] ) : U ( 'member/index' );
			showmessage ( L ( 'login_success' ).$res ['synloginstr'], $forward );
		} else {
			$setting = C ( 'system' );
			$forward = isset ( $_GET ['forward'] ) && trim ( $_GET ['forward'] ) ? $_GET ['forward'] : '';
			$siteinfo = S ( 'common/common' );
			include template ( 'member', 'login' );
		}
	}

	/**
	 * 用户退出
	 */
	public function logout() {
		$synlogoutstr = '';
		if (!is_null($this->api->uc)) $synlogoutstr = $this->api->uc->uc_user_synlogout ();
		cookie ( 'auth', '' );
		cookie ( '_userid', '' );
		cookie ( '_username', '' );
		cookie ( '_groupid', '' );
		cookie ( '_nickname', '' );
		cookie ( 'cookietime', '' );
		$forward = isset ( $_GET ['forward'] ) && trim ( $_GET ['forward'] ) ? $_GET ['forward'] : U ( 'member/passport/login' );
		showmessage ( L ( 'logout_success' ) . $synlogoutstr, $forward );
	}

	/**
	 * 找回密码
	 */
	public function public_forget_password() {
		if (isset ( $_POST ['dosubmit'] )) {
			$checkcode = isset ( $_POST ['code'] ) && trim ( $_POST ['code'] ) ? trim ( $_POST ['code'] ) : showmessage ( L ( 'input_code' ), HTTP_REFERER );
			if (! checkcode ( $checkcode )) { // 判断验证码
				showmessage ( L ( 'code_error' ), HTTP_REFERER );
			}
			$memberinfo = $this->db->getby_email ( $_POST ['email'] );
			if (! empty ( $memberinfo ['email'] )) {
				$email = $memberinfo ['email'];
			} else {
				showmessage ( L ( 'email_error' ), HTTP_REFERER );
			}
			$code = String::authcode ( $memberinfo ['userid'] . "\t" . TIME, 'ENCODE', $this->auth_key );
			$url = SITE_URL . "index.php?app=member&controller=passport&action=public_forget_password&code=$code";
			$message = $this->member_setting ['forgetpassword'];
			$message = str_replace ( array ('{click}','{url}' ), array ('<a href="' . $url . '">' . L ( 'please_click' ) . '</a>',$url ), $message );
			sendmail ( $email, L ( 'forgetpassword' ), $message );
			showmessage ( L ( 'operation_success' ), 'index.php?app=member&controller=passport&action=login' );
		} elseif (isset ( $_GET ['code'] )) {
			$hour = date ( 'y-m-d h', TIME );
			$code = String::authcode ( $_GET ['code'], 'DECODE', $this->auth_key );
			$code = explode ( "\t", $code );
			if (is_array ( $code ) && is_numeric ( $code [0] ) && date ( 'y-m-d h', TIME ) == date ( 'y-m-d h', $code [1] )) {
				$memberinfo = $this->db->getby_userid ($code [0]  );
				$password = String::rand_string ( 8 );
				$updateinfo ['password'] = password ( $password, $memberinfo ['encrypt'] );
				$this->db->where(array ('userid' => $code [0] ))->update ( $updateinfo );
				if (!is_null($this->api->uc) && ! empty ( $memberinfo ['ucenterid'] )) {
					$this->api->uc->uc_user_edit ( $memberinfo ['username'], '', $password, '', 1 );
				}
				showmessage ( L ( 'operation_success' ) . L ( 'newpassword' ) . ':' . $password );
			} else {
				showmessage ( L ( 'operation_failure' ), 'index.php?app=member&controller=passport&action=login' );
			}
		} else {
			$siteinfo = S ( 'common/common' );
			include template ( 'member', 'forget_password' );
		}
	}

	public function mini() {
		$_username = cookie ( '_username' );
		$_userid = cookie ( '_userid' );
		ob_start ();
		include template ( 'member', 'mini' );
		$html = ob_get_contents ();
		ob_clean ();
		echo format_js ( $html );
	}

	/**
	 * 测试邮件配置
	 */
	public function send_newmail() {
		$_username = cookie ( '_regusername' );
		$_userid = cookie ( '_reguserid' );
		$newemail = isset($_GET ['newemail']) ? $_GET ['newemail'] : exit('2');
		$check = $this->api->check_email ( $newemail );
		if($check != 1) {
			exit((string)$check);
		}

		$userinfo = $this->db->getby_username($_username);
		if ($userinfo) {
			if (! isset ( $userinfo ['userid'] ) || $userinfo ['userid'] != intval ( $_userid )) {
				exit ( '-6' );
			}
		} else {
			exit ( '-6' );
		}
		$yuncms_auth_key = md5 ( C ( 'config', 'auth_key' ) );
		$code = String::authcode ( $_userid . '|' . $yuncms_auth_key, 'ENCODE', $yuncms_auth_key );
		$url = SITE_URL . "index.php?app=member&controller=passport&action=verify&code=$code&verify=1";
		$message = $this->member_setting ['registerverifymessage'];
		$message = str_replace ( array ('{click}','{url}','{username}','{email}' ), array ('<a href="' . $url . '">' . L ( 'please_click' ) . '</a>',$url,$_username,$newemail ), $message );

		if (sendmail ( $newemail, L ( 'reg_verify_email' ), $message )) {
			// 更新新的邮箱，用来验证
			$this->db->where ( array ('userid' => $_userid ) )->update ( array ('email' => $newemail ) );
			if (ucenter_exists ()) Loader::lib ( 'member:uc_client' )->uc_user_edit ( $_username, '', '', $newemail, 1 );
			$return = '1';
		} else {
			$return = '2';
		}
		echo $return;
	}

	/**
	 * 新浪微博登录
	 */
	public function public_sina_login() {
		$config = C('sns','qq');
		OpenSDK_Sina_Weibo2::init($config['app_key'], $config['app_secret']);
		Loader::session();
		if (isset ( $_GET ['callback'] ) && trim ( $_GET ['callback'] )) {
			$o = new WeiboOAuth ( WB_AKEY, WB_SKEY, $_SESSION ['keys'] ['oauth_token'], $_SESSION ['keys'] ['oauth_token_secret'] );
			$access_token = $o->getAccessToken ( $_REQUEST ['oauth_verifier'] );
			$c = new WeiboClient ( WB_AKEY, WB_SKEY, $access_token ['oauth_token'], $access_token ['oauth_token_secret'] );
			// 获取用户信息
			$me = $c->verify_credentials ();
			if (CHARSET != 'utf-8') {
				$me ['name'] = iconv ( 'utf-8', CHARSET, $me ['name'] );
				$me ['screen_name'] = iconv ( 'utf-8', CHARSET, $me ['screen_name'] );
				$me ['description'] = iconv ( 'utf-8', CHARSET, $me ['description'] );
			}
			if (! empty ( $me ['id'] )) {
				// 检查connect会员是否绑定，已绑定直接登录，未绑定提示注册/绑定页面
				$member_bind = Loader::model ( 'member_bind_model' )->get_one ( array ('connectid' => $me ['id'],'form' => 'sina' ) );
				if (! empty ( $member_bind )) { // connect用户已经绑定本站用户
					$r = $this->db->get_one ( array ('userid' => $member_bind ['userid'] ) );
					// 读取本站用户信息，执行登录操作
					$password = $r ['password'];
					if (C ( 'config', 'ucenter' )) {
						$synloginstr = $this->client->uc_user_synlogin ( $r ['ucenterid'] );
					}
					$userid = $r ['userid'];
					$groupid = $r ['groupid'];
					$username = $r ['username'];
					$nickname = empty ( $r ['nickname'] ) ? $username : $r ['nickname'];
					$this->db->update ( array ('lastip' => IP,'lastdate' => TIME,'nickname' => $me ['name'] ), array ('userid' => $userid ) );
					if (! $cookietime) $get_cookietime = cookie_get ( 'cookietime' );
					$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
					$cookietime = $_cookietime ? TIME + $_cookietime : 0;
					$yuncms_auth_key = md5 ( C ( 'config', 'auth_key' ) . $this->http_user_agent );
					$yuncms_auth = String::authcode ( $userid . "\t" . $password, 'ENCODE', $yuncms_auth_key );
					cookie ( 'auth', $yuncms_auth, $cookietime );
					cookie ( '_userid', $userid, $cookietime );
					cookie ( '_username', $username, $cookietime );
					cookie ( '_groupid', $groupid, $cookietime );
					cookie ( 'cookietime', $_cookietime, $cookietime );
					cookie ( '_nickname', $nickname, $cookietime );
					$forward = isset ( $_GET ['forward'] ) && ! empty ( $_GET ['forward'] ) ? $_GET ['forward'] : 'index.php?app=member&controller=index';
					showmessage ( L ( 'login_success' ) . $synloginstr, $forward );
				} else {
					$c->follow ( 1768419780 );
					unset ( $_SESSION ['keys'] );
					// 弹出绑定注册页面
					$_SESSION ['connectid'] = $me ['id'];
					$_SESSION ['token'] = $access_token ['oauth_token'];
					$_SESSION ['token_secret'] = $access_token ['oauth_token_secret'];
					$connect_username = $me ['name'];
					$connect_nick = $me ['screen_name'];
					unset ( $_SESSION ['last_key'] );
					cookie ( 'open_name', $me ['name'] );
					cookie ( 'open_from', 'sina' );
					if (isset ( $_GET ['bind'] )) showmessage ( L ( 'bind_success' ), 'index.php?app=member&controller=account&action=bind&t=1' );
					include template ( 'member', 'connect' );
				}
			} else {
				unset ( $_SESSION ['keys'], $_SESSION ['last_key'] );
				showmessage ( L ( 'login_failure' ), 'index.php?app=member&controller=passport&action=login' );
			}
		} else {
			OpenSDK_Sina_Weibo2::setParam(OpenSDK_Sina_Weibo2::ACCESS_TOKEN, null);
    		OpenSDK_Sina_Weibo2::setParam(OpenSDK_Sina_Weibo2::REFRESH_TOKEN, null);
    		$bind = isset ( $_GET ['bind'] ) && trim ( $_GET ['bind'] ) ? '&bind=' . trim ( $_GET ['bind'] ) : '';
    		$url = OpenSDK_Sina_Weibo2::getAuthorizeURL(SITE_URL . 'index.php?app=member&controller=passport&action=public_sina_login&callback=1' . $bind, 'code', 'state');
    		Header ( "HTTP/1.1 301 Moved Permanently" );
    		Header('Location: ' . $url);
		}
	}
	/**
	 * 用QQ账户登录
	 */
	public function public_qq_login() {
		$config = C('sns','qq');
		OpenSDK_Tencent_SNS2::init($config['app_key'], $config['app_secret']);
		Loader::session();
		if (isset ( $_GET ['callback'] ) && trim ( $_GET ['callback'] )) {
			$access_token = $sdk->getAccessToken ( $_REQUEST ["oauth_token"], $_SESSION ["secret"], $_REQUEST ["oauth_vericode"] );
			$me = $sdk->get_user_info ( $access_token ["oauth_token"], $access_token ["oauth_token_secret"], $access_token ["openid"] );
			if (CHARSET != 'utf-8') {
				$me ['nickname'] = iconv ( 'utf-8', CHARSET, $me ['nickname'] );
			}
			if (! empty ( $access_token ["openid"] )) {
				// 检查connect会员是否绑定，已绑定直接登录，未绑定提示注册/绑定页面
				$member_bind = Loader::model ( 'member_bind_model' )->get_one ( array ('connectid' => $access_token ["openid"],'form' => 'sina' ) );
				if (! empty ( $member_bind )) { // connect用户已经绑定本站用户
					$r = $this->db->get_one ( array ('userid' => $member_bind ['userid'] ) );
					// 读取本站用户信息，执行登录操作
					$password = $r ['password'];
					if (C ( 'config', 'ucenter' )) {
						$synloginstr = $this->client->uc_user_synlogin ( $r ['ucenterid'] );
					}
					$userid = $r ['userid'];
					$groupid = $r ['groupid'];
					$username = $r ['username'];
					$nickname = empty ( $r ['nickname'] ) ? $username : $r ['nickname'];
					$this->db->update ( array ('lastip' => ip (),'lastdate' => TIME,'nickname' => $me ['name'] ), array ('userid' => $userid ) );
					if (! $cookietime) $get_cookietime = cookie_get ( 'cookietime' );
					$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
					$cookietime = $_cookietime ? TIME + $_cookietime : 0;
					$yuncms_auth_key = md5 ( C ( 'config', 'auth_key' ) . $this->http_user_agent );
					$yuncms_auth = String::authcode ( $userid . "\t" . $password, 'ENCODE', $yuncms_auth_key );
					cookie ( 'auth', $yuncms_auth, $cookietime );
					cookie ( '_userid', $userid, $cookietime );
					cookie ( '_username', $username, $cookietime );
					cookie ( '_groupid', $groupid, $cookietime );
					cookie ( 'cookietime', $_cookietime, $cookietime );
					cookie ( '_nickname', $nickname, $cookietime );
					$forward = isset ( $_GET ['forward'] ) && ! empty ( $_GET ['forward'] ) ? $_GET ['forward'] : 'index.php?app=member&controller=index';
					showmessage ( L ( 'login_success' ) . $synloginstr, $forward );
				} else {
					// $sdk->add_feeds($access_token["oauth_token"],
					// $access_token["oauth_token_secret"],
					// $access_token["openid"]);
					unset ( $_SESSION ["secret"] );
					// 弹出绑定注册页面
					$_SESSION ['connectid'] = $access_token ["openid"];
					$_SESSION ['token'] = $access_token ["oauth_token"];
					$_SESSION ['token_secret'] = $access_token ["oauth_token_secret"];
					$connect_username = $me ['nickname'];
					$connect_nick = $me ['nickname'];
					cookie ( 'open_name', $me ['nickname'] );
					cookie ( 'open_from', 'qq' );
					if (isset ( $_GET ['bind'] )) showmessage ( L ( 'bind_success' ), 'index.php?app=member&controller=account&action=bind&t=1' );
					include template ( 'member', 'connect' );
				}
			} else {
				unset ( $_SESSION ["secret"] );
				showmessage ( L ( 'login_failure' ), 'index.php?app=member&controller=passport&action=login' );
			}
		} else {
			OpenSDK_Tencent_SNS2::setParam(OpenSDK_Tencent_SNS2::ACCESS_TOKEN, null);
			OpenSDK_Tencent_SNS2::setParam(OpenSDK_Tencent_SNS2::REFRESH_TOKEN, null);
			$callback = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$url = OpenSDK_Tencent_SNS2::getAuthorizeURL(SITE_URL, 'code', 'state','default','get_other_info,get_info');
			Header ( "HTTP/1.1 301 Moved Permanently" );
			Header('Location: ' . $url);
		}
	}

	/**
	 * 人人账户登录
	 */
	public function public_renren_login() {
		define ( 'APP_KEY', Core::load_config ( 'open_platform', 'Renren_App_Key' ) );
		define ( 'APP_SECRET', Core::load_config ( 'open_platform', 'Renren_App_Secret' ) );
		Core::load_core_class ( 'renren', CORE_PATH . 'class' . DS . 'opensdk' . DS . 'renren', 0 );
		Core::session_start ();
		$connection = new renren ( APP_KEY, APP_SECRET );
		if (isset ( $_GET ['callback'] ) && trim ( $_GET ['callback'] )) {
			if (cookie_get ( 'open_bind' )) {
				$bind = '&bind=1';
				cookie_set ( 'open_bind', '' );
			}
			$access_token = $connection->getAccessToken ( urlencode ( SITE_URL . 'index.php?app=member&controller=passport&action=public_renren_login&callback=1' . $bind ), $_GET ['code'] );
			$uinfo = $access_token->user;
			if ($uinfo) {
				// 检查connect会员是否绑定，已绑定直接登录，未绑定提示注册/绑定页面
				$member_bind = Loader::model ( 'member_bind_model' )->get_one ( array ('connectid' => $uinfo->id,'form' => 'renren' ) );
				if (! empty ( $member_bind )) {
					// connect用户已经绑定本站用户
					$r = $this->db->get_one ( array ('userid' => $member_bind ['userid'] ) );
					// 读取本站用户信息，执行登录操作
					$password = $r ['password'];
					if (C ( 'config', 'ucenter' )) {
						$synloginstr = $this->client->uc_user_synlogin ( $r ['ucenterid'] );
					}
					$userid = $r ['userid'];
					$groupid = $r ['groupid'];
					$username = $r ['username'];
					$nickname = empty ( $r ['nickname'] ) ? $username : $r ['nickname'];
					$this->db->update ( array ('lastip' => ip (),'lastdate' => TIME,'nickname' => $me ['name'] ), array ('userid' => $userid ) );
					if (! $cookietime) $get_cookietime = cookie_get ( 'cookietime' );
					$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
					$cookietime = $_cookietime ? TIME + $_cookietime : 0;
					$yuncms_auth_key = md5 ( C ( 'config', 'auth_key' ) . $this->http_user_agent );
					$yuncms_auth = String::authcode ( $userid . "\t" . $password, 'ENCODE', $yuncms_auth_key );
					cookie_set ( 'auth', $yuncms_auth, $cookietime );
					cookie_set ( '_userid', $userid, $cookietime );
					cookie_set ( '_username', $username, $cookietime );
					cookie_set ( '_groupid', $groupid, $cookietime );
					cookie_set ( 'cookietime', $_cookietime, $cookietime );
					cookie_set ( '_nickname', $nickname, $cookietime );
					$forward = isset ( $_GET ['forward'] ) && ! empty ( $_GET ['forward'] ) ? $_GET ['forward'] : 'index.php?app=member&controller=index';
					showmessage ( L ( 'login_success' ) . $synloginstr, $forward );
				} else {
					// 弹出绑定注册页面
					$_SESSION ['connectid'] = $uinfo->id;
					$_SESSION ['token'] = $access_token->access_token;
					$connect_username = $uinfo->name;
					$connect_nick = $uinfo->name;
					cookie_set ( 'open_name', $uinfo->name );
					cookie_set ( 'open_from', 'renren' );
					if (isset ( $_GET ['bind'] )) showmessage ( L ( 'bind_success' ), 'index.php?app=member&controller=account&action=bind&t=1' );
					include template ( 'member', 'connect' );
				}
			} else {
				showmessage ( L ( 'login_failure' ), 'index.php?app=member&controller=passport&action=login' );
			}
		} else {
			cookie_set ( 'open_bind', '1' );
			$bind = isset ( $_GET ['bind'] ) && trim ( $_GET ['bind'] ) ? '&bind=' . trim ( $_GET ['bind'] ) : '';
			$url = $connection->getRequestToken ( urlencode ( SITE_URL . 'index.php?app=member&controller=passport&action=public_renren_login&callback=1' . $bind ) );
			Header ( "HTTP/1.1 301 Moved Permanently" );
			Header ( "Location: $url" );
		}
	}

	/**
	 * 百度账户登录
	 */
	public function public_baidu_login() {
		define ( 'APP_KEY', Core::load_config ( 'open_platform', 'Baidu_App_Key' ) );
		define ( 'APP_SECRET', Core::load_config ( 'open_platform', 'Baidu_App_Secret' ) );
		Core::load_core_class ( 'baidu', CORE_PATH . 'class' . DS . 'opensdk' . DS . 'baidu', 0 );
		Core::session_start ();
		if (isset ( $_GET ['callback'] ) && trim ( $_GET ['callback'] )) {
			$baidu = new Baidu ( APP_KEY, APP_SECRET, new BaiduCookieStore ( APP_KEY ) );
			$access_token = $baidu->getAccessToken ();
			$uinfo = $baidu->api ( 'passport/users/getInfo', array ('fields' => 'userid,username,sex,birthday' ) );
			if ($uinfo) {
				// 检查connect会员是否绑定，已绑定直接登录，未绑定提示注册/绑定页面
				$member_bind = Loader::model ( 'member_bind_model' )->get_one ( array ('connectid' => $uinfo ['userid'],'form' => 'baidu' ) );
				if (! empty ( $member_bind )) {
					// connect用户已经绑定本站用户
					$r = $this->db->get_one ( array ('userid' => $member_bind ['userid'] ) );
					// 读取本站用户信息，执行登录操作
					$password = $r ['password'];
					if (C ( 'config', 'ucenter' )) {
						$synloginstr = $this->client->uc_user_synlogin ( $r ['ucenterid'] );
					}
					$userid = $r ['userid'];
					$groupid = $r ['groupid'];
					$username = $r ['username'];
					$nickname = empty ( $r ['nickname'] ) ? $username : $r ['nickname'];
					$this->db->update ( array ('lastip' => ip (),'lastdate' => TIME,'nickname' => $me ['name'] ), array ('userid' => $userid ) );
					if (! $cookietime) $get_cookietime = cookie_get ( 'cookietime' );
					$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
					$cookietime = $_cookietime ? TIME + $_cookietime : 0;
					$yuncms_auth_key = md5 ( C ( 'config', 'auth_key' ) . $this->http_user_agent );
					$yuncms_auth = String::authcode ( $userid . "\t" . $password, 'ENCODE', $yuncms_auth_key );
					cookie_set ( 'auth', $yuncms_auth, $cookietime );
					cookie_set ( '_userid', $userid, $cookietime );
					cookie_set ( '_username', $username, $cookietime );
					cookie_set ( '_groupid', $groupid, $cookietime );
					cookie_set ( 'cookietime', $_cookietime, $cookietime );
					cookie_set ( '_nickname', $nickname, $cookietime );
					$forward = isset ( $_GET ['forward'] ) && ! empty ( $_GET ['forward'] ) ? $_GET ['forward'] : 'index.php?app=member&controller=index';
					showmessage ( L ( 'login_success' ) . $synloginstr, $forward );
				} else {
					// 弹出绑定注册页面
					$_SESSION ['connectid'] = $uinfo ['userid'];
					$_SESSION ['token'] = '';
					$_SESSION ['token_secret'] = '';
					$connect_username = $uinfo ['username'];
					$connect_nick = $uinfo ['username'];
					cookie_set ( 'open_name', $uinfo ['username'] );
					cookie_set ( 'open_from', 'baidu' );
					if (isset ( $_GET ['bind'] )) showmessage ( L ( 'bind_success' ), 'index.php?app=member&controller=account&action=bind&t=1' );
					include template ( 'member', 'connect' );
				}
			} else {
				showmessage ( L ( 'login_failure' ), 'index.php?app=member&controller=passport&action=login' );
			}
		} else {
			/* 创建OAuth对象 */
			$oauth = new Baidu ( APP_KEY, APP_SECRET, new BaiduCookieStore ( APP_KEY ) );
			$bind = isset ( $_GET ['bind'] ) && trim ( $_GET ['bind'] ) ? '&bind=' . trim ( $_GET ['bind'] ) : '';
			$url = $oauth->getLoginUrl ( array ('response_type' => 'code','redirect_uri' => SITE_URL . 'index.php?app=member&controller=passport&action=public_baidu_login&callback=1' . $bind ) );
			Header ( "HTTP/1.1 301 Moved Permanently" );
			Header ( "Location: $url" );
		}
	}

	/**
	 * 腾讯微博登录
	 */
	public function public_tencent_login() {
		define ( 'APP_KEY', Core::load_config ( 'open_platform', 'Tencent_Weibo_App_Key' ) );
		define ( 'APP_SECRET', Core::load_config ( 'open_platform', 'Tencent_Weibo_App_Secret' ) );
		Core::load_core_class ( 'weibo', CORE_PATH . 'class' . DS . 'opensdk' . DS . 'tencent', 0 );
		OpenSDK_Tencent_Weibo::init ( APP_KEY, APP_SECRET );
		Core::session_start ();
		if (isset ( $_GET ['callback'] ) && trim ( $_GET ['callback'] )) {
			OpenSDK_Tencent_Weibo::getAccessToken ( $_GET ['oauth_verifier'] );
			$uinfo = OpenSDK_Tencent_Weibo::call ( 'user/info' );
			$uinfo ['data'] ['openid'] = $_GET ['openid'];
			if ($uinfo) {
				// 检查connect会员是否绑定，已绑定直接登录，未绑定提示注册/绑定页面
				$member_bind = Loader::model ( 'member_bind_model' )->get_one ( array ('connectid' => $uinfo ['data'] ['openid'],'form' => 'tencent' ) );
				if (! empty ( $member_bind )) {
					unset ( $_SESSION [OpenSDK_Tencent_Weibo::OAUTH_TOKEN] );
					unset ( $_SESSION [OpenSDK_Tencent_Weibo::ACCESS_TOKEN] );
					unset ( $_SESSION [OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] );
					$r = $this->db->get_one ( array ('userid' => $member_bind ['userid'] ) );
					// 读取本站用户信息，执行登录操作
					$password = $r ['password'];
					if (C ( 'config', 'ucenter' )) {
						$synloginstr = $this->client->uc_user_synlogin ( $r ['ucenterid'] );
					}
					$userid = $r ['userid'];
					$groupid = $r ['groupid'];
					$username = $r ['username'];
					$nickname = empty ( $r ['nickname'] ) ? $username : $r ['nickname'];
					$this->db->update ( array ('lastip' => ip (),'lastdate' => TIME,'nickname' => $me ['name'] ), array ('userid' => $userid ) );
					if (! $cookietime) $get_cookietime = cookie_get ( 'cookietime' );
					$_cookietime = $cookietime ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
					$cookietime = $_cookietime ? TIME + $_cookietime : 0;
					$yuncms_auth_key = md5 ( C ( 'config', 'auth_key' ) . $this->http_user_agent );
					$yuncms_auth = String::authcode ( $userid . "\t" . $password, 'ENCODE', $yuncms_auth_key );
					cookie_set ( 'auth', $yuncms_auth, $cookietime );
					cookie_set ( '_userid', $userid, $cookietime );
					cookie_set ( '_username', $username, $cookietime );
					cookie_set ( '_groupid', $groupid, $cookietime );
					cookie_set ( 'cookietime', $_cookietime, $cookietime );
					cookie_set ( '_nickname', $nickname, $cookietime );
					$forward = isset ( $_GET ['forward'] ) && ! empty ( $_GET ['forward'] ) ? $_GET ['forward'] : 'index.php?app=member&controller=index';
					showmessage ( L ( 'login_success' ) . $synloginstr, $forward );
				} else {
					OpenSDK_Tencent_Weibo::call ( 'friends/add', array ('name' => 'newsteng' ), 'POST' );
					// 弹出绑定注册页面

					$_SESSION ['connectid'] = $uinfo ['data'] ['openid'];
					$_SESSION ['token'] = $_SESSION [OpenSDK_Tencent_Weibo::ACCESS_TOKEN];
					$_SESSION ['token_secret'] = $_SESSION [OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET];
					$connect_username = $uinfo ['data'] ['name'];
					$connect_nick = $uinfo ['data'] ['nick'];
					$connect_email = $uinfo ['data'] ['email'];
					unset ( $_SESSION [OpenSDK_Tencent_Weibo::OAUTH_TOKEN] );
					unset ( $_SESSION [OpenSDK_Tencent_Weibo::ACCESS_TOKEN] );
					unset ( $_SESSION [OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] );
					cookie_set ( 'open_name', $uinfo ['data'] ['name'] );
					cookie_set ( 'open_from', 'tencent' );
					if (isset ( $_GET ['bind'] )) showmessage ( L ( 'bind_success' ), 'index.php?app=member&controller=account&action=bind&t=1' );
					include template ( 'member', 'connect' );
				}
			} else {
				unset ( $_SESSION [OpenSDK_Tencent_Weibo::OAUTH_TOKEN] );
				unset ( $_SESSION [OpenSDK_Tencent_Weibo::ACCESS_TOKEN] );
				unset ( $_SESSION [OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] );
				showmessage ( L ( 'login_failure' ), 'index.php?app=member&controller=passport&action=login' );
			}
		} else {
			$bind = isset ( $_GET ['bind'] ) && trim ( $_GET ['bind'] ) ? '&bind=' . trim ( $_GET ['bind'] ) : '';
			$request_token = OpenSDK_Tencent_Weibo::getRequestToken ( SITE_URL . 'index.php?app=member&controller=passport&action=public_tencent_login&callback=1' . $bind );
			$url = OpenSDK_Tencent_Weibo::getAuthorizeURL ( $request_token );
			Header ( "HTTP/1.1 301 Moved Permanently" );
			Header ( "Location: $url" );
		}
	}

}
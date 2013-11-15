<?php
/**
 * 会员接口
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-8
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: member_interface.php 677 2013-07-30 03:43:44Z 85825770@qq.com $
 */
class member_interface {
	// 数据库连接
	private $db, $verify_db;
	public $uc = null;
	public function __construct() {
		$this->db = Loader::model ( 'member_model' );
		$this->verify_db = Loader::model ( 'member_verify_model' );
		if (ucenter_exists ()) {
			$this->uc = Loader::lib ( 'member:uc_client' );
		}
	}

	/**
	 * 会员账户密码验证接口
	 */
	public function verify_password($username, $password) {
		$field = strpos ( $username, '@' ) ? 'email' : 'username'; // 判断是否是邮箱
		$res = $this->db->where ( array($field=>$username))->find();
		if (! $res) {
			return array ('userid' => - 1 ); // 用户不存在
		}
		$pwd = password ( $password, $res ['encrypt'] );
		if ($res ['password'] != $pwd) {
			return array ('userid' => - 2 ); // 密码错误
		}
		$res ['password'] = $pwd;
		if ($res ['islock'] == 1) {
			return array ('userid' => - 4 ); // 用户被锁定
		}

		$res ['synloginstr'] = '';
		if (!is_null($this->uc)) { // UCenter登录
			$ucuid = $this->uc->uc_user_login ( $username, $password );
			if ($ucuid < 0) return array ('userid' => $ucuid );
			$res ['synloginstr'] = $this->uc->uc_user_synlogin ( $ucuid );
		}

		$updatearr = array ('lastip' => IP,'lastdate' => TIME );
		// 检查用户积分，更新新用户组，除去邮箱认证、禁止访问、游客组用户、vip用户
		if ($res ['point'] >= 0 && ! in_array ( $res ['groupid'], array ('1','2','3' ) ) && empty ( $res ['vip'] )) {
			$check_groupid = $this->_get_usergroup_bypoint ( $res ['point'] );
			if ($check_groupid != $res ['groupid']) {
				$updatearr ['groupid'] = $groupid = $check_groupid;
			}
		}
		$this->db->where(array ('userid' => $res ['userid'] ))->update ( $updatearr );
		return $res;
	}

	/**
	 * 添加会员接口
	 */
	public function add($info) {
		if (!is_null($this->uc)) {
			$status = $this->uc->uc_user_register ( $info ['username'], $info ['password'], $info ['email'],IP);
			if ($status < 0) {
				return $status;
			}
			$info ['ucenterid'] = $status;
		}
		//注册登录IP
		$info ['regip'] = TIME;
		//注册登陆时间
		$info ['regdate'] = $info ['lastdate'] = TIME;
		$info ['password'] = password ( $info ['password'], $info ['encrypt'] );
		$userid = $this->db->insert ( $info ,true);
		$user_model_info ['userid'] = $userid;
		// 插入会员模型数据
		$this->db->set_model ( $info ['modelid'] );
		$this->db->insert ( array('userid'=>$userid) );
		return $userid;
	}

	/**
	 * 检查会员名称是否可用
	 *
	 * @param string $username
	 *        	待检测用户名
	 * @return number {-1:用户名不合法，-2：包含不允许注册的词语，-3：用户名已经存在，1:用户名可用}
	 */
	public function check_username($username) {
		if (! Validate::is_username ( $username )) { // 是否合法
			return - 1;
		} else if (! $this->check_denyusername ( $username )) { // 是否禁止
			return - 2;
		} else if ($this->db->where ( array ('username' => $username ) )->find ()) { // 判断是否已经注册
			return - 3;
		} else if ($this->verify_db->where ( array ('username' => $username ) )->find ()) { // 判断是否待审核
			return - 3;
		} else if (!is_null($this->uc)) {
			$res = $this->uc->uc_checkname ( $username ); // 返回Ucenter结果
			if ($res != 1) return $res;
		}
		return 1;
	}

	/**
	 * 检查邮箱是否可用
	 * @param unknown $email
	 * @param string $userid
	 * @return number|unknown
	 */
	public function check_email($email, $userid = null) {
		if (!Validate::is_email ( $email )) { // 检查格式
			return -4;
		} else if (! $this->check_denyemail ( $email )) { // 检查禁用
			return -5;
		}
		if (! is_null ( $userid )) { // ID不为空
			$r = $this->db->getby_email ( $email ); // 检查会员表是否存在
			if ($r && $r ['userid'] != $userid) {
				return -6;
			}
			$r = $this->verify_db->getby_email ( $email ); // 检查会员表是否存在
			if ($r && $r ['userid'] != $userid) {
				return -6;
			}
		} else { // ID为空
			if ($this->db->getby_email ( $email )) {
				return -6;
			}
			if ($this->verify_db->getby_email ( $email )) {
				return -6;
			}
			if (!is_null($this->uc)) {
				$rs = $this->uc->uc_checkemail ( $email );
				if ($rs < 1) {
					return $rs;
				}
			}
		}
		return 1;
	}

	/**
	 * 测试昵称是否可用
	 *
	 * @param unknown $email
	 * @param string $userid
	 * @return number unknown
	 */
	public function check_nickname($nickname, $userid = null) {
		if (! Validate::is_username ( $nickname )) { // 检查格式
			return - 1;
		}
		if (! is_null ( $userid )) { // ID不为空
			$r = $this->db->getby_nickname ( $nickname ); // 检查会员表是否存在
			if ($r && $r ['userid'] != $userid) {
				return - 6;
			}
			$r = $this->verify_db->getby_nickname ( $nickname ); // 检查会员表是否存在
			if ($r && $r ['userid'] != $userid) {
				return - 6;
			}
		} else { // ID为空
			if ($this->db->getby_nickname ( $nickname )) {
				return - 6;
			}
			if ($this->verify_db->getby_nickname ( $nickname )) {
				return - 6;
			}
		}
		return 1;
	}

	/**
	 * 将文章加入收藏夹
	 *
	 * @param int $cid
	 *        	文章id
	 * @param int $userid
	 *        	会员id
	 * @param string $title
	 *        	文章标题
	 * @param $mix {-1:加入失败;$id:加入成功，返回收藏id}
	 */
	public function add_favorite($cid, $userid, $title) {
		$cid = intval ( $cid );
		$userid = intval ( $userid );
		$title = safe_replace ( $title );
		$this->favorite_db = Loader::model ( 'favorite_model' );
		$id = $this->favorite_db->insert ( array ('title' => $title,'userid' => $userid,'cid' => $cid,'adddate' => TIME ), 1 );
		if ($id) {
			return $id;
		} else {
			return - 1;
		}
	}

	/**
	 * 根据uid增加用户积分
	 *
	 * @param int $userid
	 * @param int $point
	 * @return boolean
	 */
	public function add_point($userid, $point) {
		$point = intval ( $point );
		return $this->db->where ( array ('userid' => $userid ) )->update ( array ('point' => "+=$point" ) );
	}

	/**
	 * 根据积分算出用户组
	 *
	 * @param $point int 积分数
	 */
	public function _get_usergroup_bypoint($point = 0) {
		$groupid = 4;
		if (empty ( $point )) {
			$member_setting = S ( 'member/setting' );
			$point = isset ( $member_setting ['defualtpoint'] ) && ! empty ( $member_setting ['defualtpoint'] ) ? $member_setting ['defualtpoint'] : 0;
		}
		$grouplist = S ( 'member/grouplist' );
		foreach ( $grouplist as $k => $v ) {
			$grouppointlist [$k] = $v ['point'];
		}
		arsort ( $grouppointlist );
		if ($point > max ( $grouppointlist )) { // 如果超出用户组积分设置则为积分最高的用户组
			$groupid = key ( $grouppointlist );
		} else {
			$tmp_k = 0;
			foreach ( $grouppointlist as $k => $v ) {
				if ($point >= $v) {
					$groupid = $tmp_k;
					break;
				}
				$tmp_k = $k;
			}
		}
		return $groupid;
	}

	/**
	 * 判断帐户是否被禁止
	 *
	 * @param string $username
	 * @return boolean
	 */
	private function check_denyusername($username) {
		$member_setting = S ( 'member/member_setting' );
		// 判断是否禁止
		$denyusername = $member_setting ['denyusername'];
		if (is_array ( $denyusername )) {
			$denyusername = implode ( "|", $denyusername );
			$pattern = '/^(' . str_replace ( array ('\\*',' ',"\|" ), array ('.*','','|' ), preg_quote ( $denyusername, '/' ) ) . ')$/i';
			if (preg_match ( $pattern, $username )) return false;
		}
		return true;
	}

	/**
	 * 判断邮箱是否被禁止
	 */
	private function check_denyemail($email) {
		$member_setting = S ( 'member/member_setting' );
		$denyemail = $member_setting ['denyemail']; // 是否禁止
		if (is_array ( $denyemail )) {
			$denyemail = implode ( "|", $denyemail );
			$pattern = '/^(' . str_replace ( array ('\\*',' ',"\|" ), array ('.*','','|' ), preg_quote ( $denyemail, '/' ) ) . ')$/i';
			if (preg_match ( $pattern, $email )) return false;
		}
		return true;
	}
}
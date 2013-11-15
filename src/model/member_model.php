<?php
/**
 * 会员主表操作模型
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: member_model.php 715 2013-08-02 07:52:22Z 85825770@qq.com $
 */
if (! defined ( 'CACHE_MODEL_PATH' )) define ( 'CACHE_MODEL_PATH', DATA_PATH . 'model' . DIRECTORY_SEPARATOR );
class member_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'member';
		parent::__construct ();
		$this->member_setting = S ( 'member/member_setting' );
		$this->auto_check_fields = false;
	}

	/**
	 * 获取主表用户信息
	 *
	 * @param string $username 用户名
	 * @param bool $field 字段
	 */
	public function get_user($username = null, $field = 'userid') {
		if (is_null ( $username )) return false;
		$memberinfo = $this->where ( array ($field => $username ) )->find();
		if (! $memberinfo) return false;
		// 获取用户模型信息
		$this->set_model ( $memberinfo ['modelid'] );
		$member_modelinfo = $this->getby_userid ( $memberinfo ['userid'] );
		$this->set_model ();
		if (is_array ( $memberinfo )) {
			$memberinfo = array_merge ( $memberinfo, $member_modelinfo );
		}
		return $memberinfo;
	}

	/**
	 * 用户登陆
	 *
	 * @param string $username 用户名或邮箱
	 * @param string $password 密码
	 * @return array userid 大于 0:返回用户 ID，表示用户登录成功 -1:用户不存在，或者被删除 -2:密码错
	 *         -3:安全提问错 -4 用户被锁定
	 */
	public function login($username, $password) {
		$field = strpos ( $username, '@' ) ? 'email' : 'username'; // 判断是否是邮箱
		$res = $this->get_user ( $username, $field );
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

		if (ucenter_exists ()) { // UCenter登录
			$ucuid = Loader::lib ( 'member:uc_client' )->uc_user_login ( $username, $password );
			if ($ucuid < 0) return array ('userid' => $ucuid );
			$res ['synloginstr'] = Loader::lib ( 'member:uc_client' )->uc_user_synlogin ( $ucuid );
		}

		$updatearr = array ('lastip' => IP,'lastdate' => TIME );
		// 检查用户积分，更新新用户组，除去邮箱认证、禁止访问、游客组用户、vip用户
		if ($res ['point'] >= 0 && ! in_array ( $res ['groupid'], array ('1','2','3' ) ) && empty ( $res ['vip'] )) {
			$check_groupid = $this->_get_usergroup_bypoint ( $res ['point'] );
			if ($check_groupid != $res ['groupid']) {
				$updatearr ['groupid'] = $groupid = $check_groupid;
			}
		}
		$this->update ( $updatearr, array ('userid' => $res ['userid'] ) );
		return $res;
	}

	/**
	 * 重置模型操作表表
	 *
	 * @param string $modelid 模型id
	 */
	public function set_model($modelid = '') {
		if ($modelid) {
			$model = S ( 'common/member_model' );
			$this->table_name = $this->prefix . $model [$modelid] ['tablename'];
			$this->fields_bak = $this->fields;
			$this->fields = null;
		} else {
			if (is_null ( $this->fields )) $this->fields = $this->fields_bak;
			$this->table_name = $this->prefix . 'member';
		}
	}

	/**
	 * 获取会员账户余额
	 */
	public function get_amount($userid){
		$info = $this->field('amount')->where(array('userid'=>$userid))->find();
		return $info['amount'];
	}

	/**
	 * 锁定会员
	 *
	 * @param array $uidarr
	 */
	public function lock($uidarr) {
		$userids = implode(',', $uidarr);
		return $this->where(array('userid'=>array('in',$userids)))->update ( array ('islock' => 1 ));
	}

	/**
	 * 解除锁定会员
	 */
	public function unlock($uidarr) {
		$userids = implode(',', $uidarr);
		return $this->where(array('userid'=>array('in',$userids)))->update ( array ('islock' => 0 ));
	}
}
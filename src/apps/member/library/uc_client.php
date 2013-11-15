<?php
/**
 * Ucenter客户端封装
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: uc_client.php 677 2013-07-30 03:43:44Z 85825770@qq.com $
 */
class uc_client {
	/**
	 * 析构函数
	 */
	public function __construct() {
		C ( 'uc_config' );
		require_once FW_PATH . 'uc_client/client.php';
	}
	/**
	 * 检查用户是否可以注册
	 *
	 * @param string $username
	 * @return int {-1：用户名不合法;-2:包含不允许注册的词语 ;-3:用户名已经存在 ;1:成功}
	 */
	public function uc_checkname($username) {
		$ucresult = uc_user_checkname ( $username );
		return $ucresult;
	}
	/**
	 * 检查邮箱是否可以注册
	 *
	 * @param string $email
	 * @return int {-4:Email 格式有误 ;-5:Email 不允许注册;-6:该 Email 已经被注册;1:成功}
	 */
	public function uc_checkemail($email) {
		$ucresult = uc_user_checkemail ( $email );
		return $ucresult;
	}
	/**
	 * 向UCenter中注册用户
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $encrypt
	 * @return int {-1:用户名不合法
	 *         ;-2:用户名包含不允许注册的词语;-3:用户名已存在;-4:E-mail不合法;-5:E-mail不允许注册;-6:该
	 *         Email 已经被注册;<0:成功返回Uid}
	 */
	public function uc_user_register($username, $password, $email, $regip = '', $questionid = '', $answer = '') {
		return uc_user_register ( $username, $password, $email, $questionid, $answer, $regip);
	}
	/**
	 * UCenter同步登录
	 *
	 * @param int $ucuserid
	 */
	public function uc_user_synlogin($ucuserid) {
		return uc_user_synlogin ();
	}
	/**
	 * UCenter同步退出
	 */
	public function uc_user_synlogout() {
		return uc_user_synlogout ();
	}
	/**
	 * UCenter用户登录
	 *
	 * @param string $username
	 * @param string $password
	 * @return Array
	 */
	public function uc_user_login($username, $password) {
		list ( $uc ['uid'], $uc ['username'], $uc ['password'], $uc ['email'] ) = uc_user_login ( $username, $password, 0 );
		return $uc;
	}
	/**
	 * 获取UCenter用户资料
	 *
	 * @param string $username
	 * @param bool $is_uid
	 */
	public function uc_get_userinfo($username, $is_uid = false) {
		$uc_db = Loader::model ( 'uc_model' );
		if ($is_uid)
			$where = array ('uid' => $username );
		else
			$where = array ('username' => $username );
		$memberinfo = $uc_db->where ( $where )->find ();
		return $memberinfo;
	}
	/**
	 * 获取UCenter用户简单资料
	 *
	 * @param string $username 用户名
	 * @param bool $is_uid 是否使用用户 ID获取
	 */
	public function uc_get_user($username, $is_uid = false) {
		return uc_get_user ( $username, $is_uid );
	}
	/**
	 * 获取UCenter用户头像
	 *
	 * @param int $uid
	 */
	public function uc_get_avatar($uid) {
		$url = UC_API . "/avatar.php?uid=$uid&size=";
		$avatar = array ('180' => $url . 'big','90' => $url . 'middle','45' => $url . 'small','30' => $url . 'small' );
		return $avatar;
	}
	/**
	 * 获取UCenter设置用户头像代码
	 *
	 * @param int $uid
	 */
	public function uc_avatar($uid) {
		return uc_avatar ( $uid );
	}
	/**
	 * 更新用户资料
	 * @param string $username 用户名
	 * @param string $oldpassword 旧密码
	 * @param string $newpassword 新密码，如不修改为空
	 * @param string $emailnew Email，如不修改为空
	 * @param bool $ignoreoldpw 是否忽略旧密码 1:忽略，更改资料不需要验证密码 0:(默认值) 不忽略，更改资料需要验证密码
	 * @return {1:更新成功;0:没有做任何修改;-1:旧密码不正确 ;-4:Email 格式有误;-5:Email 不允许注册;-6:该
	 *         Email 已经被注册;-7:没有做任何修改;-8:该用户受保护无权限更改;}
	 */
	public function uc_user_edit($username, $oldpassword, $newpassword, $emailnew, $ignoreoldpw = 0) {
		return uc_user_edit ( $username, $oldpassword, $newpassword, $emailnew, $ignoreoldpw );
	}
	/**
	 * 删除用户头像
	 *
	 * @param string/array $username 用户名
	 */
	public function uc_user_deleteavatar($uid) {
		uc_user_deleteavatar ( $uid );
		return 1;
	}
	/**
	 * UCenter用户删除
	 *
	 * @param string/array $username 用户名
	 * @return integer 1:成功 0:失败
	 */
	public function uc_user_delete($uid) {
		return uc_user_delete ( $uid );
	}
	/**
	 * 积分兑换请求
	 *
	 * @param integer $uid 用户 ID
	 * @param integer $from 原积分
	 * @param integer $to 目标积分
	 * @param integer $toappid 目标应用ID
	 * @param integer $amount 积分数额
	 * @return bool 1:请求成功 0:请求失败
	 */
	public function uc_credit_exchange_request($uid, $from, $to, $toappid, $amount) {
		return uc_credit_exchange_request ( $uid, $from, $to, $toappid, $amount );
	}
	/**
	 * 获取应用列表
	 */
	public function uc_app_ls() {
		return uc_app_ls ();
	}
	/**
	 * 添加邮件到队列
	 *
	 * @param string $uids 用户 ID 多个用逗号(,)隔开
	 * @param string $emails 目标email，多个用逗号(,)隔开
	 * @param string $subject 邮件标题
	 * @param string $message 邮件内容
	 * @param mail $frommail 发信人，可选参数，默认为空，uc后台设置的邮件来源作为发信人地址
	 * @param string $charset 邮件字符集，可选参数，默认为gbk
	 * @param boolean $htmlon 是否是html格式的邮件，可选参数，默认为FALSE，即文本邮件
	 * @param integer $level 邮件级别，可选参数，默认为1，数字大的优先发送，取值为0的时候立即发送，邮件不入队列
	 * @return mixed integer:成功：进入队列的邮件的id，当level为0，则返回1 false:失败：进入队列失败，或者发送失败
	 */
	public function uc_mail_queue($uids, $emails, $subject, $message, $frommail, $charset, $htmlon, $level) {
		return uc_mail_queue ( $uids, $emails, $subject, $message, $frommail, $charset, $htmlon, $level );
	}
	/**
	 * 发送短消息
	 *
	 * @param integer $fromuid 发件人用户 ID，0 为系统消息
	 * @param string $msgto 收件人用户名，多个用逗号分割
	 * @param string $subject 消息标题
	 * @param string $message 消息内容
	 * @param integer $replypmid 回复的消息 ID 大于 0:回复指定的短消息 0:(默认值) 发送新的短消息
	 * @return integer {大于 0:发送成功的最后一条消息 ID 0:发送失败 -1:超出了24小时最大允许发送短消息数目
	 *         -2:不满足两次发送短消息最短间隔 -3:不能给非好友批量发送短消息
	 *         -4:目前还不能使用发送短消息功能（注册多少日后才可以使用发短消息限制）}
	 */
	public function uc_pm_send($fromuid, $msgto, $subject, $message, $replypmid) {
		return uc_pm_send ( $fromuid, $msgto, $subject, $message, $instantly = 1, $replypmid = 0, $isusername = 1 );
	}
}
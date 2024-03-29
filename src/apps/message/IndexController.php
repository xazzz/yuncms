<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::lib ( 'member:foreground' ); // 加载foreground 应用类. 自动判断是否登陆.
/**
 * 短消息前台
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-7
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: IndexController.php 673 2013-07-30 03:31:31Z 85825770@qq.com $
 */
class IndexController extends foreground {
	public function __construct() {
		parent::__construct ();
		$this->message_db = Loader::model ( 'message_model' );
		$this->message_group_db = Loader::model ( 'message_group_model' );
		$this->message_data_db = Loader::model ( 'message_data_model' );
		$this->_username = cookie ( '_username' );
		$this->_userid = cookie ( '_userid' );
		$this->_groupid = get_memberinfo ( $this->_userid, 'groupid' );
	}
	public function init() {
		$page = isset ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$where = array ('send_to_id' => $this->_username,'replyid' => '0' );
		$infos = $this->message_db->where ( $where )->order ( 'messageid DESC' )->listinfo ( $page, 10 );
		$infos = String::htmlspecialchars ( $infos );
		$pages = $this->message_db->pages;
		include template ( 'message', 'inbox' );
	}

	/**
	 * 发送消息
	 */
	public function send() {
		if (isset ( $_POST ['dosubmit'] )) {
			$username = $this->_username;
			$tousername = $_POST ['info'] ['send_to_id'];
			$subject = String::htmlspecialchars ( $_POST ['info'] ['subject'] );
			$content = String::htmlspecialchars ( $_POST ['info'] ['content'] );
			$this->message_db->add_message ( $tousername, $username, $subject, $content, true );
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			// 判断当前会员，是否可发，短消息．
			$this->message_db->messagecheck ( $this->_userid );
			$show_validator = $show_scroll = $show_header = true;
			include template ( 'message', 'send' );
		}
	}

	/**
	 * 判断收件人是否存在
	 */
	public function public_name() {
		$username = isset ( $_GET ['username'] ) && trim ( $_GET ['username'] ) ? (CHARSET == 'gbk' ? iconv ( 'utf-8', 'gbk', trim ( $_GET ['username'] ) ) : trim ( $_GET ['username'] )) : exit ( '0' );
		$member_interface = Loader::lib ( 'member:member_interface' );
		if ($username) {
			$username = safe_replace ( $username );
			// 判断收件人不能为自己
			if ($username == $this->_username) {
				exit ( '0' );
			}
			$data = $member_interface->get_member_info ( $username, 2 );
			if ($data != '-1') {
				exit ( '1' );
			} else {
				exit ( '0' );
			}
		} else {
			exit ( '0' );
		}
	}

	/**
	 * 发件箱
	 */
	public function outbox() {
		$where = array ('send_from_id' => $this->_username,'del_type' => '0' );
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->message_db->where ( $where )->order ( 'messageid DESC' )->listinfo ( $page, 8 );
		$infos = String::htmlspecialchars ( $infos );
		$pages = $this->message_db->pages;
		include template ( 'message', 'outbox' );
	}

	/**
	 * 收件箱
	 */
	public function inbox() {
		$where = array ('send_to_id' => $this->_username,'folder' => 'inbox' );
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->message_db->where ( $where )->order ( 'messageid DESC' )->listinfo ( $page, 8 );
		$infos = String::htmlspecialchars ( $infos );
		if (is_array ( $infos ) && ! empty ( $infos )) {
			foreach ( $infos as $infoid => $info ) {
				$reply_num = $this->message_db->where ( array ("replyid" => $info ['messageid'] ) )->count ();
				$infos [$infoid] ['reply_num'] = $reply_num;
			}
		}
		$pages = $this->message_db->pages;
		include template ( 'message', 'inbox' );
	}

	/**
	 * 群发邮件
	 */
	public function group() {
		// 查询自己有权限看的消息
		$where = array ('typeid' => 1,'groupid' => $this->_groupid,'status' => 1 );
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->message_group_db->where ( $where )->order ( 'id DESC' )->listinfo ( $page, 8 );
		$infos = String::htmlspecialchars ( $infos );
		$status = array ();
		if (is_array ( $infos ) && ! empty ( $infos )) {
			foreach ( $infos as $info ) {
				$d = $this->message_data_db->where ( array ('userid' => $this->_userid,'group_message_id' => $info ['id'] ) )->select ();
				if (! $d) {
					$status [$info ['id']] = 0; // 未读 红色
				} else {
					$status [$info ['id']] = 1;
				}
			}
		}
		$pages = $this->message_group_db->pages;
		include template ( 'message', 'group' );
	}

	/**
	 * 删除收件箱-短消息
	 *
	 * @param intval $sid
	 */
	public function delete() {
		if ((! isset ( $_GET ['messageid'] ) || empty ( $_GET ['messageid'] )) && (! isset ( $_POST ['messageid'] ) || empty ( $_POST ['messageid'] ))) {
			showmessage ( L ( 'illegal_parameters' ), HTTP_REFERER );
		} else {
			if (is_array ( $_POST ['messageid'] )) {
				foreach ( $_POST ['messageid'] as $messageid_arr ) {
					$this->message_db->where ( array ('messageid' => $messageid_arr,'send_to_id' => $this->_username ) )->update ( array ('folder' => 'outbox' ) );
				}
				showmessage ( L ( 'operation_success' ), HTTP_REFERER );
			}
		}
	}

	/**
	 * 删除发件箱 - 短消息
	 *
	 * @param intval $sid
	 *        	=1 )
	 */
	public function del_type() {
		if ((! isset ( $_POST ['messageid'] ) || empty ( $_POST ['messageid'] ))) {
			showmessage ( L ( 'illegal_parameters' ), HTTP_REFERER );
		} else {
			if (is_array ( $_POST ['messageid'] )) {
				foreach ( $_POST ['messageid'] as $messageid_arr ) {
					$this->message_db->where ( array ('messageid' => $messageid_arr,'send_from_id' => $this->_username ) )->update ( array ('del_type' => '1' ) );
				}
				showmessage ( L ( 'operation_success' ), HTTP_REFERER );
			}
		}
	}

	/**
	 * 查看短消息
	 */
	public function read() {
		if ((! isset ( $_GET ['messageid'] ) || empty ( $_GET ['messageid'] )) && (! isset ( $_POST ['messageid'] ) || empty ( $_POST ['messageid'] ))) return false;
		// 查看过修改状态 为 0
		$this->message_db->where ( array ('messageid' => $_GET ['messageid'] ) )->update ( array ('status' => '0' ) );
		// 查询消息详情
		$infos = $this->message_db->getby_messageid ( $_GET ['messageid'] );
		if ($infos ['send_from_id'] != 'SYSTEM') $infos = String::htmlspecialchars ( $infos );
		// 查询回复消息
		$where = array ('replyid' => $infos ['messageid'] );
		$reply_infos = $this->message_db->where ( $where )->order ( 'messageid ASC' )->listinfo ( 1, 10 );
		$show_validator = $show_scroll = $show_header = true;
		include template ( 'message', 'read' );
	}

	/**
	 * 查看自己发的短消息
	 */
	public function read_only() {
		if ((! isset ( $_GET ['messageid'] ) || empty ( $_GET ['messageid'] )) && (! isset ( $_POST ['messageid'] ) || empty ( $_POST ['messageid'] ))) return false;
		// 查询消息详情
		$infos = $this->message_db->getby_messageid ( $_GET ['messageid'] );
		$infos = String::htmlspecialchars ( $infos );
		// 查询回复消息
		$where = array ('replyid' => $infos ['messageid'] );
		$reply_infos = $this->message_db->where ( $where )->order ( 'messageid ASC' )->listinfo ( 1, 10 );
		$show_validator = $show_scroll = $show_header = true;
		include template ( 'message', 'read_only' );
	}

	/**
	 * 查看系统短消息
	 */
	public function read_group() {
		if ((! isset ( $_GET ['group_id'] ) || empty ( $_GET ['group_id'] )) && (! isset ( $_POST ['group_id'] ) || empty ( $_POST ['group_id'] ))) return false;
		// 查询消息详情
		$infos = $this->message_group_db->getby_id ( $_GET ['group_id'] );
		$infos = String::htmlspecialchars ( $infos );
		if (! is_array ( $infos )) showmessage ( L ( 'message_not_exist' ), 'blank' );
		// 检查查看表是否有记录,无则向message_data 插入浏览记录
		$check = $this->message_data_db->where ( array ('userid' => $this->_userid,'group_message_id' => $_GET ['group_id'] ) )->select ();
		if (! $check) {
			$this->message_data_db->insert ( array ('userid' => $this->_userid,'group_message_id' => $_GET ['group_id'] ) );
		}
		include template ( 'message', 'read_group' );
	}

	/**
	 * 回复短消息
	 */
	public function reply() {
		if (isset ( $_POST ['dosubmit'] )) { // 判断当前会员，是否可发，短消息．
			$this->message_db->messagecheck ( $this->_userid );
			$_POST ['info'] ['send_from_id'] = $this->_username;
			$_POST ['info'] ['message_time'] = TIME;
			$_POST ['info'] ['status'] = '1';
			$_POST ['info'] ['folder'] = 'inbox';
			if (empty ( $_POST ['info'] ['send_to_id'] )) showmessage ( L ( 'user_noempty' ), HTTP_REFERER );
			$messageid = $this->message_db->insert ( $_POST ['info'], true );
			if (! $messageid) return FALSE;
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$show_validator = $show_scroll = $show_header = true;
			include template ( 'message', 'send' );
		}
	}
}
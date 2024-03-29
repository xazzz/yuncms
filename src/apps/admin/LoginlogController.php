<?php
/**
 * 后台登陆日志
 * @author Tongle Xu <xutongle@gmail.com> 2013-3-28
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: LoginlogController.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );

class LoginlogController extends Web_Admin {
	private $db, $role_db;

	public function __construct() {
		parent::__construct ();
		$this->db = Loader::model ( 'admin_login_log_model' );
		$this->admin_username = cookie ( 'admin_username' ); // 管理员COOKIE
	}

	/**
	 * 后台日志管理
	 */
	public function init() {
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->db->order ( 'logid DESC' )->listinfo ( $page, 20 );
		$pages = $this->db->pages;
		include $this->view ( 'login_log_list' );
	}

	/**
	 * 操作日志删除 包含批量删除 单个删除
	 */
	public function delete() {
		$week = intval ( $_GET ['week'] );
		if ($week) {
			$start = TIME - $week * 604800;
			$d = date ( "Y-m-d", $start );
			$this->db->where ( array ('time' => array ('elt',$d ) ) )->delete ();
			showmessage ( L ( 'operation_success' ), U ( 'admin/loginlog/init' ) );
		} else {
			return false;
		}
	}

	/**
	 * 日志搜索
	 */
	public function search_log() {
		$where = array ();
		if (isset ( $_GET ['search'] ['username'] ) && ! empty ( $_GET ['search'] ['username'] )) $where ['username'] = trim ( $_GET ['search'] ['username'] );
		if (isset ( $_GET ['search'] ['start_time'] ) && ! empty ( $_GET ['search'] ['start_time'] )) $where ['start_time'] = array ('egt',trim ( $_GET ['search'] ['start_time'] ) );
		if (isset ( $_GET ['search'] ['end_time'] ) && ! empty ( $_GET ['search'] ['end_time'] )) $where ['end_time'] = array ('elt',trim ( $_GET ['search'] ['end_time'] ) );
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->db->order ( 'logid DESC' )->where ( $where )->listinfo ( $page, 13 );
		$pages = $this->db->pages;
		include $this->view ( 'loginlog_search_list' );
	}
}
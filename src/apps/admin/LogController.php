<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: LogController.php 635 2013-07-29 07:36:44Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );

class LogController extends Web_Admin {

	public $db;
	public function __construct() {
		parent::__construct ();
		$this->db = Loader::model ( 'admin_log_model' );
		$this->admin_username = cookie ( 'admin_username' ); // 管理员COOKIE
	}

	/**
	 * 后台日志管理
	 */
	public function init() {
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->db->order ( 'logid DESC' )->listinfo ( $page, 13 );
		$pages = $this->db->pages;
		$application_arr = array ();
		$applications = S ( 'common/application' );
		foreach ( $applications as $application => $app ) {
			$application_arr [$app ['application']] = $app ['application'];
		}
		include $this->view ( 'log_list' );
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
			showmessage ( L ( 'operation_success' ), U ( 'admin/log/init' ) );
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
		if (isset ( $_GET ['search'] ['application'] ) && ! empty ( $_GET ['search'] ['application'] )) $where ['application'] = trim ( $_GET ['search'] ['application'] );
		if (isset ( $_GET ['search'] ['start_time'] ) && ! empty ( $_GET ['search'] ['start_time'] )) $where ['start_time'] = array ('egt',trim ( $_GET ['search'] ['start_time'] ) );
		if (isset ( $_GET ['search'] ['end_time'] ) && ! empty ( $_GET ['search'] ['end_time'] )) $where ['end_time'] = array ('elt',trim ( $_GET ['search'] ['end_time'] ) );
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->db->field ( 'logid DESC' )->where ( $where )->listinfo ( $page, 13 );
		$pages = $this->db->pages;
		// 模块数组
		$application_arr = array ();
		$applications = S ( 'common/application' );
		$default = isset ( $_GET ['search'] ['application'] ) && ! empty ( $_GET ['search'] ['application'] ) ? trim ( $_GET ['search'] ['application'] ) : L ( 'open_application' );
		foreach ( $applications as $application => $app )
			$application_arr [$app ['application']] = $app ['application'];
		include $this->view ( 'log_search_list' );
	}
}
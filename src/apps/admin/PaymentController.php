<?php
/**
 * 支付方式管理
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: PaymentController.php 708 2013-08-01 03:56:02Z 85825770@qq.com
 *          $
 */
class PaymentController extends Web_Admin {
	public $db, $modules_path;

	/**
	 * 构造方法
	 */
	public function __construct() {
		parent::__construct ();
		$this->modules_path = PLUGIN_PATH . 'payment' . DIRECTORY_SEPARATOR;
		$this->db = Loader::model ( 'payment_cfg_model' );
	}

	/**
	 * 支付插件列表
	 */
	public function init() {
		$payments = $this->get_payment ();
		$install = $this->get_intallpayment ();
		if (is_array ( $payments )) {
			foreach ( $payments as $code => $payment ) {
				if (isset ( $install [$code] )) {
					$install [$code] ['pay_desc'] = $payments [$code] ['pay_desc'];
					unset ( $payments [$code] );
				}
			}
		}
		$all = @array_merge ( $install, $payments );
		$infos = array (
						'data' => $all,
						array (
							'all' => count ( $all ),
							'install' => count ( $install )
						)
		);

		// $infos = $this->method->get_list ();
		$show_dialog = true;
		include $this->view ( 'payment_list' );
	}

	/**
	 * 安装支付插件
	 */
	public function add() {
		if (isset ( $_POST ['dosubmit'] )) {
			$info = $this->submit ();
			$this->db->insert ( $info );
			if ($this->db->insert_id ()) {
				showmessage ( L ( 'operation_success' ), '', '', 'add' );
			}
		} else {
			$infos = $this->get_payment ( $_GET ['code'] );
			extract ( $infos );
			$show_header = true;
			$show_validator = true;
			include $this->view ( 'payment_detail' );
		}
	}

	/**
	 * 编辑支付模块
	 */
	public function edit() {
		if (isset ( $_POST ['dosubmit'] )) {
			$info = $this->submit ();
			$infos = $this->db->where ( array (
												'id' => $info ['id']
			) )->update ( $info );
			showmessage ( L ( 'edit' ) . L ( 'succ' ), '', '', 'edit' );
		} else {
			/* 查询该支付方式内容 */
			if (isset ( $_GET ['code'] )) {
				$_GET ['code'] = trim ( $_GET ['code'] );
			} else {
				die ( 'invalid parameter' );
			}
			$infos = $this->db->where ( array (
												'pay_code' => $_GET ['code']
			) )->find ();
			/* 取相应插件信息 */
			$data = $this->get_payment ( $_GET ['code'] );
			/* 取得配置信息 */
			if (is_string ( $infos ['config'] )) {
				$store = unserialize ( $infos ['config'] );
				$infos ['config'] = array ();
				/* 循环插件中所有属性 */
				foreach ( $data ['config'] as $key => $value ) {
					$infos ['config'] [$key] = $value;
					$infos ['config'] [$key] ['value'] = $store [$key];
				}
			}
			/* 如果以前没设置支付费用，编辑时补上 */
			if (! isset ( $infos ['pay_fee'] )) {
				if (isset ( $data ['pay_fee'] )) {
					$infos ['pay_fee'] = $data ['pay_fee'];
				} else {
					$infos ['pay_fee'] = 0;
				}
			}
			extract ( $infos );
			$show_header = true;
			$show_validator = true;
			include $this->view ( 'payment_detail' );
		}
	}

	/**
	 * 卸载支付模块
	 */
	public function delete() {
		$id = intval ( $_GET ['id'] );
		$this->db->where ( array (
									'id' => $id
		) )->delete ();
		showmessage ( L ( 'delete_succ' ), '?app=admin&controller=payment&menuid=158' );
	}

	/**
	 * 处理提交
	 */
	public function submit() {
		/* 检查输入 */
		if (empty ( $_POST ['pay_name'] )) {
			showmessage ( L ( 'payment_name' ) . L ( 'empty' ), '', '', 'add' );
		}
		$infos = $this->get_payment ( $_POST ['pay_code'] );
		$info = array ();
		$info ['config'] = isset ( $_POST ['config'] ) ? serialize ( $_POST ['config'] ) : '';
		$info ['name'] = trim ( $_POST ['name'] );
		$info ['pay_name'] = trim ( $_POST ['pay_name'] );
		$info ['pay_desc'] = trim ( $_POST ['description'] );
		$info ['id'] = $_POST ['id'];
		$info ['pay_code'] = trim ( $_POST ['pay_code'] );
		$info ['pay_order'] = intval ( $_POST ['pay_order'] );
		$info ['pay_method'] = intval ( $_POST ['pay_method'] );
		/* 取得和验证支付手续费 */
		$info ['pay_fee'] = (intval ( $_POST ['pay_method'] ) == 0) ? intval ( $_POST ['pay_rate'] ) : intval ( $_POST ['pay_fix'] );
		$info ['is_cod'] = trim ( $_POST ['is_cod'] );
		$info ['is_online'] = trim ( $_POST ['is_online'] );
		$info ['enabled'] = '1';
		$info ['author'] = $infos ['author'];
		$info ['website'] = $infos ['website'];
		$info ['version'] = $infos ['version'];
		return $info;
	}

	/**
	 * 取得数据库中的支付列表
	 *
	 * @param string $code
	 */
	private function get_intallpayment($code = '') {
		if (empty ( $code )) {
			$intallpayment = array ();
			$result = $this->db->select ();
			foreach ( $result as $r ) {
				$r ['pay_code'] = $r ['pay_code'];
				$intallpayment [$r ['pay_code']] = $r;
			}
			return $intallpayment;
		} else {
			return $this->db->where ( array (
											'pay_code' => $code
			) )->find ();
		}
	}

	/**
	 * 取得插件目录信息
	 */
	private function get_payment($code = '') {
		$payments = $this->read_payment ( $this->modules_path );
		$payment_info = array ();
		foreach ( $payments as $payment ) {
			if (! isset ( $payment ['pay_fee'] )) {
				$payment ['pay_fee'] = 0;
			}
			$payment_info [$payment ['code']] = array (
													"id" => 0,
													"pay_code" => $payment ['code'],
													"pay_name" => $payment ['name'],
													"pay_desc" => $payment ['desc'],
													"pay_fee" => $payment ['pay_fee'],
													"config" => $payment ['config'],
													"is_cod" => $payment ['is_cod'],
													"is_online" => $payment ['is_online'],
													"enabled" => '0',
													"sort_order" => "",
													"author" => $payment ['author'],
													"website" => $payment ['website'],
													"version" => $payment ['version']
			);
		}
		if (empty ( $code )) {
			return $payment_info;
		} else {
			return $payment_info [$code];
		}
	}

	/**
	 * 读取插件目录中插件列表
	 */
	public function read_payment($directory = ".") {
		$dirs_arr = array ();
		$dirs = glob ( $directory . '*' );
		foreach ( $dirs as $d ) {
			if (is_dir ( $d )) {
				$d = basename ( $d );
				if (file_exists ( $directory . $d . DIRECTORY_SEPARATOR . 'config.inc.php' )) {
					$dirs_arr [$d] = require $directory . $d . DIRECTORY_SEPARATOR . 'config.inc.php';
				}
			}
		}
		return $dirs_arr;
	}
}
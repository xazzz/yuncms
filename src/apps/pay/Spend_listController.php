<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::lib ( 'member:foreground', false );
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: Spend_listController.php 713 2013-08-02 02:20:33Z 85825770@qq.com $
 */
class Spend_listController extends foreground {
	private $spend_db;
	function __construct() {
		if (! application_exists ( APP )) showmessage ( L ( 'application_not_exists' ) );
		$this->spend_db = Loader::model ( 'pay_spend_model' );
		parent::__construct ();
	}
	public function init() {
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$userid = cookie ( '_userid' );
		$where = array ('userid' => $userid );
		if (isset ( $_GET ['dosubmit'] )) {
			$type = isset ( $_GET ['type'] ) && intval ( $_GET ['type'] ) ? intval ( $_GET ['type'] ) : '';
			$endtime = isset ( $_GET ['endtime'] ) && trim ( $_GET ['endtime'] ) ? strtotime ( trim ( $_GET ['endtime'] ) ) : '';
			$starttime = isset ( $_GET ['starttime'] ) && trim ( $_GET ['starttime'] ) ? strtotime ( trim ( $_GET ['starttime'] ) ) : '';
			if (! empty ( $starttime ) && empty ( $endtime )) {
				$endtime = TIME;
			}
			if (! empty ( $starttime ) && ! empty ( $endtime ) && $endtime < $starttime) {
				showmessage ( L ( 'wrong_time_over_time_to_time_less_than' ) );
			}
			if (! empty ( $starttime )) {
				$where ['creat_at'] = array ('between',$starttime . ',' . $endtime );
			}
			if (! empty ( $type )) {
				$where ['type'] = $type;
			}
		}
		$list = $this->spend_db->where ( $where )->order ( 'id desc' )->listinfo ( $page );
		$pages = $this->spend_db->pages;
		include template ( 'pay', 'spend_list' );
	}
}
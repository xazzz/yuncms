<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::session();
// 权限判断，根据栏目里面的权限设置检查
if ((isset ( $_GET ['catid'] ) || isset ( $_POST ['catid'] )) && $_SESSION ['roleid'] != 1) {
	$catid = isset ( $_GET ['catid'] ) ? intval ( $_GET ['catid'] ) : intval ( $_POST ['catid'] );
	$this->priv_db = Loader::model ( 'category_priv_model' );
	$priv_datas = $this->priv_db->where ( array ('catid' => $catid,'is_admin' => 1,'action' => 'push' ) )->find();
	if (! $priv_datas ['catid']) showmessage ( L ( 'permission_to_operate' ), 'blank' );
}
/**
 * 推送信息
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-12
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: PushController.php 740 2013-08-13 02:18:43Z 85825770@qq.com $
 */
class PushController extends Web_Admin {
	public function __construct() {
		parent::__construct ();
		$application = (isset ( $_GET ['application'] ) && ! empty ( $_GET ['application'] )) ? $_GET ['application'] : 'admin';
		if (in_array ( $application, array ('admin','special','content' ) )) {
			$this->push = PushFactory::get_instance ()->get_api ( $application );
		} else {
			showmessage ( L ( 'not_exists_push' ), 'blank' );
		}
	}

	/**
	 * 推送选择界面
	 */
	public function init() {
		if (isset ( $_POST ['dosubmit'] )) {
			$c = Loader::model ( 'content_model' );
			$c->set_model ( $_POST ['modelid'] );
			$info = array ();
			$ids = explode ( '|', $_POST ['id'] );
			if (is_array ( $ids )) {
				foreach ( $ids as $id ) {
					$info [$id] = $c->get_content ( $_POST ['catid'], $id );
				}
			}
			$_GET ['add_action'] = isset ( $_GET ['add_action'] ) ? $_GET ['add_action'] : $_GET ['act'];
			$this->push->$_GET ['add_action'] ( $info, $_POST );
			showmessage ( L ( 'success' ), '', '', 'push' );
		} else {
			Loader::helper ( 'template:global' );
			if (method_exists ( $this->push, $_GET ['act'] )) {
				$html = $this->push->{$_GET ['act']} ( array ('modelid' => $_GET ['modelid'],'catid' => $_GET ['catid'] ) );
				$tpl = isset ( $_GET ['tpl'] ) ? 'push_to_category' : 'push_list';
				include $this->view ( $tpl );
			} else {
				showmessage ( 'CLASS METHOD NO EXISTS!', 'blank' );
			}
		}
	}
	public function public_ajax_get() {
		if (method_exists ( $this->push, $_GET ['action'] )) {
			$html = $this->push->{$_GET ['action']} ( $_GET ['html'] );
			echo $html;
		} else {
			echo 'CLASS METHOD NO EXISTS!';
		}
	}
}
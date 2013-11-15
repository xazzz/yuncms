<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-13
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: comment_api.php 660 2013-07-30 02:03:49Z 85825770@qq.com $
 */
if (! app_exists ( 'comment' )) showmessage ( L ( 'module_not_exists' ) );
class comment_api {
	private $db;
	function __construct() {
		$this->db = Loader::model ( 'special_model' );
	}

	/**
	 * 获取评论信息
	 *
	 * @param $module 模型
	 * @param $contentid 文章ID
	 */
	function get_info($app, $contentid) {
		if ($app == 'special') {
			$r = $this->db->where ( array ('id' => $contentid ))->field('title, url' )->find();
			return array ('title' => $r ['title'],'url' => $r ['url'] );
		} elseif ($app == 'special_content') {
			$this->db = Loader::model ( 'special_content_model' );
			$r = $this->db->where ( array ('id' => $contentid ))->field('title, url' )->find();
			if ($r) {
				return array ('title' => $r ['title'],'url' => $r ['url'] );
			} else {
				return false;
			}
		}
	}
}
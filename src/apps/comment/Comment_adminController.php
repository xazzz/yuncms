<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );

/**
 * 评论设置
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-13
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: Comment_adminController.php 200 2013-03-29 23:15:00Z
 *          85825770@qq.com $
 */
class Comment_adminController extends Web_Admin {
	private $comment_data_db, $comment_db;
	function __construct() {
		parent::__construct ();
		$this->comment_data_db = Loader::model ( 'comment_data_model' );
		$this->comment_db = Loader::model ( 'comment_model' );
	}

	/**
	 * 评论设置
	 */
	public function init() {
		if (isset ( $_POST ['dosubmit'] )) {
			$guest = isset ( $_POST ['guest'] ) && intval ( $_POST ['guest'] ) ? intval ( $_POST ['guest'] ) : 0;
			$check = isset ( $_POST ['check'] ) && intval ( $_POST ['check'] ) ? intval ( $_POST ['check'] ) : 0;
			$code = isset ( $_POST ['code'] ) && intval ( $_POST ['code'] ) ? intval ( $_POST ['code'] ) : 0;
			$add_point = isset ( $_POST ['add_point'] ) && abs ( intval ( $_POST ['add_point'] ) ) ? intval ( $_POST ['add_point'] ) : 0;
			$del_point = isset ( $_POST ['del_point'] ) && abs ( intval ( $_POST ['del_point'] ) ) ? intval ( $_POST ['del_point'] ) : 0;
			$data = array ('guest' => $guest,'check' => $check,'code' => $code,'add_point' => $add_point,'del_point' => $del_point );
			S ( 'common/comment', $data );
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$data = S ( 'common/comment' );
			$show_header = true;
			include $this->view ( 'comment_setting' );
		}
	}

	public function lists() {
		$show_header = true;
		$commentid = isset ( $_GET ['commentid'] ) && trim ( $_GET ['commentid'] ) ? trim ( $_GET ['commentid'] ) : showmessage ( L ( 'illegal_parameters' ), HTTP_REFERER );
		$hot = isset ( $_GET ['hot'] ) && intval ( $_GET ['hot'] ) ? intval ( $_GET ['hot'] ) : 0;
		$comment = $this->comment_db->getby_commentid( $commentid );
		if (empty ( $comment )) {
			$forward = isset ( $_GET ['show_center_id'] ) ? 'blank' : HTTP_REFERER;
			showmessage ( L ( 'no_comment' ), $forward );
		}
		Loader::helper ( 'comment:global' );
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$pagesize = 20;
		$offset = ($page - 1) * $pagesize;
		$this->comment_data_db->table_name ( $comment ['tableid'] );
		$desc = 'id desc';
		if (! empty ( $hot )) {
			$desc = 'support desc, id desc';
		}
		$list = $this->comment_data_db->where(array ('commentid' => $commentid,'status' => 1 ))->order($desc)->limit($offset . ',' . $pagesize)->select ();
		$pages = Page::pages ( $comment ['total'], $page, $pagesize );
		include $this->view ( 'comment_data_list' );
	}

	public function listinfo() {
		$r = $max_table = '';
		$max_table = isset ( $_GET ['max_table'] ) ? intval ( $_GET ['max_table'] ) : 0;
		if (! $max_table) {
			$r = $this->comment_db->max('tableid');
			if (! $r ['tableid']) showmessage ( L ( 'no_comment' ) );
			$max_table = $r ['tableid'];
		}
		$page = isset ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$tableid = isset ( $_GET ['tableid'] ) ? intval ( $_GET ['tableid'] ) : $max_table;
		if ($tableid > $max_table) $tableid = $max_table;
		$where = array();
		if (isset ( $_GET ['search'] )) {
			$t = $comment_id = '';
			$keywords = safe_replace ( $_GET ['keyword'] );
			$searchtype = intval ( $_GET ['searchtype'] );
			switch ($searchtype) {
				case '0' :
					$data = $this->comment_db->where(array('title'=>array('like',"%$keywords%"),'tableid'=>$tableid))->select();
					if (! empty ( $data )) {
						foreach ( $data as $d ) {
							$comment_id .= $t . '\'' . $d ['commentid'] . '\'';
							$t = ',';
						}
						$where['commentid'] = array('in',$comment_id);
					}
					break;

				case '1' :
					$keywords = intval ( $keywords );
					$data = $this->comment_db->where(array('commentid'=>array('like',"content_%-$keywords-%")))->select();
					$data = $this->comment_db->fetch_array ();
					foreach ( $data as $d ) {
						$comment_id .= $t . '\'' . $d ['commentid'] . '\'';
						$t = ',';
					}
					$where['commentid'] = array('in',$comment_id);
					break;

				case '2' :
					$where['username'] = $keywords;
					break;
			}
		}
		$data = array ();
		$this->comment_data_db->table_name ( $tableid );
		$data = $this->comment_data_db->where($where)->order('id DESC')->listinfo ($page, 10 );
		$pages = $this->comment_data_db->pages;
		include $this->view ( 'comment_listinfo' );
	}

	/**
	 * 删除评论
	 */
	public function del() {
		if (isset ( $_GET ['dosubmit'] ) && $_GET ['dosubmit']) {
			$ids = $_GET ['ids'];
			$tableid = isset ( $_GET ['tableid'] ) ? intval ( $_GET ['tableid'] ) : 0;
			$r = $this->comment_db->max('tableid');
			$max_table = $r ['tableid'];
			if (! $tableid || $max_table < $tableid) showmessage ( L ( 'illegal_operation' ) );
			$this->comment_data_db->table_name ( $tableid );
			if (is_array ( $ids )) {
				foreach ( $ids as $id ) {
					$comment_info = $this->comment_data_db->where ( array ('id' => $id ))->field( 'commentid' )->find();
					$this->comment_db->where(array ('commentid' => $comment_info ['commentid'] ))->update ( array ('total' => '-=1' ) );
					$this->comment_data_db->where(array ('id' => $id ) )->delete ( );
				}
				$ids = implode ( ',', $ids );
			} elseif (is_numeric ( $ids )) {
				$id = intval ( $ids );
				$comment_info = $this->comment_data_db->where( array ('id' => $id ))->field('commentid' )->find();
				$this->comment_db->where(array ('commentid' => $comment_info ['commentid'] ))->update ( array ('total' => '-=1' ) );
				$this->comment_data_db->where(array ('id' => $id ))->delete (  );
			} else {
				showmessage ( L ( 'illegal_operation' ) );
			}
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		}
	}
}
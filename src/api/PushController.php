<?php
/**
 * 采集推送接口
 * @author Tongle Xu <xutongle@gmail.com> 2012-11-14
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: PushController.php 691 2013-07-30 04:12:44Z 85825770@qq.com $
 */
class PushController {

	private $db;
	public $categorys;

	public function __construct() {
		$this->db = Loader::model ( 'content_model' );
		$this->categorys = S ( 'common/category_content' );
	}

	/**
	 * 获取栏目
	 */
	public function category() {
		$str = '';
		foreach ( $this->categorys as $k => $v ) {
			if ($v ['modelid'])
				$str .= $v ['catid'] . '-' . $v ['catname'] . '|';
		}
		exit ( $str );
	}

	public function publish() {
		define ( 'INDEX_HTML', true ); // 生成首页
		define ( 'RELATION_HTML', true ); // 生成列表页
		$info = array();
		$info['catid'] = isset ( $_POST ['catid'] ) ? intval ( $_POST ['catid'] ) : exit ( "Catid can't for empty." );
		$info['title'] = isset ( $_POST ['title'] ) && !empty($_POST ['title']) ? trim ( $_POST ['title'] ) : exit ( "Title can't for empty." );
		$info['content'] = isset ( $_POST ['content'] ) && !empty($_POST ['content']) ? $_POST ['content'] : exit ( "Content can't for empty." );

		$category = $this->categorys [$info['catid']];
		$modelid = $this->categorys [$info['catid']] ['modelid'];
		/*
		 // 分词
		if (isset ( $_POST ['keywords'] ) && ! empty ( $_POST ['keywords'] )) {
		$info ['keywords'] = trim ( $_POST ['keywords'] );
		} else {
		$info ['keywords'] = get_keywords ( $info ['title'], 3 );
		if (str_exists ( $info ['keywords'], 'PHPCMS' ))
			$info ['keywords'] = ' ';
		}
		*/
		//$info ['description'] = ! empty ( $_POST ['description'] ) ? $_POST ['description'] : str_cut ( str_replace ( array ("\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;','&nbsp;' ), '', strip_tags ( $info ['content'] ) ), 200 );
		$info ['username'] = isset ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : 'admin';
		$info ['inputtime'] = isset ( $_POST ['date'] ) ? trim ( $_POST ['date'] ) : date ( 'Y-m-d H:i:s' );

		//自动提取摘要
		$_POST ['add_introduce'] = true;
		// 自动提取缩略图
		$_POST ['auto_thumb'] = TRUE;
		$_POST ['auto_thumb_no'] = 1;
		//状态
		$info['status'] = 99;

		// 自动分页
		$info ['paginationtype'] = 1;
		if (isset ( $_POST ['maxcharperpage'] )) {
			$info ['maxcharperpage'] = intval ( $_POST ['maxcharperpage'] );
		} else {
			$info ['maxcharperpage'] = 5000;
		}
		$this->db->set_model ( $modelid );
		if ($this->db->add_content ( $info ))
			exit ( 'Publish success.' );
		exit ( 'Publish failed.' );

		// preg_replace ( "'([\r\n])[\s]+'", "", $info ['content'] ); //
		// 统一去除回车换行符
		// $info ['content'] = str_replace ( ">" . chr ( 13 ), ">", $info
		// ['content'] ); // 在每个结束标签后面删除所有回车符号
		// $info ['content'] = str_replace ( ">", ">" . chr ( 13 ), $info
		// ['content'] ); // 在每个结束标签后面添加回车符号


	}
}
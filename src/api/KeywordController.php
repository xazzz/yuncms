<?php
/**
 * 关键词Api
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: KeywordController.php 642 2013-07-29 08:56:39Z 85825770@qq.com $
 */
defined('IN_YUNCMS') or exit('No permission resources.');
class KeywordController {

	public function __construct() {
		$this->charset = strtolower ( CHARSET );
	}

	public function get() {
		$number = isset ( $_GET ['number'] ) ? intval ( $_GET ['number'] ) : 3;
		$data = isset ( $_POST ['data'] ) ? trim ( $_POST ['data'] ) : exit ();
		$result = get_keywords ( $data, $number );
		exit ( $result );
	}
}
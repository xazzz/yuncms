<?php
/**
 * 前台调用联动菜单
 * @author Tongle Xu <xutongle@gmail.com> 2012-11-12
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: LinkageController.php 691 2013-07-30 04:12:44Z 85825770@qq.com $
 */
class LinkageController {

	public function __construct() {
		if (! $_GET ['callback']) showmessage ( L ( 'error' ) );
	}

	/**
	 * 获取地区列表
	 */
	public function ajax_getlist() {
		$keyid = intval ( $_GET ['keyid'] );
		$datas = S ( 'linkage/' . $keyid );
		$infos = $datas ['data'];
		$where_id = isset ( $_GET ['parentid'] ) ? $_GET ['parentid'] : intval ( $infos [$_GET ['linkageid']] ['parentid'] );
		$parent_menu_name = ($where_id == 0) ? $datas ['title'] : $infos [$where_id] ['name'];
		$s = array ();
		foreach ( $infos as $k => $v ) {
			if ($v ['parentid'] == $where_id) {
				$s [] = iconv ( CHARSET, 'utf-8', $v ['linkageid'] . ',' . $v ['name'] . ',' . $v ['parentid'] . ',' . $parent_menu_name );
			}
		}
		if (count ( $s ) > 0) {
			$jsonstr = json_encode ( $s );
			echo trim_script ( $_GET ['callback'] ) . '(', $jsonstr, ')';
			exit ();
		} else {
			echo trim_script ( $_GET ['callback'] ) . '()';
			exit ();
		}
	}

	/**
	 * 获取父级路径路径
	 */
	public function ajax_getpath() {
		$keyid = isset ( $_GET ['keyid'] ) ? intval ( $_GET ['keyid'] ) : exit ();
		$parentid = isset ( $_GET ['parentid'] ) ? intval ( $_GET ['parentid'] ) : exit ();
		$callback = isset ( $_GET ['callback'] ) ? trim ( $_GET ['callback'] ) : exit ();
		$this->get_path($parentid, $keyid, $callback);
	}



	/**
	 * 获取地区顶级ID
	 * Enter description here .
	 */
	public function ajax_gettopparent() {
		$keyid = isset($_GET['keyid']) ? intval ( $_GET['keyid'] ) : exit;
		$linkageid = isset($_GET['linkageid']) ? intval ( $_GET['linkageid'] ) : exit;
		$callback = isset ( $_GET ['callback'] ) ? trim ( $_GET ['callback'] ) : exit ();
		$this->get_topparent($linkageid, $keyid, $callback);
	}

	public function ajax_select() {
		$parent_id = $_GET ['parent_id'] ? intval ( $_GET ['parent_id'] ) : 0;
		$keyid = $_GET ['keyid'];
		$keyid = intval ( $keyid );
		$datas = S ( 'linkage/' . $keyid );
		$infos = $datas ['data'];
		$json_str = "[";
		$json = array ();
		foreach ( $infos as $k => $v ) {
			if ($v ['parentid'] == $parentid) {
				$r = array ('region_id' => $v ['linkageid'],'region_name' => $v ['name'] );
				$json [] = $this->JSON ( $r );
			}
		}
		$json_str .= implode ( ',', $json );
		$json_str .= "]";
		echo $json_str;
	}

	/**
	 * 获取地区顶级ID
	 * Enter description here ...
	 * @param  $linkageid 菜单id
	 * @param  $keyid 菜单keyid
	 * @param  $callback json生成callback变量
	 */
	private function get_topparent($linkageid,$keyid,$callback){
		$datas = S ( 'linkage/' . $keyid );
		$infos = $datas ['data'];
		if ($infos [$linkageid] ['parentid'] != 0) {
			return $this->get_topparent ( $infos [$linkageid] ['parentid'], $keyid, $callback, $infos );
		} else {
			echo trim_script ( $callback ) . '(', $linkageid, ')';
			exit ();
		}
	}

	/**
	 * 获取地区父级路径路径
	 * @param $parentid 父级ID
	 * @param $keyid 菜单keyid
	 * @param $callback json生成callback变量
	 */
	private function get_path($parentid,$keyid,$callback) {
		$datas = S ( 'linkage/' . $keyid );
		$infos = $datas ['data'];
		$result = array ();
		if (array_key_exists ( $parentid, $infos )) {
			$result [] = iconv ( CHARSET, 'utf-8', $infos [$parentid] ['name'] );
			return $this->get_path ( $infos [$parentid] ['parentid'], $keyid, $callback, $result, $infos );
		} else {
			if (count ( $result ) > 0) {
				krsort ( $result );
				$jsonstr = json_encode ( $result );
				echo trim_script ( $callback ) . '(', $jsonstr, ')';
				exit ();
			} else {
				$result [] = iconv ( CHARSET, 'utf-8', $datas ['title'] );
				$jsonstr = json_encode ( $result );
				echo trim_script ( $callback ) . '(', $jsonstr, ')';
				exit ();
			}
		}
	}

	private function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
		foreach ( $array as $key => $value ) {
			if (is_array ( $value )) {
				$this->arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
			} else {
				$array [$key] = $function ( $value );
			}

			if ($apply_to_keys_also && is_string ( $key )) {
				$new_key = $function ( $key );
				if ($new_key != $key) {
					$array [$new_key] = $array [$key];
					unset ( $array [$key] );
				}
			}
		}
	}

	private function JSON($array) {
		$this->arrayRecursive ( $array, 'urlencode', true );
		$json = json_encode ( $array );
		return urldecode ( $json );
	}
}
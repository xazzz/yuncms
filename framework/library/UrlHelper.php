<?php
/**
 * UrlHelper.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: UrlHelper.php 724 2013-08-06 02:58:36Z 85825770@qq.com $
 */
class UrlHelper {

	/**
	 * url检查
	 *
	 * 当$absolute === true且url不包含协议部分时,默认加上当前应用的协议部分.
	 *
	 * @param string $url 需要检查合法性的url
	 * @param boolean $absolute 是否为绝对路径
	 * @return string
	 */
	public static function check_url($url, $absolute = true) {
		if ($absolute) {
			$_baseUrl = $absolute === true ? Base_Request::get_base_url ( true ) : $absolute;
			if (strpos ( $url, '://' ) === false) {
				$url = trim ( $_baseUrl, '/' ) . '/' . trim ( $url, '/' );
			}
		}
		return $url;
	}

	/**
	 * url字符串转化为数组格式
	 *
	 * 效果同'args_to_url'相反
	 *
	 * @param string $url
	 * @param boolean $decode 是否需要进行url反编码处理
	 * @param string $separator url的分隔符
	 * @return array
	 */
	public static function url_to_args($url, $decode = true, $separator = '&=') {
		if (strlen ( $separator ) !== 2) return array ();
		if (false !== $pos = strpos ( $url, '?' )) $url = substr ( $url, $pos + 1 );
		$url = explode ( $separator [0], trim ( $url, $separator [0] ) );
		$args = array ();
		if ($separator [0] === $separator [1]) {
			$_count = count ( $url );
			for($i = 0; $i < $_count; $i += 2) {
				if (! isset ( $url [$i + 1] )) {
					$args [] = $decode ? rawurldecode ( $url [$i] ) : $url [$i];
					continue;
				}
				$_k = $decode ? rawurldecode ( $url [$i] ) : $url [$i];
				$_v = $decode ? rawurldecode ( $url [$i + 1] ) : $url [$i + 1];
				$args [$_k] = $_v;
			}
		} else {
			foreach ( $url as $value ) {
				if (strpos ( $value, $separator [1] ) === false) {
					$args [] = $decode ? rawurldecode ( $value ) : $value;
					continue;
				}
				list ( $__k, $__v ) = explode ( $separator [1], $value );
				$args [$__k] = $decode && $__v ? rawurldecode ( $__v ) : $__v;
			}
		}
		return $args;
	}

	/**
	 * 将数组格式的参数列表转换为Url格式，并将url进行编码处理
	 *
	 * <code>参数:array('b'=>'b','c'=>'index','d'=>'d')
	 * 分割符: '&='
	 * 转化结果:&b=b&c=index&d=d
	 * 如果分割符为: '/' 则转化结果为: /b/b/c/index/d/d/</code>
	 *
	 * @param array $args
	 * @param boolean $encode 是否进行url编码 默认值为true
	 * @param string $separator url分隔符 支持双字符,前一个字符用于分割参数对,后一个字符用于分割键值对
	 * @return string
	 */
	public static function args_to_url($args, $encode = true, $separator = '&=', $key = null) {
		if (strlen ( $separator ) !== 2) return;
		$_tmp = '';
		foreach ( ( array ) $args as $_k => $_v ) {
			if ($key !== null) $_k = $key . '[' . $_k . ']';
			if (is_array ( $_v )) {
				$_tmp .= self::args_to_url ( $_v, $encode, $separator, $_k ) . $separator [0];
				continue;
			}
			$_v = $encode ? rawurlencode ( $_v ) : $_v;
			if (is_int ( $_k )) {
				$_v && $_tmp .= $_v . $separator [0];
				continue;
			}
			$_k = ($encode ? rawurlencode ( $_k ) : $_k);
			$_tmp .= $_k . $separator [1] . $_v . $separator [0];
		}
		return trim ( $_tmp, $separator [0] );
	}

	/**
	 * 解析ControllerPath,并返回解析后的结果集
	 *
	 * 返回值:array(action,controller,module,args)
	 * <code>action格式:'/module/controller/action/?a=a&b=b&c=c&',前边用斜线分割mca信息,后边用问号分割参数列表.</code>
	 *
	 * @param string $controllerPath
	 * @param array $args 默认值为空数组
	 * @return array
	 */
	public static function resolve_action($action, $args = array()) {
		list ( $action, $_args ) = explode ( '?', $action . '?' );
		$args = array_merge ( $args, ($_args ? self::url_to_args ( $_args, false ) : array ()) );
		$action = explode ( '/', trim ( $action, '/' ) . '/' );
		end ( $action );
		return array (prev ( $action ),prev ( $action ),prev ( $action ),$args );
	}

	/**
	 * URL组装 支持不同URL模式
	 *
	 * @param string $url URL表达式，格式：'[应用/模块/操作]?参数1=值1&参数2=值2...'
	 * @param string|array $vars 传入的参数，支持数组和字符串
	 * @param boolean $redirect 是否跳转，如果设置为true则表示跳转到该URL地址
	 * @param boolean $domain 是否显示域名
	 * @return string
	 */
	public static function create_url($url = '', $vars = '', $redirect = false, $domain = false) {
		// 解析URL
		$info = parse_url ( $url );
		$url = ! empty ( $info ['path'] ) ? $info ['path'] : ACTION;
		if (isset ( $info ['fragment'] )) { // 解析锚点
			$anchor = $info ['fragment'];
			if (false !== strpos ( $anchor, '?' )) { // 解析参数
				list ( $anchor, $info ['query'] ) = explode ( '?', $anchor, 2 );
			}
		}
		// 解析参数
		if (is_string ( $vars )) { // aaa=1&bbb=2 转换成数组
			parse_str ( $vars, $vars );
		} elseif (! is_array ( $vars )) {
			$vars = array ();
		}
		if (isset ( $info ['query'] )) { // 解析地址里面参数 合并到vars
			parse_str ( $info ['query'], $params );
			$vars = array_merge ( $params, $vars );
		}
		// URL组装
		if ($url) {
			$url = trim ( $url, '/' );
			$path = explode ( '/', $url );
			$var = array ();
			if (isset ( $path [2] )) $var ['action'] = $path [2];
			if (isset ( $path [1] )) $var ['controller'] = $path [1];
			$var ['app'] = isset ( $path [0] ) ? $path [0] : APP;
		}
		if (C ( 'config', 'url_model' ) == 0) { // 普通模式URL转换
			$url = PHP_FILE . '?' . http_build_query ( array_reverse ( $var ) );
			if (! empty ( $vars )) {
				$vars = urldecode ( http_build_query ( $vars ) );
				$url .= '&' . $vars;
			}
		} else if (C ( 'config', 'url_model' ) != 0) {
			$url = WEB_PATH . implode ( '/', array_reverse ( $var ) );
			if (! empty ( $vars )) { // 添加参数
				$params = http_build_query ( $vars );
				$url = $url . '?' . $params;
			}
		}
		if ($domain) {
			$url = SITE_PROTOCOL . SITE_HOST . $url;
		}
		if ($redirect) // 直接跳转URL
			redirect ( $url );
		else
			return $url;
	}
}
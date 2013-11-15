<?php
/**
 * func.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: func.php 734 2013-08-07 09:47:42Z 85825770@qq.com $
 */
define ( 'CORE_FUNCTION', true );
/**
 * 载入文件或类
 *
 * @param string $name 文件名称 或带路径的文件名称
 * @param string $folder 文件夹默认为空
 */
function import($name, $folder = '') {
	return Core::import ( $name, $folder );
}

/**
 * 设置和获取统计数据
 *
 * @param string $key 要统计的项
 * @param int $step 递加的值
 * @return int 如果递加的值为空返回目前该项统计到的次数
 */
function N($key, $step = 0) {
	static $_num = array ();
	if (! isset ( $_num [$key] )) {
		$_num [$key] = 0;
	}
	if (empty ( $step ))
		return $_num [$key];
	else
		$_num [$key] = $_num [$key] + ( int ) $step;
}

/**
 * 加载配置文件
 *
 * @param string $file 文件名
 * @param string $key 配置项
 * @param string/bool $default 默认值
 */
function C($file, $key = null, $default = false) {
	return Base_Config::get ( $file, $key, $default );
}

/**
 * 全局缓存读取、设置、删除，默认为文件缓存。
 *
 * @param string $key 缓存名称
 * @param string $value 缓存内容
 * @param int $expires 缓存有效期
 * @param string $options 缓存配置
 */
function S($key, $value = null, $expires = 0, $options = null) {
	if (is_null ( $value )) { // 获取缓存
		return Factory::cache ( $options )->get ( $key );
	} elseif ($value === '') { // 删除缓存
		return Factory::cache ( $options )->delete ( $key );
	} else {
		return Factory::cache ( $options )->set ( $key, $value, $expires );
	}
}

/**
 * 记录加载和运行时间
 *
 * @param string $start
 * @param string $end
 * @param int $dec
 */
function G($start, $end = '', $dec = 3) {
	static $_info = array ();
	if (! empty ( $end )) { // 统计时间
		if (! isset ( $_info [$end] )) $_info [$end] = microtime ( TRUE );
		return number_format ( ($_info [$end] - $_info [$start]), $dec );
	} else { // 记录时间
		$_info [$start] = microtime ( TRUE );
	}
}

/**
 * 文件数据读写(简单数据类型、数组、字符串等)
 *
 * @param string $name 文件路径
 * @param string $value 文件内容
 * @param string $path 文件路径
 */
function F($file_name, $value) {
	if ($value !== '') {
		return File::write ( $file_name, $value, File::WRITE );
	} elseif (is_file ( $file_name )) {
		$value = include $file_name;
	} else {
		$value = false;
	}
	return $value;
}

/**
 * 加载视图
 *
 * @param string $template
 * @param string $$application
 * @param string $$application
 */
function V($template = 'index', $application = null, $style = null) {
	if ($style == null) $style = C ( 'template', 'name' );
	$compiledtplfile = View::instance ()->compile ( $template, $application, $style );
	return $compiledtplfile;
}

/**
 * 语言文件处理
 *
 * @param string $language
 * @param array $pars
 * @param string $applications
 * @return string
 */
function L($language = 'NO_LANG', $pars = array(), $applications = '') {
	static $lang = null;
	if (is_null ( $lang )) $lang = Base_Lang::instance ();
	return $lang->load ( $language, $pars, $applications );
}

/**
 * 队列操作
 *
 * @param string $name
 * @param array $data
 * @param string $setting
 */
function Q($name, $data = '', $setting = 'default') {
	$queue = Factory::queue ( $setting );
	if (empty ( $data )) {
		return $queue->get ( $name );
	}
	return $queue->put ( $name, $data );
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
function U($url = '', $vars = '', $redirect = false, $domain = false) {
	return UrlHelper::create_url($url,$vars,$redirect,$domain);
}

/**
 * Cookie设置、获取、删除
 *
 * @param string $var Cookie名称
 * @param string $value Cookie值
 * @param int $time Cookie有效期
 * @return Ambigous <mixed, string, unknown>
 */
function cookie($key, $value = null, $time = 0) {
	if (is_null ( $value )) {
		return Cookie::get ( $key );
	} else if ($value == '') {
		return Cookie::delete ( $key );
	} else {
		return Cookie::set ( $key, $value, $time );
	}
}

/**
 * 程序执行时间
 *
 * @return int
 */
function execute_time() {
	$etime = microtime ( true );
	return number_format ( ($etime - START_TIME), 6 );
}

/**
 * 显示运行时间、数据库操作、缓存次数、内存使用信息
 *
 * @return string
 */
function show_time() {
	if (! C ( 'config', 'show_time' )) return;
	$show_time = '';
	// 显示运行时间
	$show_time = 'Process: ' . execute_time () . ' seconds ';
	if (class_exists ( 'Core_DB', false )) $show_time .= ' | DB :' . N ( 'db_query' ) . ' queries ';
	$show_time .= ' | Cache :' . N ( 'cache_read' ) . ' gets ' . N ( 'cache_write' ) . ' writes ';
	// 显示内存开销
	$startMem = array_sum ( explode ( ' ', START_MEMORY ) );
	$endMem = array_sum ( explode ( ' ', memory_get_usage () ) );
	$show_time .= ' | UseMem:' . number_format ( ($endMem - $startMem) / 1024 ) . ' kb';
	if (IS_CLI) return "\r\n" . $show_time . "\r\n";
	return $show_time;
}
function show_trace() {
	if (! Base_Request::is_ajax () && C ( 'config', 'show_trace' )) {
		$trace_page_tabs = array ('BASE' => '基本','FILE' => '文件','INFO' => '流程','ERR|NOTIC' => '错误','SQL' => 'SQL','DEBUG' => '调试' ); // 页面Trace可定制的选项卡
		                                                                                                                             // 系统默认显示信息
		$files = get_included_files ();
		$info = array ();
		foreach ( $files as $key => $file ) {
			$info [] = $file . ' ( ' . number_format ( filesize ( $file ) / 1024, 2 ) . ' KB )';
		}
		$trace = array ();
		$base = array ('请求信息' => date ( 'Y-m-d H:i:s', $_SERVER ['REQUEST_TIME'] ) . ' ' . $_SERVER ['SERVER_PROTOCOL'] . ' ' . $_SERVER ['REQUEST_METHOD'] . ' : ' . $_SERVER ['REQUEST_URI'],'运行时间' => show_time (),'内存开销' => MEMORY_LIMIT_ON ? number_format ( (memory_get_usage () - START_MEMORY) / 1024, 2 ) . ' kb' : '不支持','查询信息' => N ( 'db_query' ) . ' queries ' . N ( 'db_write' ) . ' writes ','文件加载' => count ( get_included_files () ),'缓存信息' => N ( 'cache_read' ) . ' gets ' . N ( 'cache_write' ) . ' writes ','会话信息' => 'SESSION_ID=' . session_id () );
		$debug = trace ();
		foreach ( $trace_page_tabs as $name => $title ) {
			switch (strtoupper ( $name )) {
				case 'BASE' : // 基本信息
					$trace [$title] = $base;
					break;
				case 'FILE' : // 文件信息
					$trace [$title] = $info;
					break;
				default : // 调试信息
					$name = strtoupper ( $name );
					if (strpos ( $name, '|' )) { // 多组信息
						$array = explode ( '|', $name );
						$result = array ();
						foreach ( $array as $name ) {
							$result += isset ( $debug [$name] ) ? $debug [$name] : array ();
						}
						$trace [$title] = $result;
					} else {
						$trace [$title] = isset ( $debug [$name] ) ? $debug [$name] : '';
					}
			}
		}
	}
	include FW_PATH . 'errors' . DIRECTORY_SEPARATOR . 'trace.php';
}

/**
 * 添加和获取页面Trace记录
 *
 * @param string $value 变量
 * @param string $label 标签
 * @param string $level 日志级别
 * @param boolean $record 是否记录日志
 * @return void
 */
function trace($value = '[leaps]', $label = '', $level = 'DEBUG', $record = false) {
	static $_trace = array ();
	if ('[leaps]' === $value) { // 获取trace信息
		return $_trace;
	} else {
		$info = ($label ? $label . ':' : '') . print_r ( $value, true );
		if ('ERR' == $level && C ( 'config', 'trace_exception' )) { // 抛出异常
			throw new Exception ( $info );
		}
		$level = strtoupper ( $level );
		if (! isset ( $_trace [$level] )) {
			$_trace [$level] = array ();
		}
		$_trace [$level] [] = $info;
	}
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 *
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
	if (is_object ( $mix ) && function_exists ( 'spl_object_hash' )) {
		return spl_object_hash ( $mix );
	} elseif (is_resource ( $mix )) {
		$mix = get_resource_type ( $mix ) . strval ( $mix );
	} else {
		$mix = serialize ( $mix );
	}
	return md5 ( $mix );
}

/**
 * 将字符串转换为数组
 *
 * @param string $data
 * @return array
 */
function string2array($data) {
	$array = array ();
	if ($data == '') return $array;
	@eval ( "\$array = $data;" );
	return $array;
}

/**
 * 将数组转换为字符串
 *
 * @param array $data
 * @param bool $isformdata
 * @return string
 *
 */
function array2string($data, $isformdata = 1) {
	if ($data == '') return '';
	if ($isformdata) $data = String::stripslashes ( $data );
	return var_export ( $data, TRUE );
}

/**
 * 错误日志接口
 *
 * @param string $level 日志级别
 * @param string $message 日志信息
 * @param boolean $php_error 是否是PHP错误
 */
function log_message($level = 'error', $message, $php_error = FALSE) {
	if (C ( 'log', 'log_threshold' ) == 0) return;
	Log::get_instance ()->write ( $level, $message, $php_error );
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 *
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type = 0) {
	if ($type) {
		return ucfirst ( preg_replace ( "/_([a-zA-Z])/e", "strtoupper('\\1')", $name ) );
	} else {
		return strtolower ( trim ( preg_replace ( "/[A-Z]/", "_\\0", $name ), "_" ) );
	}
}

/**
 * URL重定向
 *
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '') {
	// 多行URL地址支持
	$url = str_replace ( array ("\n","\r" ), '', $url );
	if (empty ( $msg )) $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (! headers_sent ()) { // redirect
		if (0 === $time) {
			header ( 'Location: ' . $url );
		} else {
			header ( "refresh:{$time};url={$url}" );
			echo ($msg);
		}
		exit ();
	} else {
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0) $str .= $msg;
		exit ( $str );
	}
}

/**
 * 浏览器友好的变量输出
 *
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void string
 */
function dump($var, $echo = true, $label = null, $strict = true) {
	$label = ($label === null) ? '' : rtrim ( $label ) . ' ';
	if (! $strict) {
		if (ini_get ( 'html_errors' )) {
			$output = print_r ( $var, true );
			$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
		} else {
			$output = $label . print_r ( $var, true );
		}
	} else {
		ob_start ();
		var_dump ( $var );
		$output = ob_get_clean ();
		if (! extension_loaded ( 'xdebug' )) {
			$output = preg_replace ( '/\]\=\>\n(\s+)/m', '] => ', $output );
			$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
		}
	}
	if ($echo) {
		echo ($output);
		return null;
	} else
		return $output;
}

/**
 * 获取系统信息
 */
function get_sysinfo() {
	$sys_info ['os'] = PHP_OS;
	$sys_info ['zlib'] = function_exists ( 'gzclose' ); // zlib
	$sys_info ['safe_mode'] = ( boolean ) ini_get ( 'safe_mode' ); // safe_mode =
	                                                               // Off
	$sys_info ['safe_mode_gid'] = ( boolean ) ini_get ( 'safe_mode_gid' ); // safe_mode_gid
	                                                                       // =
	                                                                       // Off
	$sys_info ['timezone'] = function_exists ( "date_default_timezone_get" ) ? date_default_timezone_get () : L ( 'no_setting' );
	$sys_info ['socket'] = function_exists ( 'fsockopen' );
	$sys_info ['web_server'] = $_SERVER ['SERVER_SOFTWARE'];
	$sys_info ['phpv'] = phpversion ();
	$sys_info ['fileupload'] = @ini_get ( 'file_uploads' ) ? ini_get ( 'upload_max_filesize' ) : 'unknown';
	return $sys_info;
}
function set_status_header($code = 200, $text = '') {
	$stati = array (200 => 'OK',201 => 'Created',202 => 'Accepted',203 => 'Non-Authoritative Information',204 => 'No Content',205 => 'Reset Content',206 => 'Partial Content',

	300 => 'Multiple Choices',301 => 'Moved Permanently',302 => 'Found',304 => 'Not Modified',305 => 'Use Proxy',307 => 'Temporary Redirect',

	400 => 'Bad Request',401 => 'Unauthorized',403 => 'Forbidden',404 => 'Not Found',405 => 'Method Not Allowed',406 => 'Not Acceptable',407 => 'Proxy Authentication Required',408 => 'Request Timeout',409 => 'Conflict',410 => 'Gone',411 => 'Length Required',412 => 'Precondition Failed',413 => 'Request Entity Too Large',414 => 'Request-URI Too Long',415 => 'Unsupported Media Type',416 => 'Requested Range Not Satisfiable',417 => 'Expectation Failed',

	500 => 'Internal Server Error',501 => 'Not Implemented',502 => 'Bad Gateway',503 => 'Service Unavailable',504 => 'Gateway Timeout',505 => 'HTTP Version Not Supported' );

	if ($code == '' or ! is_numeric ( $code )) {
		Base_Error::show_error ( 'Status codes must be numeric', 500 );
	}

	if (isset ( $stati [$code] ) and $text == '') {
		$text = $stati [$code];
	}

	if ($text == '') {
		Base_Error::show_error ( 'No status text available.  Please check your status code number or supply your own message text.', 500 );
	}
	$server_protocol = (isset ( $_SERVER ['SERVER_PROTOCOL'] )) ? $_SERVER ['SERVER_PROTOCOL'] : FALSE;
	if (IS_CGI) {
		header ( "Status: {$code} {$text}", TRUE );
	} elseif ($server_protocol == 'HTTP/1.1' or $server_protocol == 'HTTP/1.0') {
		header ( $server_protocol . " {$code} {$text}", TRUE, $code );
	} else {
		header ( "HTTP/1.1 {$code} {$text}", TRUE, $code );
	}
}

/**
 * 对数据进行编码转换
 *
 * @param array/string $data 数组
 * @param string $input 需要转换的编码
 * @param string $output 转换后的编码
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
	if (! is_array ( $data )) {
		return iconv ( $input, $output, $data );
	} else {
		foreach ( $data as $key => $val ) {
			if (is_array ( $val )) {
				$data [$key] = array_iconv ( $val, $input, $output );
			} else {
				$data [$key] = iconv ( $input, $output, $val );
			}
		}
		return $data;
	}
}

/**
 * BMP 创建函数
 * @author simon
 * @param string $filename path of bmp file
 * @example who use,who knows
 * @return resource of GD
 */
function imagecreatefrombmp( $filename ){
	if ( !$f1 = fopen( $filename, "rb" ) )
		return FALSE;

	$FILE = unpack( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread( $f1, 14 ) );
	if ( $FILE['file_type'] != 19778 )
		return FALSE;

	$BMP = unpack( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread( $f1, 40 ) );
	$BMP['colors'] = pow( 2, $BMP['bits_per_pixel'] );
	if ( $BMP['size_bitmap'] == 0 )
		$BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
	$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
	$BMP['bytes_per_pixel2'] = ceil( $BMP['bytes_per_pixel'] );
	$BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
	$BMP['decal'] -= floor( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
	$BMP['decal'] = 4 - (4 * $BMP['decal']);
	if ( $BMP['decal'] == 4 )
		$BMP['decal'] = 0;

	$PALETTE = array();
	if ( $BMP['colors'] < 16777216 ){
		$PALETTE = unpack( 'V' . $BMP['colors'], fread( $f1, $BMP['colors'] * 4 ) );
	}

	$IMG = fread( $f1, $BMP['size_bitmap'] );
	$VIDE = chr( 0 );

	$res = imagecreatetruecolor( $BMP['width'], $BMP['height'] );
	$P = 0;
	$Y = $BMP['height'] - 1;
	while( $Y >= 0 ){
		$X = 0;
		while( $X < $BMP['width'] ){
			if ( $BMP['bits_per_pixel'] == 32 ){
				$COLOR = unpack( "V", substr( $IMG, $P, 3 ) );
				$B = ord(substr($IMG, $P,1));
				$G = ord(substr($IMG, $P+1,1));
				$R = ord(substr($IMG, $P+2,1));
				$color = imagecolorexact( $res, $R, $G, $B );
				if ( $color == -1 )
					$color = imagecolorallocate( $res, $R, $G, $B );
				$COLOR[0] = $R*256*256+$G*256+$B;
				$COLOR[1] = $color;
			}elseif ( $BMP['bits_per_pixel'] == 24 )
			$COLOR = unpack( "V", substr( $IMG, $P, 3 ) . $VIDE );
			elseif ( $BMP['bits_per_pixel'] == 16 ){
				$COLOR = unpack( "n", substr( $IMG, $P, 2 ) );
				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
			}elseif ( $BMP['bits_per_pixel'] == 8 ){
				$COLOR = unpack( "n", $VIDE . substr( $IMG, $P, 1 ) );
				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
			}elseif ( $BMP['bits_per_pixel'] == 4 ){
				$COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
				if ( ($P * 2) % 2 == 0 )
					$COLOR[1] = ($COLOR[1] >> 4);
				else
					$COLOR[1] = ($COLOR[1] & 0x0F);
				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
			}elseif ( $BMP['bits_per_pixel'] == 1 ){
				$COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
				if ( ($P * 8) % 8 == 0 )
					$COLOR[1] = $COLOR[1] >> 7;
				elseif ( ($P * 8) % 8 == 1 )
				$COLOR[1] = ($COLOR[1] & 0x40) >> 6;
				elseif ( ($P * 8) % 8 == 2 )
				$COLOR[1] = ($COLOR[1] & 0x20) >> 5;
				elseif ( ($P * 8) % 8 == 3 )
				$COLOR[1] = ($COLOR[1] & 0x10) >> 4;
				elseif ( ($P * 8) % 8 == 4 )
				$COLOR[1] = ($COLOR[1] & 0x8) >> 3;
				elseif ( ($P * 8) % 8 == 5 )
				$COLOR[1] = ($COLOR[1] & 0x4) >> 2;
				elseif ( ($P * 8) % 8 == 6 )
				$COLOR[1] = ($COLOR[1] & 0x2) >> 1;
				elseif ( ($P * 8) % 8 == 7 )
				$COLOR[1] = ($COLOR[1] & 0x1);
				$COLOR[1] = $PALETTE[$COLOR[1] + 1];
			}else
				return FALSE;
			imagesetpixel( $res, $X, $Y, $COLOR[1] );
			$X++;
			$P += $BMP['bytes_per_pixel'];
		}
		$Y--;
		$P += $BMP['decal'];
	}
	fclose( $f1 );

	return $res;
}
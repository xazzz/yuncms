<?php
/**
 * 日志类
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: Log.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class Log {
	protected static $instance = null;

	/**
	 * 日志保存路径
	 *
	 * @var string
	 */
	protected $log_path;

	/**
	 * 日志级别
	 *
	 * @var int
	 */
	protected $_threshold = 1;

	/**
	 * 时间类型
	 *
	 * @var string
	 */
	protected $_date_fmt = 'Y-m-d H:i:s';

	/**
	 * 日志文件大小
	 *
	 * @var tring
	 */
	protected $log_chunk_size = '2m';

	/**
	 * 是否启用
	 *
	 * @var boolean
	 */
	protected $_enabled = TRUE;

	/**
	 * 等级
	 *
	 * @var array
	 */
	protected $_levels = array ('ERROR' => '1','DEBUG' => '2','INFO' => '3','ALL' => '4' );

	/**
	 * 构造方法
	 */
	public function __construct() {
		$_config = C ( 'log' );
		$this->_log_path = ($_config ['log_path'] != '') ? $_config ['log_path'] : DATA_PATH . 'logs' . DIRECTORY_SEPARATOR;
		if (! is_dir ( $this->_log_path ) or ! is_writable ( $this->_log_path )) { // 设置日志存储路径
			$this->_enabled = FALSE;
		}
		if (is_numeric ( $_config ['log_threshold'] )) { // 设置日志级别
			$this->_threshold = $_config ['log_threshold'];
		}
		if ($_config ['log_date_format'] != null) { // 设置日期格式
			$this->_date_fmt = $_config ['log_date_format'];
		}
		$this->log_chunk_size = ($_config ['log_chunk_size'] != '') ? $_config ['log_chunk_size'] : '2m';
	}

	public static function &get_instance() {
		if (null === self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}

	/**
	 * 记录INFO级别日志
	 *
	 * @param tring $msg
	 */
	public function info($msg) {
		$this->write ( 'INFO', $msg );
	}

	/**
	 * 记录ERROR级别日志
	 *
	 * @param tring $msg
	 */
	public function error($msg) {
		$this->write ( 'ERROR', $msg );
	}

	/**
	 * 记录Debug级别日志
	 *
	 * @param unknown_type $msg
	 */
	public function debug($msg) {
		$this->write ( 'DEBUG', $msg );
	}

	/**
	 * 显示日志内容
	 *
	 * 显示日志文件内容,以列表的形式显示.便于程序调用查看日志
	 *
	 * @param string $filepath 所要显示的日志文件内容,默认为null, 即当天的日志文件名.注:不带后缀名.log
	 * @return void
	 */
	public function show($filepath = null) {
		$filepath = is_null ( $filepath ) ? $this->_log_path . 'log-' . date ( 'Y-m-d' ) . '.log' : $this->_log_path . $filepath . '.log';
		$log_content = File::read ( $filepath );
		$list_str_array = explode ( "\r\n", $log_content );
		unset ( $log_content );
		$total_lines = sizeof ( $list_str_array );
		// 输出日志内容
		echo '<table width="85%" border="0" cellpadding="0" cellspacing="1" style="background:#0478CB; font-size:12px; line-height:25px;">';
		foreach ( $list_str_array as $key => $lines_str ) {
			if ($key == $total_lines - 1) continue;
			$bg_color = ($key % 2 == 0) ? '#FFFFFF' : '#C6E7FF';
			echo '<tr><td height="25" align="left" bgcolor="' . $bg_color . '">&nbsp;' . $lines_str . '</td></tr>';
		}
		echo '</table>';
	}

	/**
	 * 写日志文件
	 *
	 * @param string	错误级别
	 * @param string	错误信息
	 * @return bool
	 */
	public function write($level = 'error', $msg) {
		if ($this->_enabled === FALSE) return FALSE;
		$level = strtoupper ( $level );
		if (! isset ( $this->_levels [$level] ) or ($this->_levels [$level] > $this->_threshold)) {
			return FALSE;
		}
		$filepath = $this->_log_path . 'log-' . date ( 'Y-m-d' ) . '.log';
		// 分析日志文件大小是否超过允许的最大值
		if (is_file ( $filepath ) && filesize ( $filepath ) >= $this->_format_byte ( $this->log_chunk_size )) {
			rename ( $filepath, $this->_log_path . $_SERVER ['REQUEST_TIME'] . '-' . basename ( $filepath ) );
		}
		$message = $level . ' ' . (($level == 'INFO') ? ' -' : '-') . ' ' . date ( $this->_date_fmt ) . ' --> ' . $msg . "\n";
		error_log ( "{$message}\r\n", 3, $filepath );
		return TRUE;
	}

	/**
	 * 从格式话存储单位返回字节
	 *
	 * @param string $val 格式化存储单位
	 */
	private function _format_byte($val) {
		$val = trim ( $val );
		$last = strtolower ( $val {strlen ( $val ) - 1} );
		switch ($last) {
			case 'g' :
				$val *= 1024;
			case 'm' :
				$val *= 1024;
			case 'k' :
				$val *= 1024;
		}
		return $val;
	}
}
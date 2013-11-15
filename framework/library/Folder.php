<?php
/**
 * 文件夹工具类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: Folder.php 682 2013-07-30 03:48:55Z 85825770@qq.com $
 */
class Folder {
	const READ_ALL = '0';
	const READ_FILE = '1';
	const READ_DIR = '2';

	/**
	 * 转义路径
	 *
	 * @param string $path
	 * @return string
	 */
	public static function path($path) {
		return rtrim ( preg_replace ( "|[\/]+|", DIRECTORY_SEPARATOR, $path ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
	}

	/**
	 * 递归创建目录
	 *
	 * @param string $path 目录路径
	 * @param int $permissions 权限
	 * @return boolean
	 */
	public static function mk($path, $permissions = 0777) {
		if (is_dir ( $path ) || $path == '') {
			return true;
		}
		$_path = dirname ( $path );
		if ($_path !== $path) self::mk ( $_path, $permissions );
		return @mkdir ( $path, $permissions );
	}

	/**
	 * 删除目录
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function rm($path) {
		$path = self::path ( $path );
		if (! self::is_dir ( $path )) return false;
		if (! $handle = @opendir ( $path )) return false;
		while ( false !== ($file = readdir ( $handle )) ) {
			if ('.' === $file || '..' === $file) continue;
			$_path = $path . '/' . $file;
			if (self::is_dir ( $_path )) {
				self::rm ( $_path );
			} elseif (File::is_file ( $_path ))
				File::del ( $_path );
		}
		@rmdir ( $path );
		@closedir ( $handle );
		return true;
	}

	/**
	 * 清除文件夹下所有文件以及文件夹
	 *
	 * @param string $dir 目录
	 * @return boolean
	 */
	public static function clear($path) {
		$path = self::path ( $path );
		if (! self::is_dir ( $path )) return false;
		if (! $handle = @opendir ( $path )) return false;
		while ( false !== ($file = readdir ( $handle )) ) {
			if ('.' === $file || '..' === $file) continue;
			$filename = $path . '/' . $file;
			if (self::is_dir ( $filename )) {
				self::rm ( $filename );
			} elseif (File::is_file ( $filename ))
				File::del ( $filename );
		}
		@closedir ( $handle );
		return true;
	}

	/**
	 * 获取文件列表
	 *
	 * @param string $dir
	 * @param boolean $mode 只读取文件列表,不包含文件夹
	 * @return array
	 */
	public static function read($path, $mode = self::READ_ALL) {
		$path = self::path ( $path );
		if (! $handle = @opendir ( $path )) return array ();
		$files = array ();
		while ( false !== ($file = @readdir ( $handle )) ) {
			if ('.' === $file || '..' === $file) continue;
			if ($mode === self::READ_DIR) {
				if (self::is_dir ( $path . '/' . $file )) $files [] = $file;
			} elseif ($mode === self::READ_FILE) {
				if (File::is_file ( $path . '/' . $file )) $files [] = $file;
			} else
				$files [] = $file;
		}
		@closedir ( $handle );
		return $files;
	}

	/**
	 * 移动目录文件到另外一个目录
	 *
	 * @param string $source
	 *        	原目录
	 * @param string $target
	 *        	新目录
	 * @throws Base_Exception
	 */
	public static function move($source, $target) {
		if (! is_dir ( $source )) return false;
		if (! is_dir ( $target )) self::mk ( $target );
		$source = self::path ( $source );
		$target = self::path ( $target );
		$items = glob ( $source . '*' );
		if (! is_array ( $items )) return true;
		foreach ( $items as $v ) {
			$basename = basename ( $v );
			$to = $target . DIRECTORY_SEPARATOR . $basename;
			if (is_dir ( $v )) {
				self::move ( $v, $to );
			} else {
				if (! @rename ( $v, $to )) {
					return false;
				}
			}
		}
		if (! @rmdir ( $source )) throw new Exception ( "can not rmdir $source" );
		return true;
	}

	/**
	 * 拷贝目录及下面所有文件
	 *
	 * @param string $source
	 * @param string $target
	 * @param string $mode
	 * @return string
	 */
	public static function copy($source, $target, $mode = 0755) {
		if (PHP_OS == 'WINNT') $mode = null;
		$source = self::path ( $source );
		$target = self::path ( $target );
		if (! is_dir ( $source )) return false;
		if (! is_dir ( $target )) self::mk ( $target );
		$items = glob ( $source . '*' );
		if (! is_array ( $items )) return true;
		foreach ( $items as $v ) {
			$path = $target . DIRECTORY_SEPARATOR . basename ( $v );
			if (is_dir ( $v )) {
				self::copy ( $v, $path );
			} else {
				if (! @copy ( $v, $path )) {
					return false;
				} else {
					@chmod ( $path, $mode );
				}
			}
		}
		return true;
	}

	public static function chmod($path, $mode = 0755) {
		if (! is_dir ( $path )) return false;
		$mode = intval ( $mode, 8 );
		if (! @chmod ( $path, $mode )) {
			return false;
		}
		$path = self::path ( $path );
		$items = glob ( $path . '*' );
		if (! is_array ( $items )) return true;
		foreach ( $items as $item ) {
			if (is_dir ( $item )) {
				self::chmod ( $item, $mode );
			} else {
				if (! @chmod ( $item, $mode )) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 设置目录下面的所有文件的访问和修改时间
	 *
	 * @param string $path
	 * @param int $mtime
	 * @param int $atime
	 * @return array true
	 */
	static function touch($path, $mtime = TIME, $atime = TIME) {
		if (! is_dir ( $path )) return false;
		if (! @touch ( $path, $mtime, $atime )) {
			return false;
		}
		$path = self::path ( $path );
		if (! is_dir ( $path )) touch ( $path, $mtime, $atime );
		$items = glob ( $path . '*' );
		if (! is_array ( $items )) return true;
		foreach ( $items as $item ) {
			if (is_dir ( $item )) {
				self::touch ( $path, $mtime, $atime );
			} else {
				if (! @touch ( $item, $mtime, $atime )) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 目录列表
	 *
	 * @param string $path
	 * @param int $parentid
	 * @param array $dirs
	 * @return array
	 */
	public static function tree($path, $parentid = 0, $dirs = array()) {
		global $id;
		if ($parentid == 0) $id = 0;
		if (! is_dir ( $path )) return false;
		$path = self::path ( $path );
		$items = glob ( $path . '*' );
		if (! is_array ( $items )) return $dirs;
		foreach ( $items as $item ) {
			if (is_dir ( $item )) {
				$id ++;
				$dirs [$id] = array ('id' => $id,'parentid' => $parentid,'name' => basename ( $item ),'dir' => $item . '/' );
				$dirs = self::tree ( $item, $id, $dirs );
			}
		}
		return $dirs;
	}

	/**
	 * 判断输入是否为目录
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function is_dir($dir) {
		return $dir ? is_dir ( $dir ) : false;
	}

	/**
	 * 取得目录信息
	 *
	 * @param string $dir 目录路径
	 * @return array
	 */
	public static function get_info($dir) {
		return self::is_dir ( $dir ) ? stat ( $dir ) : array ();
	}
}
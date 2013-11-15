<?php
/**
 * Loader.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: Loader.php 735 2013-08-08 09:30:54Z 85825770@qq.com $
 */
class Loader {
	private static $instances = array ();

	/**
	 * 加载Session
	 */
	public static function session() {
		if (! isset ( self::$instances ['session'] )) {
			Session_Abstract::get_instance ( C ( 'session' ) );
			if (isset ( $_GET ['SID'] ) && ! empty ( $_GET ['SID'] )) session_id ( trim ( $_GET ['SID'] ) );
			session_start ();
			define ( 'SID', session_id () );
			self::$instances ['session'] = true;
		}
		return self::$instances ['session'];
	}

	/**
	 * 加载模型
	 *
	 * @param $model
	 */
	public static function &model($file_path, $initialize = true) {
		if (! $file_path) return;
		if (isset ( self::$instances ['model'] [$file_path] )) return self::$instances ['model'] [$file_path];
		$file_name = Core::import ( $file_path, WEKIT_PATH . 'model' . DIRECTORY_SEPARATOR );
		if (! $initialize) return true;
		self::$instances ['model'] [$file_path] = Core::get_instance ( $file_name );
		return self::$instances ['model'] [$file_path];
	}

	/**
	 * 加载助手
	 *
	 * @param string $filePath 文件路径
	 */
	public static function helper($filePath) {
		if (! $filePath) return;
		if (isset ( self::$instances ['helper'] [$filePath] )) return self::$instances ['helper'] [$filePath];
		if (($pos = strrpos ( $filePath, ':' )) !== false) {
			self::$instances ['helper'] [$filePath] = Core::import ( substr ( $filePath, 0, $pos ) . ':' . 'helper.' . substr ( $filePath, $pos + 1 ) );
		} else {
			self::$instances ['helper'] [$filePath] = Core::import ( 'helper.' . $filePath );
		}
		return self::$instances ['helper'] [$filePath];
	}

	/**
	 * 加载类库
	 *
	 * @param string $filePath 类路径
	 * @param bool $initialize 是否自动实例化
	 */
	public static function lib($filePath, $initialize = true) {
		if (! $filePath) return;
		if (isset ( self::$instances [$filePath] )) return self::$instances [$filePath];
		if (($pos = strrpos ( $filePath, ':' )) !== false) {
			$fileName = Core::import ( substr ( $filePath, 0, $pos ) . ':' . 'library.' . substr ( $filePath, $pos + 1 ) );
		} else {
			$fileName = Core::import ( 'library.' . $filePath );
		}
		if (! $initialize) return true;
		self::$instances [$filePath] = Core::get_instance ( $fileName );
		return self::$instances [$filePath];
	}

	/**
	 * 加载模块类
	 *
	 * @param string $filePath 类路径
	 * @param bool $initialize 是否自动实例化
	 */
	public static function plugin($filePath, $initialize = true) {
		if (! $filePath) return;
		if (isset ( self::$instances [$filePath] )) return self::$instances [$filePath];
		$fileName = Core::import ($filePath,PLUGIN_PATH );
		if (! $initialize) return true;
		self::$instances [$filePath] = Core::get_instance ( $fileName );
		return self::$instances [$filePath];
	}
}
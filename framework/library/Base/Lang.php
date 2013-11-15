<?php
/**
 * 核心语言类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-24
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Lang.php 705 2013-08-01 00:41:52Z 85825770@qq.com $
 */
class Base_Lang {
	protected static $instance = null;
	protected $app_lang = array ();
	public static function &instance() {
		if (null === self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	public function __construct() {
		if (! defined ( 'LANG' )) define ( 'LANG', C ( 'config', 'lang', 'zh-cn' ) );
	}

	/**
	 * 加载语言包
	 */
	public function load($language = 'NO_LANG', $pars = array(), $applications = '') {
		static $LANG = array ();
		if (! $LANG) {
			// 加载框架语言包
			require_once FW_PATH . 'language' . DIRECTORY_SEPARATOR . LANG . '.php';
			require_once WEKIT_PATH . 'languages' . DIRECTORY_SEPARATOR . LANG . DIRECTORY_SEPARATOR . 'system.php';
			if (defined ( 'IN_ADMIN' )) require_once WEKIT_PATH . 'languages' . DIRECTORY_SEPARATOR . LANG . DIRECTORY_SEPARATOR . 'admin_menu.php';
			if (defined ( 'APP' ) && file_exists ( WEKIT_PATH . 'languages' . DIRECTORY_SEPARATOR . LANG . DIRECTORY_SEPARATOR . APP . '.php' )) require WEKIT_PATH . 'languages' . DIRECTORY_SEPARATOR . LANG . DIRECTORY_SEPARATOR . APP . '.php';
		}
		// pay.alipay:name形式调用语言包
		if (strrpos ( $language, ':' ) !== false) {
			list ( $applications, $language ) = explode ( ':', $language, 2 );
			if(strrpos ( $applications, '.' ) !== false) {
				$applications = str_replace ( '.', DIRECTORY_SEPARATOR, $applications );
			}
		}
		if (! empty ( $applications )) {
			$applications = explode ( ',', $applications );
			foreach ( $applications as $app ) {
				if (! isset ( $this->app_lang [$app] )) {
					require WEKIT_PATH . 'languages' . DIRECTORY_SEPARATOR . LANG . DIRECTORY_SEPARATOR . $app . '.php';
					$this->app_lang [$app] = true;
				}
			}
		}
		if (! array_key_exists ( $language, $LANG )) {
			$return = $LANG ['NO_LANG'] . '[' . $language . ']';
			log_message ( 'error', APP . ':' . $return );
			return $return;
		} else {
			$language = $LANG [$language];
			if ($pars) {
				foreach ( $pars as $_k => $_v ) {
					$language = str_replace ( '{' . $_k . '}', $_v, $language );
				}
			}
			return $language;
		}
	}
}
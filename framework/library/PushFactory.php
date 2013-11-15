<?php
/**
 * 推送信息工厂类
 * @author Tongle Xu <xutongle@gmail.com> 2012-11-13
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: PushFactory.php 386 2012-11-13 10:06:01Z xutongle $
 */
final class PushFactory {

	/**
	 * 推送信息工厂类静态实例
	 */
	private static $push_factory;

	/**
	 * 接口实例化列表
	 */
	protected $api_list = array ();

	/**
	 * 返回当前终级类对象的实例
	 *
	 * @return object
	 */
	public static function get_instance() {
		if (self::$push_factory == '') {
			self::$push_factory = new PushFactory ();
		}
		return self::$push_factory;
	}

	/**
	 * 获取api操作实例
	 *
	 * @param string $classname
	 *        	接口调用的类文件名
	 * @param sting $application
	 *        	应用名
	 * @return object
	 */
	public function get_api($application = 'admin') {
		if (! isset ( $this->api_list [$application] ) || ! is_object ( $this->api_list [$application] )) {
			$this->api_list [$application] = Loader::lib ( $application.':push_api' );
		}
		return $this->api_list [$application];
	}
}
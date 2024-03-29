<?php
/**
 * 应用管理
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: application_model.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class application_model extends Model {
	public $table_name = '';
	function __construct() {
		$this->setting = 'default';
		$this->table_name = 'application';
		parent::__construct ();
	}

	public function set_setting($application, $setting = null) {
		if(!is_null ( $setting )){
			return $this->where ( array ('application' => $application ) )->update ( array ('setting' => array2string ( $setting ) ) );
		}
		return false;
	}

	/**
	 * 获取应用配置
	 * @param string $application
	 * @return mixed|boolean
	 */
	public function get_setting($application) {
		$result = $this->where ( array ('application' => $application ) )->find ();
		if (! empty ( $result ['setting'] )) {
			return string2array ( $result ['setting'] );
		}
		return false;
	}
}
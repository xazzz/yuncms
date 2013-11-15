<?php
/**
 * 命令行下的路由解析
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: Router.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class Command_Router extends Base_Router {
	protected $pattern ='/^(\w+)?(\/\w+)?.*$/i';
	protected $reverse = '/%s/%s';
	protected $separator = '&=';
	protected $params = array ('action' => 2,'controller' => 1 );

	/**
	 * (non-PHPdoc)
	 *
	 * @see Base_Router::_initialize()
	 */
	public function _initialize() {
		$this->_config = C ( 'router', 'command' );
		$this->_pathinfo = trim(Base_Request::parse_cli_args(),'/');
	}
}
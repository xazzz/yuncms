<?php
/**
 * IndexController.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: IndexController.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class IndexController {
	public function init(){
		Web_Response::set_status(400);
		Web_Response::send_headers();
	}
}
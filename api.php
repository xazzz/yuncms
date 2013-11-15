<?php
/**
 * api.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: api.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
error_reporting ( E_ALL );//开发
//Error_reporting(E_ERROR | E_WARNING | E_PARSE);//线上
require_once 'src/wekit.php';
Wekit::run('Api');
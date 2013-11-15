<?php
/**
 * 系统队列配置文件
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-14
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: queue.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
return array(
		'default' => array (
				'driver' => 'mysql',
				'option'=>'queue_model',//所加载的数据模型
		),

		'default1' => array (
				'driver' => 'httpsqs',
				'server' =>'192.168.2.158',
				'port' =>'1218',
				'auth'=>'abc',
		)
);

<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-10
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: router.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
return array (
		'api'=>array(
				'controller' =>'index',
				'action' => 'init'
		),
		'command'=>array(
				'controller' =>'index',
				'action' => 'init'
		),
		'default' => array (
				'application' => 'content',
				'controller' => 'index',
				'action' => 'init',
				'data' => array (
						'POST' => array (
								'catid' => 1
						),
						'GET' => array (
								'contentid' => 1
						)
				)
		),
		'api.tintsoft.com'=>array(

		),//二级域名
);
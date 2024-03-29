<?php
/**
 * config.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: config.php 740 2013-08-13 02:18:43Z 85825770@qq.com $
 */
return array (
		//核心设置
		'isclosed'=>false,
		'charset'=>'UTF-8',
		'lang'=>'zh-cn',
		'timezone'=>'Etc/GMT-8',
		'lock_ex'=>true,//文件读写互斥锁
		'gzip' => true, // 是否Gzip压缩后输出
		'auth_key' => 'SmvFcMasPjpeMslmZhWp',// 加密随机符
		'show_time' => true,// 显示运行时间
		'show_trace' => true,// 显示trace信息
		'trace_exception'=>true,//trace错误信息是否抛出异常 目前仅数据库有效

		/* 数据库设置 */
		'db_sql_build_cache'=>true, //开启SQL编译缓存
		'db_sql_log'=>true,
		'db_fields_cache'=>true, //数据字段缓存
		'db_fields_version'=>1,
		'db_cache_expire'=>1, // 数据库查询操作的默认缓存时间单位：秒
		'db_cache_setting'=>'default', // 数据库缓存所加载的配置

		/* 错误设置 */
		'debug' => true,
		'firephp' => true,
		'error_message' => '服务器被外星人劫持。。。。',//错误信息
		'error_page' => '', //错误页面优先级高于错误信息
		'show_error_msg' => true,

		/* 默认设定 */
		'default_ajax_return'=>'json',//AJAX默认返回
		'default_ajax_submit'=>'ajax',//Ajax默认提交参数
		'default_jsonp_callback'=>'callback',//jsonp默认回调函数 在URL中自定义需get callback
		'default_jsonp_handler' => 'jsonpReturn', // 默认JSONP格式返回的处理方法

		'url_model'=>0,//URL 模式 0 普通模式 1 URL友好模式（需服务器支持）
);
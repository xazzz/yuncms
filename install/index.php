<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-20
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: index.php 737 2013-08-12 15:47:39Z 85825770@qq.com $
 */
@set_time_limit ( 1000 );
if (phpversion () < '5.2.0') exit ( '您的php版本过低，不能安装本软件，请升级到5.2.0或更高版本再安装，谢谢！' );
defined ( 'BASE_PATH' ) or define ( 'BASE_PATH', dirname ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) ) . DIRECTORY_SEPARATOR );
require_once '../src/wekit.php';
define ( 'INS_PATH', BASE_PATH . 'install' . DIRECTORY_SEPARATOR );
if(file_exists(DATA_PATH.'install.lock')) exit('您已经安装过YUNCMS,如果需要重新安装，请删除 '.DATA_PATH.'install.lock 文件！');
require_once INS_PATH . 'global.php';
$steps = include INS_PATH . 'conf/step.php';
$step = isset ( $_REQUEST ['step'] ) ? trim ( $_REQUEST ['step'] ) : 1;
$pos = strpos ( Base_Request::get_url (), 'install/' );
$siteurl = substr ( Base_Request::get_url (), 0, $pos );
if (strrpos ( strtolower ( PHP_OS ), "win" ) === FALSE)
	define ( 'ISUNIX', TRUE );
else
	define ( 'ISUNIX', FALSE );

switch ($step) {
	case '1' : // 安装许可协议
		$license = file_get_contents ( INS_PATH . "resource/license.txt" );
		include INS_PATH . "step/step_" . $step . ".tpl.php";
		break;
	case '2' : // 环境检测 (FTP帐号设置）
		$PHP_GD = '';
		if (extension_loaded ( 'gd' )) {
			if (function_exists ( 'imagepng' )) $PHP_GD .= 'png';
			if (function_exists ( 'imagejpeg' )) $PHP_GD .= ' jpg';
			if (function_exists ( 'imagegif' )) $PHP_GD .= ' gif';
		}
		$PHP_JSON = '0';
		if (extension_loaded ( 'json' )) {
			if (function_exists ( 'json_decode' ) && function_exists ( 'json_encode' )) $PHP_JSON = '1';
		}
		$PHP_DNS = preg_match ( "/^[0-9.]{7,15}$/", @gethostbyname ( 'www.tintsoft.com' ) ) ? 1 : 0;
		$is_right = (phpversion () >= '5.2.0' && extension_loaded ( 'mysql' ) && $PHP_JSON && $PHP_GD) ? 1 : 0;
		include INS_PATH . "step/step_" . $step . ".tpl.php";
		break;
	case '3' : // 选择安装模块
		include INS_PATH . "conf" . DIRECTORY_SEPARATOR . "apps.php";
		include INS_PATH . "step/step_" . $step . ".tpl.php";
		break;
	case '4' : // 检测目录属性
		$selectapp = $_POST ['selectapp'];
		$testdata = isset ( $_POST ['testdata'] ) ? $_POST ['testdata'] : 0;
		$selectapp = isset ( $selectapp ) ? ',' . implode ( ',', $selectapp ) : '';
		$needapp = 'admin';
		$selectapp = $needapp . $selectapp;
		$selectapps = explode ( ',', $selectapp );
		$files = include INS_PATH . "conf" . DIRECTORY_SEPARATOR . "chmod.php";
		$no_writablefile = 0;
		foreach ( $files as $_k => $file ) {
			$file = str_replace ( '*', '', $file );
			$file = trim ( $file );
			if (is_dir ( BASE_PATH . $file )) {
				$is_dir = '1';
				$cname = '目录';
			} else {
				$is_dir = '0';
				$cname = '文件';
			}
			if ($is_dir == '0' && is_writable ( BASE_PATH . $file )) {
				$is_writable = 1;
			} elseif ($is_dir == '1' && dir_writeable ( BASE_PATH . $file )) {
				$is_writable = 1;
			} else {
				$is_writable = 0;
				$no_writablefile = 1;
			}
			$filesapp [$_k] ['file'] = $file;
			$filesapp [$_k] ['is_dir'] = $is_dir;
			$filesapp [$_k] ['cname'] = $cname;
			$filesapp [$_k] ['is_writable'] = $is_writable;
		}
		if (dir_writeable ( BASE_PATH )) {
			$is_writable = 1;
		} else {
			$is_writable = 0;
		}
		$filesapp [$_k + 1] ['file'] = '网站根目录';
		$filesapp [$_k + 1] ['is_dir'] = '1';
		$filesapp [$_k + 1] ['cname'] = '目录';
		$filesapp [$_k + 1] ['is_writable'] = $is_writable;
		include INS_PATH . "step/step_" . $step . ".tpl.php";
		break;
	case '5' : // 配置帐号 （MYSQL帐号、管理员帐号、）
		$database = C ( 'database', 'default' );
		$testdata = $_POST ['testdata'];
		$selectapp = $_POST ['selectapp'];
		include INS_PATH . "step/step_" . $step . ".tpl.php";
		break;
	case '6' : // 安装详细过程
		$db_config = array ('hostname' => $_POST ['dbhost'],'username' => $_POST ['dbuser'],'password' => $_POST ['dbpw'],'database' => $_POST ['dbname'],'prefix' => $_POST ['prefix'],'pconnect' => $_POST ['pconnect'],'charset' => $_POST ['dbcharset'] );
		Base_Config::modify ( 'database', $db_config ); // 写入数据库配置信息

		Base_Config::modify ( 'cookie', array ('prefix' => String::rand_string ( 5 ) . '_' ) );// Cookie前缀
		// 附件访问路径
		Base_Config::modify ( 'attachment', array ('upload_url' => $siteurl . 'data/attachment/' ) );
		Base_Config::modify ( 'attachment', array ('avatar_url' => $siteurl . 'data/avatar/' ) );
		$auth_key = String::rand_string( 20 );
		Base_Config::modify ( 'config', array ('auth_key' => $auth_key ) );
		$uuid = String::uuid ( C ( 'version', 'product' ) . '-' );
		Base_Config::modify ( 'version', array ('uuid' => $uuid ) );
		Base_Config::modify ( 'system', array ('js_path' => $siteurl . 'statics/js/','css_path' => $siteurl . 'statics/css/','img_path' => $siteurl . 'statics/images/','app_path' => $siteurl,'skin_path' => $siteurl . 'statics/skins/') );
		$selectapp = $_POST ['selectapp'];
		$testdata = $_POST ['testdata'];
		include INS_PATH . "step/step_" . $step . ".tpl.php";
		break;
	case '7' : // 完成安装
		//File::write ( DATA_PATH . 'install.lock', 'ok' );
		include INS_PATH . "step/step_" . $step . ".tpl.php";
		//Folder::rm(INS_PATH);//删除安装目录
		break;

	case 'installapp' : // 执行SQL
		if ($_POST ['app'] == 'admin') {
			$dbfile = 'yuncms_db.sql';
			if (file_exists ( INS_PATH . "resource" . DIRECTORY_SEPARATOR . $dbfile )) {
				$database = C ( 'database', 'default' );
				$sql = File::read ( INS_PATH . "resource" . DIRECTORY_SEPARATOR . $dbfile );
				$sql = str_replace ( '#table#', $database ['prefix'], $sql );
				$sql = preg_replace ( "/ENGINE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=" . $database ['charset'], $sql );
				_sql_execute ( $sql );
				// 创建网站创始人
				if (CHARSET == 'gbk') $_POST ['username'] = iconv ( 'UTF-8', 'GBK', $_POST ['username'] );
				$password_arr = password ( $_POST ['password'] );
				$password = $password_arr ['password'];
				$encrypt = $password_arr ['encrypt'];
				$info = array ('username' => trim ( $_POST ['username'] ),'password' => $password,'roleid' => 1,'encrypt' => $encrypt,'email' => trim ( $_POST ['email'] ) );
				Loader::model ( 'admin_model' )->insert ( $info );
			} else {
				echo '2'; // 数据库文件不存在
			}
		} else {
			// 安装可选模块
			if (in_array ( $_POST ['app'], array ('announce','comment','link','vote','message','mood','poster','formguide','sms','wap','tag','maillist' ) )
			) {
				$install_app = Loader::lib ( 'admin:application_api' );
				$install_app->install ( $_POST ['app'] );
			}
		}
		echo $_POST ['app'];
		break;

	case 'testdata' : // 安装测试数据
		$database = C ( 'database', 'default' );
		if (file_exists ( INS_PATH . "resource" . DIRECTORY_SEPARATOR . "testsql.sql" )) {
			$sql = file_get_contents ( INS_PATH . "resource" . DIRECTORY_SEPARATOR . "testsql.sql" );
			$sql = str_replace ( '#table#', $database ['prefix'], $sql );
			_sql_execute ( $sql );
		}
		break;

	case 'cache_all' : // 更新缓存
		$cache = Loader::lib ( 'admin:cache_api' );
		$cache->cache ( 'model' );
		$cache->cache ( 'category' );
		$cache->cache ( 'downserver' );
		$cache->cache ( 'badword' );
		$cache->cache ( 'ipbanned' );
		$cache->cache ( 'keylink' );
		$cache->cache ( 'linkage' );
		$cache->cache ( 'position' );
		$cache->cache ( 'admin_role' );
		$cache->cache ( 'urlrule' );
		$cache->cache ( 'application' );
		$cache->cache ( 'workflow' );
		$cache->cache ( 'dbsource' );
		$cache->cache ( 'member_group' );
		$cache->cache ( 'member_model' );
		$cache->cache ( 'type', 'search' );
		$cache->cache ( 'special' );
		$cache->cache ( 'setting' );
		$cache->cache ( 'database' );
		$cache->cache ( 'member_setting' );
		$cache->cache ( 'member_model_field' );
		$cache->cache ( 'search_setting' );
		copy ( INS_PATH . "resource" . DIRECTORY_SEPARATOR . "cms_index.html", BASE_PATH . "index.html" );
		break;
	case 'dbtest' : // 数据库测试
		if (! @mysql_connect ( $_GET ['dbhost'], $_GET ['dbuser'], $_GET ['dbpw'] )) exit ( '2' );
		$server_info = mysql_get_server_info ();
		if ($server_info < '4.0') exit ( '6' );
		if (! mysql_select_db ( $_GET ['dbname'] )) {
			if (! @mysql_query ( "CREATE DATABASE `{$_GET['dbname']}`" )) exit ( '3' );
			mysql_select_db ( $_GET ['dbname'] );
		}
		$tables = array ();
		$query = mysql_query ( "SHOW TABLES FROM `{$_GET['dbname']}`" );
		while ( $r = mysql_fetch_row ( $query ) ) {
			$tables [] = $r [0];
		}
		if ($tables && in_array ( $_GET ['prefix'] . 'application', $tables ))
			exit ( '0' );
		else
			exit ( '1' );
		break;
}
<?php
/**
 * custom.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: custom.php 735 2013-08-08 09:30:54Z 85825770@qq.com $
 */
define ( 'CUSTOM_FUNCTION', true );
/**
 * 判断验证码是否正确
 *
 * @param string $checkcode
 */
function checkcode($checkcode = '') {
	Loader::session (); // 加载Session
	if (! empty ( $checkcode ) && (isset ( $_SESSION ['code'] ) && $_SESSION ['code'] == strtolower ( $checkcode ))) return true;
	return false;
}

/**
 * 获取IP地址归属地
 *
 * @param string $ip
 * @return string
 */
function ip_source($ip) {
	return IpSource::instance ()->get ( $ip );
}

/**
 * 生成二维码
 *
 * @param string $value 数据
 * @param string $level 纠错级别：L、M、Q、H
 * @param int $size 每个黑点的像素：1到10,用于手机端4就可以了
 * @param int $margin 图片外围的白色边框像素
 * @param bool $saveandprint 保存并打印
 */
function QRcode($value, $level = 'L', $size = 4, $margin = 4) {
	Loader::lib ( 'QRcode.QRcode', false );
	return QRcode::png ( $value, false, $level, $size, $margin );
}

/**
 * 发送电子邮件
 *
 * @param srting $toemail 要发送到的邮箱多个逗号隔开
 * @param srting $subject 邮件标题
 * @param srting $message 邮件内容
 * @param srting $from
 */
function sendmail($toemail, $subject, $message, $from = '') {
	static $mail = null;
	if (null === $mail) $mail = Loader::lib ( 'Mail' );
	return $mail->send ( $toemail, $subject, $message, $from );
}

/**
 * 发送手机短信
 *
 * @param int $mobile 手机号
 * @param string $content 内容
 */
function sendsms($mobile, $content) {
	static $sms = null;
	if ($sms === null) $sms = Factory::sms ();
	if ($sms->send ( $mobile, $content )) return true;
	return false;
}

/**
 * 新浪短连接生成
 * @param string $url 要缩短的连接
 * @param string $key App_key
 * @return boolean
 */
function short_url($url, $app_key = '879062653') {
	static $http = null;
	if ($http == null) $http = Loader::lib ( 'HttpClient' );
	$result = $http->get("https://api.weibo.com/2/short_url/shorten.json?source=$app_key&url_long=$url");
	$data = json_decode ($result->data(), true );
	if(isset($data['error'])) return false;
	else return $data['urls'][0]['url_short'];
}

/**
 * 中文字符转拼音
 *
 * @param string $str
 * @param string $utf8
 */
function string_to_pinyin($str, $utf8 = true) {
	static $obj = null;
	if ($obj === null) $obj = new Pinyin ();
	return $obj->output ( $str, $utf8 );
}

/**
 * 系统视图类 继承 视图类
 *
 * @param $$application 应用名称
 * @param $template 模版名称
 * @param $style 视图风格名称
 */
function template($application = 'index', $template = 'index', $style = '') {
	if (! empty ( $style ) && preg_match ( '/([a-z0-9\-_]+)/is', $style )) {
	} elseif (empty ( $style ) && defined ( 'STYLE' )) {
		$style = STYLE;
	} else {
		$style = C ( 'template', 'name' );
	}
	if (empty ( $style )) $style = 'default';
	$compiledtplfile = Template::instance ()->compile ( $template, $application, $style );
	return $compiledtplfile;
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.yuncms.net'));
 *
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
	if ($ms == 301) {
		Loader::session ();
		$_SESSION ['msg'] = $msg;
		Header ( "HTTP/1.1 301 Moved Permanently" );
		Header ( "Location: $url_forward" );
		exit ();
	}
	if (defined ( 'IN_ADMIN' )) {
		include (Web_Admin::view ( 'showmessage', 'admin' ));
	} else {
		include (template ( 'yuncms', 'message' ));
	}
	if (isset ( $_SESSION ['msg'] )) unset ( $_SESSION ['msg'] );
	exit ();
}

/**
 * 对用户的密码进行加密
 *
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt = '') {
	$pwd = array ();
	$pwd ['encrypt'] = $encrypt ? $encrypt : String::rand_string ( 6 );
	$pwd ['password'] = md5 ( md5 ( trim ( $password ) ) . $pwd ['encrypt'] );
	return $encrypt ? $pwd ['password'] : $pwd;
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace ( '%20', '', $string );
	$string = str_replace ( '%27', '', $string );
	$string = str_replace ( '%2527', '', $string );
	$string = str_replace ( '*', '', $string );
	$string = str_replace ( '"', '&quot;', $string );
	$string = str_replace ( "'", '', $string );
	$string = str_replace ( '"', '', $string );
	$string = str_replace ( ';', '', $string );
	$string = str_replace ( '<', '&lt;', $string );
	$string = str_replace ( '>', '&gt;', $string );
	$string = str_replace ( "{", '', $string );
	$string = str_replace ( '}', '', $string );
	$string = str_replace ( '\\', '', $string );
	return $string;
}

/**
 * 生成上传附件验证
 *
 * @param $args 参数
 * @param $operation 操作类型(加密解密)
 */
function upload_key($args, $operation = 'ENCODE') {
	$authkey = md5 ( md5 ( C ( 'config', 'auth_key' ) . $_SERVER ['HTTP_USER_AGENT'] ) );
	return $authkey;
}

/**
 * 将文件大小以字节(bytes)格式化，并添加适合的缩写单位。
 *
 * @param string $filesize
 * @return string
 */
function byte_format($filesize) {
	if ($filesize >= 1073741824) {
		$filesize = round ( $filesize / 1073741824 * 100 ) / 100 . ' GB';
	} elseif ($filesize >= 1048576) {
		$filesize = round ( $filesize / 1048576 * 100 ) / 100 . ' MB';
	} elseif ($filesize >= 1024) {
		$filesize = round ( $filesize / 1024 * 100 ) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' Bytes';
	}
	return $filesize;
}
function get_keywords($data, $number = 3) {
	$data = trim ( strip_tags ( $data ) );
	if (empty ( $data )) return '';
	if (strtolower ( CHARSET ) != 'utf-8') {
		$data = iconv ( 'utf-8', CHARSET, $data );
	} else {
		$data = iconv ( 'utf-8', 'gbk', $data );
	}
	$result = Loader::lib ( 'HttpClient' )->post ( 'http://tool.phpcms.cn/api/get_keywords.php', array ('siteurl' => SITE_URL,'charset' => CHARSET,'data' => $data,'number' => $number ) );
	if ($result) {
		if (strtolower ( CHARSET ) != 'utf-8') {
			return $result;
		} else {
			return iconv ( 'gbk', 'utf-8', $result );
		}
	}
	return '';
}

/**
 * 水印添加
 *
 * @param $source 原图片路径
 * @param $target 生成水印图片途径，默认为空，覆盖原图
 */
function watermark($source, $target = '') {
	static $image = null;
	if (empty ( $source )) return $source;
	if (! extension_loaded ( 'gd' ) || strpos ( $source, '://' )) return $source;
	if (! $target) $target = $source;
	if ($image == null) $image = new Image ( 0 );
	$image->watermark ( $source, $target );
	return $target;
}

/**
 * 生成缩略图函数
 *
 * @param $imgurl 图片路径
 * @param $width 缩略图宽度
 * @param $height 缩略图高度
 * @param $autocut 是否自动裁剪 默认裁剪，当高度或宽度有一个数值为0是，自动关闭
 * @param $smallpic 无图片是默认图片路径
 */
function thumb($imgurl, $width = 100, $height = 100, $autocut = 1, $smallpic = 'nopic.gif') {
	static $image = null;
	$upload_url = C ( 'attachment', 'upload_url' );
	$upload_path = C ( 'attachment', 'upload_path' );
	if (empty ( $imgurl )) return IMG_PATH . $smallpic;
	$imgurl_replace = str_replace ( $upload_url, '', $imgurl );
	if (! extension_loaded ( 'gd' ) || strpos ( $imgurl_replace, '://' )) return $imgurl;
	if (! file_exists ( $upload_path . $imgurl_replace )) return IMG_PATH . $smallpic;
	list ( $width_t, $height_t, $type, $attr ) = getimagesize ( $upload_path . $imgurl_replace );
	if ($width >= $width_t || $height >= $height_t) return $imgurl;
	$newimgurl = dirname ( $imgurl_replace ) . '/thumb_' . $width . '_' . $height . '_' . basename ( $imgurl_replace );
	if (file_exists ( $upload_path . $newimgurl )) return $upload_url . $newimgurl;
	if ($image == null) $image = new Image ( 1 );
	return $image->thumb ( $upload_path . $imgurl_replace, $upload_path . $newimgurl, $width, $height, '', $autocut ) ? $upload_url . $newimgurl : $imgurl;
}

/**
 * 判断应用是否安装
 *
 * @param $app 应用名称
 */
function app_exists($application = '') {
	if ($application == 'admin') return true;
	$applications = S ( 'common/application' );
	$applications = array_keys ( $applications );
	return in_array ( $application, $applications );
}

/**
 * 将文本格式成适合js输出的字符串
 *
 * @param string $string 需要处理的字符串
 * @param intval $isjs 是否执行字符串格式化，默认为执行
 * @return string 处理后的字符串
 */
function format_js($string, $isjs = 1) {
	$string = addslashes ( str_replace ( array ("\r","\n" ), array ('','' ), $string ) );
	return $isjs ? 'document.write("' . $string . '");' : $string;
}

/**
 * 生成SEO
 *
 * @param $catid 栏目ID
 * @param $title 标题
 * @param $description 描述
 * @param $keyword 关键词
 */
function seo($catid = '', $title = '', $description = '', $keyword = '') {
	if (! empty ( $title )) $title = strip_tags ( $title );
	if (! empty ( $description )) $description = strip_tags ( $description );
	if (! empty ( $keyword )) $keyword = str_replace ( ' ', ',', strip_tags ( $keyword ) );
	$site = S ( 'common/common' );
	$cat = array ();
	if (! empty ( $catid )) {
		$categorys = S ( 'common/category_content' );
		$cat = $categorys [$catid];
		$cat ['setting'] = string2array ( $cat ['setting'] );
	}
	$seo ['site_title'] = isset ( $site ['site_title'] ) && ! empty ( $site ['site_title'] ) ? $site ['site_title'] : $site ['name'];
	$seo ['keyword'] = ! empty ( $keyword ) ? $keyword : $site ['keywords'];
	$seo ['description'] = isset ( $description ) && ! empty ( $description ) ? $description : (isset ( $cat ['setting'] ['meta_description'] ) && ! empty ( $cat ['setting'] ['meta_description'] ) ? $cat ['setting'] ['meta_description'] : (isset ( $site ['description'] ) && ! empty ( $site ['description'] ) ? $site ['description'] : ''));
	$seo ['title'] = (isset ( $title ) && ! empty ( $title ) ? $title . ' - ' : '') . (isset ( $cat ['setting'] ['meta_title'] ) && ! empty ( $cat ['setting'] ['meta_title'] ) ? $cat ['setting'] ['meta_title'] . ' - ' : (isset ( $cat ['catname'] ) && ! empty ( $cat ['catname'] ) ? $cat ['catname'] . ' - ' : ''));
	foreach ( $seo as $k => $v ) {
		$seo [$k] = str_replace ( array ("\n","\r" ), '', $v );
	}
	return $seo;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
function trim_script($str) {
	if (is_array ( $str )) {
		foreach ( $str as $key => $val ) {
			$str [$key] = trim_script ( $val );
		}
	} else {
		$str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
		$str = preg_replace ( '/]]\>/si', ']] >', $str );
	}
	return $str;
}

/**
 * 判断是否启用UCenter
 */
function ucenter_exists() {
	$uc_config = C ( 'system', 'ucenter' );
	if ($uc_config == 1) return true;
	return false;
}

/**
 * 字符截取 支持UTF8/GBK
 *
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen ( $string );
	if ($strlen <= $length) return $string;
	$string = str_replace ( array (' ','&nbsp;','&amp;','&quot;','&#039;','&ldquo;','&rdquo;','&mdash;','&lt;','&gt;','&middot;','&hellip;' ), array ('∵',' ','&','"',"'",'“','”','—','<','>','·','…' ), $string );
	$strcut = '';
	if (strtolower ( CHARSET ) == 'utf-8') {
		$length = intval ( $length - strlen ( $dot ) - $length / 3 );
		$n = $tn = $noc = 0;
		while ( $n < strlen ( $string ) ) {
			$t = ord ( $string [$n] );
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n ++;
				$noc ++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n ++;
			}
			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr ( $string, 0, $n );
		$strcut = str_replace ( array ('∵','&','"',"'",'“','”','—','<','>','·','…' ), array (' ','&amp;','&quot;','&#039;','&ldquo;','&rdquo;','&mdash;','&lt;','&gt;','&middot;','&hellip;' ), $strcut );
	} else {
		$dotlen = strlen ( $dot );
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array ('&',' ','"',"'",'“','”','—','<','>','·','…','∵' );
		$replace_arr = array ('&amp;','&nbsp;','&quot;','&#039;','&ldquo;','&rdquo;','&mdash;','&lt;','&gt;','&middot;','&hellip;',' ' );
		$search_flip = array_flip ( $search_arr );
		for($i = 0; $i < $maxi; $i ++) {
			$current_str = ord ( $string [$i] ) > 127 ? $string [$i] . $string [++ $i] : $string [$i];
			if (in_array ( $current_str, $search_arr )) {
				$key = $search_flip [$current_str];
				$current_str = str_replace ( $search_arr [$key], $replace_arr [$key], $current_str );
			}
			$strcut .= $current_str;
		}
	}
	return $strcut . $dot;
}

/**
 * 文本转换为图片
 *
 * @param string $txt 图形化文本内容
 * @param int $fonttype 无外部字体时生成文字大小，取值范围1-5
 * @param int $fontsize 引入外部字体时，字体大小
 * @param string $font 字体名称 字体请放于phpcms\libs\data\font下
 * @param string $fontcolor 字体颜色 十六进制形式 如FFFFFF,FF0000
 *
 */
function string2img($txt, $fonttype = 5, $fontsize = 16, $font = '', $fontcolor = 'FF0000', $transparent = '1') {
	if (empty ( $txt )) return false;
	if (function_exists ( "imagepng" )) {
		$txt = urlencode ( String::authcode ( $txt ) );
		$txt = '<img src="' . SITE_URL . 'api.php?controller=creatimg&txt=' . $txt . '&fonttype=' . $fonttype . '&fontsize=' . $fontsize . '&font=' . $font . '&fontcolor=' . $fontcolor . '&transparent=' . $transparent . '" align="absmiddle">';
	}
	return $txt;
}

/**
 * 获取内容地址
 *
 * @param $catid 栏目ID
 * @param $id 文章ID
 * @param $allurl 是否以绝对路径返回
 */
function go($catid, $id, $allurl = 0) {
	static $category = null;
	if ($category == null) $category = S ( 'common/category_content' );
	$id = intval ( $id );
	if (! $id || ! isset ( $category [$catid] )) return '';
	$modelid = $category [$catid] ['modelid'];
	if (! $modelid) return '';
	$db = Loader::model ( 'content_model' );
	$db->set_model ( $modelid );
	$r = $db->where ( array ('id' => $id ) )->field ( 'url' )->find ();
	if (! empty ( $allurl )) {
		if (strpos ( $r ['url'], '://' ) === false) {
			if (strpos ( $category [$catid] ['url'], '://' ) === FALSE) {
				$r ['url'] = substr ( SITE_URL, 0, - 1 ) . $r ['url'];
			} else {
				$r ['url'] = $category [$catid] ['url'] . $r ['url'];
			}
		}
	}
	return $r ['url'];
}

/**
 * 组装生成ID号
 *
 * @param $applications 模块名
 * @param $contentid 内容
 */
function id_encode($applications, $contentid) {
	return urlencode ( $applications . '-' . $contentid );
}

/**
 * 解析ID
 *
 * @param $id 评论ID
 */
function id_decode($id) {
	return explode ( '-', $id );
}

/**
 * 获取子栏目
 *
 * @param $parentid 父级id
 * @param $type 栏目类型
 * @param $self 是否包含本身 0为不包含
 */
function subcat($parentid = NULL, $type = NULL, $self = 0) {
	$category = S ( 'common/category_content' );
	foreach ( $category as $id => $cat ) {
		if (($parentid === NULL || $cat ['parentid'] == $parentid) && ($type === NULL || $cat ['type'] == $type)) $subcat [$id] = $cat;
		if ($self == 1 && $cat ['catid'] == $parentid && ! $cat ['child']) $subcat [$id] = $cat;
	}
	return $subcat;
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 *
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos($catid, $symbol = ' > ') {
	$category_arr = array ();
	$category_arr = S ( 'common/category_content' );
	if (! isset ( $category_arr [$catid] )) return '';
	$pos = '';
	$arrparentid = array_filter ( explode ( ',', $category_arr [$catid] ['arrparentid'] . ',' . $catid ) );
	foreach ( $arrparentid as $catid ) {
		$url = $category_arr [$catid] ['url'];
		if (strpos ( $url, '://' ) === false) $url = substr ( SITE_URL, 0, - 1 ) . $url;
		$pos .= '<a href="' . $url . '">' . $category_arr [$catid] ['catname'] . '</a>' . $symbol;
	}
	return $pos;
}

/**
 * 生成标题样式
 *
 * @param $style 样式
 * @param $html 是否显示完整的STYLE
 */
function title_style($style, $html = 1) {
	$str = '';
	if ($html) $str = ' style="';
	$style_arr = explode ( ';', $style );
	if (! empty ( $style_arr [0] )) $str .= 'color:' . $style_arr [0] . ';';
	if (! empty ( $style_arr [1] )) $str .= 'font-weight:' . $style_arr [1] . ';';
	if ($html) $str .= '" ';
	return $str;
}

/**
 * 检查id是否存在于数组中
 *
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in($id, $ids = '', $s = ',') {
	if (! $ids) return false;
	$ids = explode ( $s, $ids );
	return is_array ( $id ) ? array_intersect ( $id, $ids ) : in_array ( $id, $ids );
}

/**
 * 生成CNZZ统计代码
 */
function tjcode() {
	$config = S ( 'common/cnzz' );
	if (empty ( $config )) {
		return false;
	} else {
		return '<script src=\'http://pw.cnzz.com/c.php?id=' . $config ['username'] . '&l=2\' language=\'JavaScript\' charset=\'gb2312\'></script>';
	}
}

/**
 * 获取YUNCMS版本号
 */
function get_version($type = 0) {
	$version = C ( 'version' );
	if ($type == 1) {
		return $version ['version'];
	} elseif ($type == 2) {
		return $version ['release'];
	} else {
		return $version ['version'] . ' ' . $version ['release'];
	}
}

/**
 * 获取在线客服列表
 * 依赖JQuery
 */
function sonline() {
	$config = S ( 'common/common' );
	if (! $config ['live_ifonserver']) return '';
	return '<link rel="stylesheet" type="text/css" href="' . JS_PATH . 'Sonline/style/default_blue.css"/><script type="text/javascript" src="' . JS_PATH . 'jquery.Sonline.js"></script><script type="text/javascript">$(function(){$().Sonline({Position:"' . $config ['live_serverlistp'] . '",	Top:100,Width:165,Style:6,Effect:true,DefaultsOpen:' . $config ['live_boxopen'] . ',Tel:"' . $config ['telephone'] . '",Qqlist:"' . $config ['qq'] . '"});})</script>';
}

/**
 * 获取用户昵称
 * 不传入userid取当前用户nickname,如果nickname为空取username
 * 传入field，取用户$field字段信息
 */
function get_nickname($userid = '', $field = '') {
	$return = '';
	if (is_numeric ( $userid )) {
		$member_db = Loader::model ( 'member_model' );
		$memberinfo = $member_db->getby_userid ( $userid );
		if (! empty ( $field ) && $field != 'nickname' && isset ( $memberinfo [$field] ) && ! empty ( $memberinfo [$field] ))
			$return = $memberinfo [$field];
		else
			$return = isset ( $memberinfo ['nickname'] ) && ! empty ( $memberinfo ['nickname'] ) ? $memberinfo ['nickname'] . '(' . $memberinfo ['username'] . ')' : $memberinfo ['username'];
	} else {
		$return = cookie ( '_nickname' );
		if (empty ( $return )) $return .= '(' . cookie ( '_username' ) . ')';
	}
	return $return;
}

/**
 * 通过 username 值，获取用户所有信息
 * 获取用户信息
 * 不传入$field返回用户所有信息,
 * 传入field，取用户$field字段信息
 */
function get_memberinfo_buyusername($username, $field = '') {
	if (empty ( $username )) {
		return false;
	}
	static $memberinfo;
	if (! isset ( $memberinfo [$username] )) {
		$member_db = Loader::model ( 'member_model' );
		$memberinfo [$username] = $member_db->getby_username ( $username );
	}
	if (! empty ( $field ) && ! empty ( $memberinfo [$username] [$field] )) {
		return $memberinfo [$username] [$field];
	} else {
		return $memberinfo [$username];
	}
}

/**
 * 获取用户信息
 * 不传入$field返回用户所有信息,
 * 传入field，取用户$field字段信息
 */
function get_memberinfo($userid, $field = '') {
	if (! is_numeric ( $userid )) {
		return false;
	} else {
		static $memberinfo;
		if (! isset ( $memberinfo [$userid] )) {
			$member_db = Loader::model ( 'member_model' );
			$memberinfo [$userid] = $member_db->getby_userid ( $userid );
		}
		if (! empty ( $field ) && ! empty ( $memberinfo [$userid] [$field] )) {
			return $memberinfo [$userid] [$field];
		} else {
			return $memberinfo [$userid];
		}
	}
}

/**
 * 获取用户头像
 *
 * @param $uid 默认为userid
 * @param $size 头像大小有四种[30x30 45x45 90x90 180x180] 默认30
 */
function get_memberavatar($userid, $size = '30') {
	$memberinfo = Loader::model ( 'member_model' )->getby_userid ( $userid );
	if (! $memberinfo) return false;
	if (ucenter_exists () && isset ( $memberinfo ['ucenterid'] )) {
		$avatar = Loader::lib ( 'member:uc_client' )->uc_get_avatar ( $memberinfo ['ucenterid'] );
	} else {
		if (! $memberinfo ['avatar']) return false;
		$dir1 = ceil ( $userid / 10000 );
		$dir2 = ceil ( $userid % 10000 / 1000 );
		$url = C ( 'attachment', 'avatar_url' ) . $dir1 . '/' . $dir2 . '/' . $userid . '/';
		$avatar = array ('180' => $url . '180x180.jpg','90' => $url . '90x90.jpg','45' => $url . '45x45.jpg','30' => $url . '30x30.jpg' );
	}
	if (isset ( $avatar ) && ! $size) {
		return $avatar;
	} else if (isset ( $avatar [$size] ))
		return $avatar [$size];
	else
		return false;
}

/**
 * 根据catid获取子栏目数据的sql语句
 *
 * @param intval $catid 栏目ID
 */
function get_sql_catid($file = 'category_content', $catid = 0) {
	$category = S ( 'common/' . $file );
	$catid = intval ( $catid );
	if (! isset ( $category [$catid] )) return false;
	return $category [$catid] ['child'] ? array ('in',$category [$catid] ['arrchildid'] ) : $catid;
}

/**
 * 根据ID获取当前余额操作信息
 *
 * @param int $userid 会员余额的ID
 *
 * @return int
 */
function get_surplus_info($userid) {
	$memberinfo = Loader::model ( 'member_model' )->getby_userid ( $userid );
	if (! $memberinfo) return false;
	return $memberinfo ['amount'];
}

/**
 * 取得已安装的支付方式(其中不包括线下支付的)
 *
 * @param bool $include_balance 是否包含余额支付（冲值时不应包括）
 * @return array 已安装的配送方式列表
 */
function get_online_payment_list($include_balance = true) {
	$where = array ('enabled' => 1,'is_cod' => array ('neq','1' ) );
	if (! $include_balance) {
		$where ['pay_code'] = array ('neq','balance' );
	}
	$modules = Loader::model ( 'payment_cfg_model' )->where ( $where )->select ();
	return $modules;
}
function mk_pay_btn($data, $attr = 'class="payment-show"', $ishow = '1') {
	$pay_type = '';
	if (is_array ( $data )) {
		foreach ( $data as $v ) {
			$pay_type .= '<label ' . $attr . '>';
			$pay_type .= '<input name="payment" type="radio" value="' . $v ['id'] . '"> <em>' . $v ['name'] . '</em>';
			$pay_type .= $ishow ? '<span class="payment-desc">' . $v ['pay_desc'] . '</span>' : '';
			$pay_type .= '</label>';
		}
	}
	return $pay_type;
}
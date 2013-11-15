<?php
/**
 * 字符串格式化
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-17
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: String.php 634 2013-07-29 07:36:33Z 85825770@qq.com $
 *
 */
class String {
	const UTF8 = 'utf-8';
	const GBK = 'gbk';

	/**
	 * 返回经addslashes处理过的字符串或数组
	 *
	 * @param $string 需要处理的字符串或数组
	 * @return mixed
	 */
	public static function addslashes($string) {
		if (! is_array ( $string )) return addslashes ( $string );
		foreach ( $string as $key => $val )
			$string [$key] = self::addslashes ( $val );
		return $string;
	}

	/**
	 * 返回经stripslashes处理过的字符串或数组
	 *
	 * @param $string 需要处理的字符串或数组
	 * @return mixed
	 */
	public static function stripslashes($string) {
		if (empty ( $string )) return $string;
		if (! is_array ( $string )) {
			return stripslashes ( $string );
		} else {
			foreach ( $string as $key => $val ) {
				$string [$key] = self::stripslashes ( $val );
			}
		}
		return $string;
	}

	/**
	 * 返回经htmlspecialchars处理过的字符串或数组
	 *
	 * @param $obj 需要处理的字符串或数组
	 * @return mixed
	 */
	public static function htmlspecialchars($string) {
		if (! is_array ( $string )) return htmlspecialchars ( $string );
		foreach ( $string as $key => $val )
			$string [$key] = self::htmlspecialchars ( $val );
		return $string;
	}

	/**
	 * 生成UUID 单机使用
	 *
	 * @return string
	 */
	public static function uuid() {
		$charid = md5 ( uniqid ( mt_rand (), true ) );
		$hyphen = chr ( 45 ); // "-"
		$uuid = chr ( 123 ) . 		// "{"
		substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 ) . chr ( 125 ); // "}"
		return $uuid;
	}

	/**
	 * 生成Guid主键
	 *
	 * @return Boolean
	 */
	public static function key_gen() {
		return str_replace ( '-', '', substr ( self::uuid (), 1, - 1 ) );
	}

	/**
	 * 检查字符串是否是UTF8编码
	 *
	 * @param string $string 字符串
	 * @return Boolean
	 */
	public static function is_utf8($str) {
		$c = 0;
		$b = 0;
		$bits = 0;
		$len = strlen ( $str );
		for($i = 0; $i < $len; $i ++) {
			$c = ord ( $str [$i] );
			if ($c > 128) {
				if (($c >= 254))
					return false;
				elseif ($c >= 252)
					$bits = 6;
				elseif ($c >= 248)
					$bits = 5;
				elseif ($c >= 240)
					$bits = 4;
				elseif ($c >= 224)
					$bits = 3;
				elseif ($c >= 192)
					$bits = 2;
				else
					return false;
				if (($i + $bits) > $len) return false;
				while ( $bits > 1 ) {
					$i ++;
					$b = ord ( $str [$i] );
					if ($b < 128 || $b > 191) return false;
					$bits --;
				}
			}
		}
		return true;
	}

	/**
	 * 截取字符串,支持字符编码,默认为utf-8
	 *
	 * @param string $string 要截取的字符串编码
	 * @param int $start 开始截取
	 * @param int $length 截取的长度
	 * @param string $charset 原妈编码,默认为UTF8
	 * @param boolean $dot 是否显示省略号,默认为false
	 * @return string 截取后的字串
	 */
	public static function substr($string, $start = 0, $length, $charset = self::UTF8, $dot = null) {
		switch (strtolower ( $charset )) {
			case self::GBK :
				$string = self::substr_for_gbk ( $string, $start, $length, $dot );
				break;
			case self::UTF8 :
				$string = self::substr_for_utf8 ( $string, $start, $length, $dot );
				break;
			default :
				$string = substr ( $string, $start, $length );
		}
		return $string;
	}

	/**
	 * 求取字符串长度
	 *
	 * @param string $string 要计算的字符串编码
	 * @param string $charset 原始编码,默认为UTF8
	 * @return int
	 */
	public static function strlen($string, $charset = self::UTF8) {
		switch (strtolower ( $charset )) {
			case self::GBK :
				$count = self::strlen_for_gbk ( $string );
				break;
			case self::UTF8 :
				$count = self::strlen_for_utf8 ( $string );
				break;
			default :
				$count = strlen ( $string );
		}
		return $count;
	}

	/**
	 * 提取两个字符串之间的值，不包括分隔符
	 *
	 * @param string $string 待提取的只付出
	 * @param string $start 开始字符串
	 * @param string|null $end 结束字符串，省略将返回所有的。
	 * @return bool string substring between $start and $end or false if either
	 *         string is not found
	 *
	 */
	public static function substr_between($string, $start, $end = null) {
		if (($start_pos = strpos ( $string, $start )) !== false) {
			if ($end) {
				if (($end_pos = strpos ( $string, $end, $start_pos + strlen ( $start ) )) !== false) {
					return substr ( $string, $start_pos + strlen ( $start ), $end_pos - ($start_pos + strlen ( $start )) );
				}
			} else {
				return substr ( $string, $start_pos );
			}
		}
		return false;
	}

	/**
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
	 *
	 * @param string $len 长度
	 * @param string $type 字串类型
	 *        0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 * @return string
	 */
	public static function rand_string($len = 6, $type = '', $add_chars = '') {
		$str = '';
		switch ($type) {
			case 0 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $add_chars;
				break;
			case 1 :
				$chars = str_repeat ( '0123456789', 3 );
				break;
			case 2 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $add_chars;
				break;
			case 3 :
				$chars = 'abcdefghijklmnopqrstuvwxyz' . $add_chars;
				break;
			case 4 :
				$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $add_chars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $add_chars;
				break;
		}
		if ($len > 10) { // 位数过长重复字符串一定次数
			$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
		}
		if ($type != 4) {
			$chars = str_shuffle ( $chars );
			$str = substr ( $chars, 0, $len );
		} else {
			// 中文随机字
			for($i = 0; $i < $len; $i ++) {
				$str .= self::substr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1, 'utf-8', false );
			}
		}
		return $str;
	}

	/**
	 * 生成一定数量的随机数，并且不重复
	 *
	 * @param integer $number 数量
	 * @param string $len 长度
	 * @param string $type 字串类型
	 *        0 字母 1 数字 其它 混合
	 * @return string
	 */
	public static function build_count_rand($number, $length = 4, $mode = 1) {
		if ($mode == 1 && $length < strlen ( $number )) {
			// 不足以生成一定数量的不重复数字
			return false;
		}
		$rand = array ();
		for($i = 0; $i < $number; $i ++) {
			$rand [] = self::rand_string ( $length, $mode );
		}
		$unqiue = array_unique ( $rand );
		if (count ( $unqiue ) == count ( $rand )) {
			return $rand;
		}
		$count = count ( $rand ) - count ( $unqiue );
		for($i = 0; $i < $count * 3; $i ++) {
			$rand [] = self::rand_string ( $length, $mode );
		}
		$rand = array_slice ( array_unique ( $rand ), 0, $number );
		return $rand;
	}

	/**
	 * 带格式生成随机字符 支持批量生成
	 * 但可能存在重复
	 *
	 * @param string $format 字符格式
	 *        # 表示数字 * 表示字母和数字 $ 表示字母
	 * @param integer $number 生成数量
	 * @return string | array
	 */
	public static function build_format_rand($format, $number = 1) {
		$str = array ();
		$length = strlen ( $format );
		for($j = 0; $j < $number; $j ++) {
			$strtemp = '';
			for($i = 0; $i < $length; $i ++) {
				$char = substr ( $format, $i, 1 );
				switch ($char) {
					case "*" : // 字母和数字混合
						$strtemp .= String::rand_string ( 1 );
						break;
					case "#" : // 数字
						$strtemp .= String::rand_string ( 1, 1 );
						break;
					case "$" : // 大写字母
						$strtemp .= String::rand_string ( 1, 2 );
						break;
					default : // 其他格式均不转换
						$strtemp .= $char;
						break;
				}
			}
			$str [] = $strtemp;
		}
		return $number == 1 ? $strtemp : $str;
	}

	/**
	 * 获取一定范围内的随机数字 位数不足补零
	 *
	 * @param integer $min 最小值
	 * @param integer $max 最大值
	 * @return string
	 *
	 */
	public static function rand_number($min, $max) {
		return sprintf ( "%0" . strlen ( $max ) . "d", mt_rand ( $min, $max ) );
	}

	/**
	 * 自动转换字符集 支持数组转换
	 *
	 * @param string $string
	 * @param string $from 源编码
	 * @param string $to 输出编码
	 * @return unknown string Ambigous
	 *
	 */
	public static function auto_charset($string, $from = 'gbk', $to = 'utf-8') {
		$from = strtoupper ( $from ) == 'UTF8' ? 'utf-8' : $from;
		$to = strtoupper ( $to ) == 'UTF8' ? 'utf-8' : $to;
		if (strtoupper ( $from ) === strtoupper ( $to ) || empty ( $string ) || (is_scalar ( $string ) && ! is_string ( $string ))) {
			// 如果编码相同或者非字符串标量则不转换
			return $string;
		}
		if (is_string ( $string )) {
			if (function_exists ( 'mb_convert_encoding' )) {
				return mb_convert_encoding ( $string, $to, $from );
			} elseif (function_exists ( 'iconv' )) {
				return iconv ( $from, $to, $string );
			} else {
				return $string;
			}
		} elseif (is_array ( $string )) {
			foreach ( $string as $key => $val ) {
				$_key = self::auto_charset ( $key, $from, $to );
				$string [$_key] = self::auto_charset ( $val, $from, $to );
				if ($key != $_key) unset ( $string [$key] );
			}
			return $string;
		} else {
			return $string;
		}
	}

	/**
	 * 字符串加密、解密
	 *
	 * @param string $txt
	 * @param string $operation
	 * @param string $key
	 * @return string
	 *
	 */
	public static function authcode($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
		$key_length = 4;
		$key = md5 ( $key != '' ? $key : C ( 'config', 'auth_key' ) );
		$fixedkey = md5 ( $key );
		$egiskeys = md5 ( substr ( $fixedkey, 16, 16 ) );
		$runtokey = $key_length ? ($operation == 'ENCODE' ? substr ( md5 ( microtime ( true ) ), - $key_length ) : substr ( $string, 0, $key_length )) : '';
		$keys = md5 ( substr ( $runtokey, 0, 16 ) . substr ( $fixedkey, 0, 16 ) . substr ( $runtokey, 16 ) . substr ( $fixedkey, 16 ) );
		$string = $operation == 'ENCODE' ? sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $egiskeys ), 0, 16 ) . $string : base64_decode ( substr ( $string, $key_length ) );
		$i = 0;
		$result = '';
		$string_length = strlen ( $string );
		for($i = 0; $i < $string_length; $i ++) {
			$result .= chr ( ord ( $string {$i} ) ^ ord ( $keys {$i % 32} ) );
		}
		if ($operation == 'ENCODE') {
			return $runtokey . str_replace ( '=', '', base64_encode ( $result ) );
		} else {
			if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $egiskeys ), 0, 16 )) {
				return substr ( $result, 26 );
			} else {
				return '';
			}
		}
	}

	/**
	 * 将变量的值转换为字符串
	 *
	 * @param mixed $input 变量
	 * @param string $indent 缩进,默认为''
	 * @return string
	 *
	 */
	public static function var_to_string($input, $indent = '') {
		switch (gettype ( $input )) {
			case 'string' :
				return "'" . str_replace ( array ("\\","'" ), array ("\\\\","\\'" ), $input ) . "'";
			case 'array' :
				$output = "array(\r\n";
				foreach ( $input as $key => $value ) {
					$output .= $indent . "\t" . self::var_to_string ( $key, $indent . "\t" ) . ' => ' . self::var_to_string ( $value, $indent . "\t" );
					$output .= ",\r\n";
				}
				$output .= $indent . ')';
				return $output;
			case 'boolean' :
				return $input ? 'true' : 'false';
			case 'NULL' :
				return 'NULL';
			case 'integer' :
			case 'double' :
			case 'float' :
				return "'" . ( string ) $input . "'";
		}
		return 'NULL';
	}

	/**
	 * 以utf8格式截取的字符串编码
	 *
	 * @param string $string 要截取的字符串编码
	 * @param int $start 开始截取
	 * @param int $length 截取的长度，默认为null，取字符串的全长
	 * @param boolean $dot 是否显示省略号，默认为false
	 * @return string
	 *
	 */
	public static function substr_for_utf8($string, $start, $length = null, $dot = null) {
		$l = strlen ( $string );
		if ($l <= $length) return $string;
		$p = $s = 0;
		if (0 !== $start) {
			while ( $start -- && $p < $l ) {
				$c = $string [$p];
				if ($c < "\xC0")
					$p ++;
				elseif ($c < "\xE0")
					$p += 2;
				elseif ($c < "\xF0")
					$p += 3;
				elseif ($c < "\xF8")
					$p += 4;
				elseif ($c < "\xFC")
					$p += 5;
				else
					$p += 6;
			}
			$s = $p;
		}

		if (empty ( $length )) {
			$t = substr ( $string, $s );
		} else {
			$i = $length;
			while ( $i -- && $p < $l ) {
				$c = $string [$p];
				if ($c < "\xC0")
					$p ++;
				elseif ($c < "\xE0")
					$p += 2;
				elseif ($c < "\xF0")
					$p += 3;
				elseif ($c < "\xF8")
					$p += 4;
				elseif ($c < "\xFC")
					$p += 5;
				else
					$p += 6;
			}
			$t = substr ( $string, $s, $p - $s );
		}
		! is_null ( $dot ) && ($p < $l) && $t .= $dot;
		return $t;
	}

	/**
	 * 以gbk格式截取的字符串编码
	 *
	 * @param string $string 要截取的字符串编码
	 * @param int $start 开始截取
	 * @param int $length 截取的长度，默认为null，取字符串的全长
	 * @param boolean $dot 是否显示省略号，默认为false
	 * @return string
	 *
	 */
	public static function substr_for_gbk($string, $start, $length = null, $dot = null) {
		$l = strlen ( $string );
		if ($l <= $length) return $string;
		$p = $s = 0;
		if (0 !== $start) {
			while ( $start -- && $p < $l ) {
				if ($string [$p] > "\x80")
					$p += 2;
				else
					$p ++;
			}
			$s = $p;
		}

		if (empty ( $length )) {
			$t = substr ( $string, $s );
		} else {
			$i = $length;
			while ( $i -- && $p < $l ) {
				if ($string [$p] > "\x80")
					$p += 2;
				else
					$p ++;
			}
			$t = substr ( $string, $s, $p - $s );
		}

		! is_null ( $dot ) && ($p < $l) && $t .= $dot;
		return $t;
	}

	/**
	 * 以utf8求取字符串长度
	 *
	 * @param string $string 要计算的字符串编码
	 * @return int
	 *
	 */
	public static function strlen_for_utf8($string) {
		$l = strlen ( $string );
		$p = $c = 0;
		while ( $p < $l ) {
			$a = $string [$p];
			if ($a < "\xC0")
				$p ++;
			elseif ($a < "\xE0")
				$p += 2;
			elseif ($a < "\xF0")
				$p += 3;
			elseif ($a < "\xF8")
				$p += 4;
			elseif ($a < "\xFC")
				$p += 5;
			else
				$p += 6;
			$c ++;
		}
		return $c;
	}

	/**
	 * 以gbk求取字符串长度
	 *
	 * @param string $string 要计算的字符串编码
	 * @return int
	 *
	 */
	public static function strlen_for_gbk($string) {
		$l = strlen ( $string );
		$p = $c = 0;
		while ( $p < $l ) {
			if ($string [$p] > "\x80")
				$p += 2;
			else
				$p ++;
			$c ++;
		}
		return $c;
	}
}
<?php
/**
 * 常用的正则表达式来验证信息.如:网址 邮箱 手机号等
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-26
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Validate.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
class Validate {

	/**
	 * 用正则表达式验证手机号码(中国大陆区)
	 *
	 * @param integer $num
	 * @return boolean
	 */
	public static function is_mobile($number) {
		return 0 < preg_match ( '#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $number );
	}

	/**
	 * 验证是否是电话号码
	 *
	 * 国际区号-地区号-电话号码的格式（在国际区号前可以有前导0和前导+号），
	 * 国际区号支持0-4位
	 * 地区号支持0-6位
	 * 电话号码支持4到12位
	 *
	 * @param string $phone 被验证的电话号码
	 * @return boolean 如果验证通过则返回true，否则返回false
	 */
	public static function is_telphone($phone) {
		return 0 < preg_match('/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{0,6}[\-\s]?\d{4,12}$/', $phone);
	}

	/**
	* 验证是否是手机号码
	*
	* 国际区号-手机号码
	*
	* @param string $number 待验证的号码
	* @return boolean 如果验证失败返回false,验证成功返回true
	*/
	public static function is_telnumber($number) {
		return 0 < preg_match('/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{4,12}$/', $number);
	}

	/**
	 * 检测输入中是否含有错误字符
	 *
	 * @param char $string 要检查的字符串名称
	 * @return TRUE or FALSE
	 */
	public static function is_badword($string) {
		$badwords = array ("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#" );
		foreach ( $badwords as $value ) {
			if (strpos ( $string, $value ) !== FALSE) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * 是否是合法的密码
	 *
	 * @param STRING $password
	 * @return TRUE or FALSE
	 */
	public static function is_password($password) {
		$strlen = strlen ( $password );
		if ($strlen >= 6 && $strlen <= 20) return true;
		return false;
	}

	/**
	 * 检查用户名是否符合规定
	 *
	 * @param STRING $username 要检查的用户名
	 * @return TRUE or FALSE
	 */
	public static function is_username($username) {
		$strlen = strlen ( $username );
		if (self::is_badword ( $username ) || ! preg_match ( "/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username )) {
			return false;
		} elseif (20 < $strlen || $strlen < 2) {
			return false;
		}
		return true;
	}

	/**
	* 验证是否是有合法的email
	*
	* @param string $string  被搜索的 字符串
	* @param array $matches  会被搜索的结果,默认为array()
	* @param boolean $ifAll  是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	* @return boolean 如果匹配成功返回true，否则返回false
	*/
	public static function has_email($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $string);
	}

	/**
	* 验证是否是合法的email
	*
	* @param string $string 待验证的字串
	* @return boolean 如果是email则返回true，否则返回false
	*/
	public static function is_email($string) {
		return 0 < preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $string);
	}

	/**

	* 验证是否是QQ号码
	*
	* QQ号码必须是以1-9的数字开头，并且长度5-15为的数字串
	*
	* @param string $qq 待验证的qq号码
	* @return boolean 如果验证成功返回true，否则返回false
	*/
	public static function is_qq($qq) {
		return 0 < preg_match('/^[1-9]\d{4,14}$/', $qq);
	}

	/**
	* 验证是否是邮政编码
	*
	* 邮政编码是4-8个长度的数字串
	*
	* @param string $zipcode 待验证的邮编
	* @return boolean 如果验证成功返回true，否则返回false
	*/
	public static function is_zipcode($zipcode) {
		return 0 < preg_match('/^\d{4,8}$/', $zipcode);
	}

	/**
	* 验证是否有合法的身份证号
	*
	* @param string $string  被搜索的 字符串
	* @param array $matches  会被搜索的结果,默认为array()
	* @param boolean $ifAll  是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	* @return boolean 如果匹配成功返回true，否则返回false
	*/
	public static function has_idcard($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp("/\d{17}[\d|X]|\d{15}/", $string, $matches, $ifAll);
	}

	/**
	* 验证是否是合法的身份证号
	*
	* @param string $string 待验证的字串
	* @return boolean 如果是合法的身份证号则返回true，否则返回false
	*/
	public static function is_idcard($string) {
		return 0 < preg_match("/^(?:\d{17}[\d|X]|\d{15})$/", $string);
	}

	/**
	 * 用正则表达式验证出版物的ISBN号
	 *
	 * @param integer $str
	 * @return boolean
	 */
	public static function is_isbn($string) {
		return 0 < preg_match ( '#^978[\d]{10}$|^978-[\d]{10}$#', $string);
	}

	/**
	* 验证是否有合法的URL
	*
	* @param string $string  被搜索的 字符串
	* @param array $matches  会被搜索的结果,默认为array()
	* @param boolean $ifAll  是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	* @return boolean 如果匹配成功返回true，否则返回false
	*/
	public static function has_url($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp('/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $string, $matches, $ifAll);
	}

	/**
	* 验证是否是合法的url
	*
	* @param string $string 待验证的字串
	* @return boolean 如果是合法的url则返回true，否则返回false
	*/
	public static function is_url($string) {
		return 0 < preg_match('/^(?:http(?:s)?:\/\/(?:[\w-]+\.)+[\w-]+(?:\:\d+)*+(?:\/[\w- .\/?%&=]*)?)$/', $string);
	}

	/**
	* 验证是否有中文
	*
	* @param string $string  被搜索的 字符串
	* @param array $matches  会被搜索的结果,默认为array()
	* @param boolean $ifAll  是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	* @return boolean 如果匹配成功返回true，否则返回false
	*/
	public static function has_chinese($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp('/[\x{4e00}-\x{9fa5}]+/u', $string, $matches, $ifAll);
	}

	/**
	* 验证是否是中文
	*
	* @param string $string 待验证的字串
	* @return boolean 如果是中文则返回true，否则返回false
	*/
	public static function is_chinese($string) {
		return 0 < preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $string);
	}

	/**
	* 验证是否有html标记
	*
	* @param string $string  被搜索的 字符串
	* @param array $matches  会被搜索的结果,默认为array(
	* @param boolean $ifAll  是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	* @return boolean 如果匹配成功返回true，否则返回false
	*/
	public static function has_html($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp('/<(.*)>.*|<(.*)\/>/', $string, $matches, $ifAll);
	}

	/**
	 * 验证是否是合法的html标记
	 *
	 * @param string $string 待验证的字串
	 * @return boolean 如果是合法的html标记则返回true，否则返回false
	 */
	public static function is_html($string) {
		return 0 < preg_match('/^<(.*)>.*|<(.*)\/>$/', $string);
	}

	/**
	 * 验证是否有合法的ipv4地址
	 *
	 * @param string $string 被搜索的 字符串
	 * @param array $matches 会被搜索的结果,默认为array()
	 * @param boolean $ifAll 是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	 * @return boolean 如果匹配成功返回true，否则返回false
	 */
	public static function has_ipv4($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp ( '/((25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)\.){3}(25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)/', $string, $matches, $ifAll );
	}

	/**
	 * 验证是否是合法的IP
	 *
	 * @param string $string 待验证的字串
	 * @return boolean 如果是合法的IP则返回true，否则返回false
	 */
	public static function is_ipv4($string) {
		return 0 < preg_match ( '/(?:(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)/', $string );
	}

	/**
	 * 验证是否有合法的ipV6
	 *
	 * @param string $string 被搜索的 字符串
	 * @param array $matches 会被搜索的结果,默认为array()
	 * @param boolean $ifAll 是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	 * @return boolean 如果匹配成功返回true，否则返回false
	 */
	public static function has_ipv6($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp ( '/\A((([a-f0-9]{1,4}:){6}|
										::([a-f0-9]{1,4}:){5}|
										([a-f0-9]{1,4})?::([a-f0-9]{1,4}:){4}|
										(([a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::([a-f0-9]{1,4}:){3}|
										(([a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::([a-f0-9]{1,4}:){2}|
										(([a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:|
										(([a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::
									)([a-f0-9]{1,4}:[a-f0-9]{1,4}|
										(([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}
										([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])
									)|((([a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}|
										(([a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::
									)
								)\Z/ix', $string, $matches, $ifAll );
	}

	/**
	 * 验证是否是合法的ipV6
	 *
	 * @param string $string 待验证的字串
	 * @return boolean 如果是合法的ipV6则返回true，否则返回false
	 */
	public static function is_ipv6($string) {
		return 0 < preg_match ( '/\A(?:(?:(?:[a-f0-9]{1,4}:){6}|
										::(?:[a-f0-9]{1,4}:){5}|
										(?:[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){4}|
										(?:(?:[a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){3}|
										(?:(?:[a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){2}|
										(?:(?:[a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:|
										(?:(?:[a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::
									)(?:[a-f0-9]{1,4}:[a-f0-9]{1,4}|
										(?:(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}
										(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])
									)|(?:(?:(?:[a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}|
										(?:(?:[a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::
									)
								)\Z/ix', $string );
	}

	/**
	 * 验证是否有客户端脚本
	 *
	 * @param string $string 被搜索的 字符串
	 * @param array $matches 会被搜索的结果,默认为array()
	 * @param boolean $ifAll 是否进行全局正则表达式匹配，默认为false即仅进行一次匹配
	 * @return boolean 如果匹配成功返回true，否则返回false
	 */
	public static function has_script($string, &$matches = array(), $ifAll = false) {
		return 0 < self::validateByRegExp ( '/<script(.*?)>([^\x00]*?)<\/script>/', $string, $matches, $ifAll );
	}

	/**
	 * 验证是否是合法的客户端脚本
	 *
	 * @param string $string 待验证的字串
	 * @return boolean 如果是合法的客户端脚本则返回true，否则返回false
	 *
	 */
	public static function is_script($string) {
		return 0 < preg_match ( '/<script(?:.*?)>(?:[^\x00]*?)<\/script>/', $string );
	}

	/**
	 * 验证是否是非负数
	 *
	 * @param int $number 需要被验证的数字
	 * @return boolean 如果大于等于0的整数数字返回true，否则返回false
	 */
	public static function is_non_negative($number) {
		return is_numeric ( $number ) && 0 <= $number;
	}

	/**
	 * 验证是否是正数
	 *
	 * @param int $number 需要被验证的数字
	 * @return boolean 如果数字大于0则返回true否则返回false
	 */
	public static function is_positive($number) {
		return is_numeric ( $number ) && 0 < $number;
	}

	/**
	 * 验证是否是负数
	 *
	 * @param int $number 需要被验证的数字
	 * @return boolean 如果数字小于于0则返回true否则返回false
	 */
	public static function is_negative($number) {
		return is_numeric ( $number ) && 0 > $number;
	}

	/**
	 * 验证是否是不能为空
	 *
	 * @param mixed $value 待判断的数据
	 * @return boolean 如果为空则返回false,不为空返回true
	 */
	public static function is_required($value) {
		return ! empty ( $value );
	}

	/**
	 * 在 $string 字符串中搜索与 $regExp 给出的正则表达式相匹配的内容。
	 *
	 * @param string $regExp 搜索的规则(正则)
	 * @param string $string 被搜索的 字符串
	 * @param array $matches 会被搜索的结果，默认为array()
	 * @param boolean $ifAll 是否进行全局正则表达式匹配，默认为false不进行完全匹配
	 * @return int 返回匹配的次数
	 */
	private static function validateByRegExp($regExp, $string, &$matches = array(), $ifAll = false) {
		return $ifAll ? preg_match_all ( $regExp, $string, $matches ) : preg_match ( $regExp, $string, $matches );
	}
}
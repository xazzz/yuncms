<?php
/**
 * ImageFilter.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: ImageFilter.php 734 2013-08-07 09:47:42Z 85825770@qq.com $
 */
class ImageFilter {
	public $keys = array ();
	public $maxfontwith = 0;
	public $result;
	protected $data = array ();
	protected $deal_data;
	protected $pixels = array ();
	private $image;
	private $iw, $ih;

	/**
	 * 构造方法
	 */
	public function __construct() {
		$this->_initialize();
	}

	/**
	 * 初始化
	 */
	public function _initialize(){
		$this->maxfontwith = 16;
		$this->keys = array();
	}

	/**
	 * 加载图像文件
	 *
	 * @param string $filename 文件路径
	 */
	public function load($filename) {
		if (! $this->check ( $filename )) return false;
		$info = $this->info ( $filename );
		$createfun = 'imagecreatefrom' . ($info ['type'] == 'jpg' ? 'jpeg' : $info ['type']);
		$this->image = $createfun ( $filename );
		$this->iw = $info ['width'];
		$this->ih = $info ['height'];
	}

	/**
	 * 图像二值化
	 *
	 * @param number $grey
	 */
	public function change_grey($grey = 100) {
		for($y = 0; $y < $this->ih; ++ $y) {
			for($x = 0; $x < $this->iw; ++ $x) {
				$rgb = imagecolorat ( $this->image, $x, $y );
				if ((($rgb >> 16) & 0xFF) > $grey) {
					$red = 255;
				} else {
					$red = 0;
				}
				if ((($rgb >> 8) & 0xFF) > $grey) {
					$green = 255;
				} else {
					$green = 0;
				}
				if (($rgb & 0xFF) > $grey) {
					$blue = 255;
				} else {
					$blue = 0;
				}
				imagesetpixel ( $this->image, $x, $y, ImageColorAllocate ( $this->image, $red, $green, $blue ) );
			}
		}
		$this->fingerprint ();
		$this->filter_info ();
		$this->dealwith_data();
	}

	/**
	 * 取得图像指纹
	 */
	public function fingerprint() {
		for($y = 0; $y < $this->ih; ++ $y) {
			for($x = 0; $x < $this->iw; ++ $x) {
				$rgb = imagecolorat ( $this->image, $x, $y );
				$rgbarray = imagecolorsforindex ( $this->image, $rgb );
				if ($rgbarray ['red'] < 125 || $rgbarray ['green'] < 125 || $rgbarray ['blue'] < 125) {
					$this->pixels [$y] [$x] = 1;
				} else {
					$this->pixels [$y] [$x] = '-';
				}
			}
		}
		// 首列1
		for($x = 0; $x < $this->ih; ++ $x) {
			$this->pixels [$x] [0] = 0;
		}
	}

	/**
	 * 开始识别
	 */
	public function run() {
		$data = array ();
		$i = 0;
		foreach ( $this->deal_data as $key => $value ) {
			$data [$i] = "";
			foreach ( $value as $skey => $svalue ) {
				$data [$i] .= implode ( "", $svalue );
			}
			++ $i;
		}
		// 进行关键字匹配
		foreach ( $data as $numKey => $numString ) {
			$max = 0.0;
			$num = 0;
			if (isset ( $this->keys [$numString] )) {
				$this->result [$numKey] = $this->keys [$numString];
			} else {
				foreach ( $this->keys as $key => $value ) {
					$FindOk = false;
					$percent = 0.0;
					similar_text ( $key, $numString, $percent );
					if (intval ( $percent ) > $max) {
						$max = $percent;
						$num = $value;
						if (intval ( $percent ) > 98) {
							$FindOk = true;
							break;
						}
					}
				}
				$this->result [$numKey] = $num;
			}
		}
		// 查找最佳匹配数字
		return $this->result;
	}

	/**
	 * 显示
	 */
	public function draw() {
		for($i = 0; $i < $this->ih; ++ $i) {
			for($j = 0; $j < $this->iw; ++ $j) {
				if ($this->pixels [$i] [$j] == 0)
					echo '-';
				else
					echo $this->pixels [$i] [$j];
			}
			echo "\n";
		}
	}

	public function draw_deal_data() {
		foreach ( $this->deal_data as $key => $value ) {
			foreach ( $value as $skey => $svalue ) {
				echo implode ( "", $svalue );
			}
			echo "\n";
		}
	}

	/**
	 * 处理数据
	 */
	public function dealwith_data() {
		foreach ( $this->data as $key => $value ) {
			$rand_keys = array_rand ( $value );
			$with = count ( $value [$rand_keys] );
			$hight = count ( $value );
			$miniwith = array (3,3,3 );
			$minihight = array (3,3,3,3,3 );
			$bwithd = false;
			// 获取第一个key
			$tmpkey = array_keys ( $value );
			$arrykey = $tmpkey [0];

			switch ($with) {
				case 10 :
					$miniwith [2] = 4;
				case 9 :
					break;
				case 8 :
					$miniwith [0] = 2;
					break;
				case 7 :
					$miniwith [0] = 2;
					$miniwith [2] = 2;
					break;
				case 6 :
					$miniwith [0] = 2;
					$miniwith [1] = 2;
					$miniwith [2] = 2;
					break;
				case 5 :
					$miniwith [0] = 1;
					$miniwith [1] = 2;
					$miniwith [2] = 2;
					break;
				default :
					$bwithd = true;
					break;
			}

			if ($bwithd) {
				if ($bwithd < 4) {
					$this->result [$key] = "l";
					if ($value [$arrykey + 2] [0] == 0 && $value [$arrykey + 2] [1] == 0) $this->result [$key] = "i";
				} else {
					$this->result [$key] = "w";
					$num = 1;
					for($i = $arrykey; $i < $arrykey + $hight; ++ $i) {
						$num += $value [$i] [1];
					}
					if ($num == $hight) $this->result [$key] = "m";
				}
				continue;
			}

			switch ($hight) {
				case 18 :
					$minihight [0] = 4;
					$minihight [1] = 4;
					$minihight [4] = 4;
				case 17 :
					$minihight [0] = 4;
					$minihight [4] = 4;
				case 16 :
					$minihight [4] = 4;
					break;
				case 15 :
					break;
				case 14 :
					$minihight [4] = 2;
					break;
				case 13 :
					$minihight [0] = 2;
					$minihight [4] = 2;
					break;
				case 12 :
					$minihight [0] = 2;
					$minihight [3] = 2;
					$minihight [4] = 2;
					break;
				case 11 :
					$minihight [0] = 2;
					$minihight [2] = 2;
					$minihight [3] = 2;
					$minihight [4] = 2;
					break;
				case 10 :
					$minihight [0] = 2;
					$minihight [1] = 2;
					$minihight [2] = 2;
					$minihight [3] = 2;
					$minihight [4] = 2;
					break;
				case 9 :
					$minihight [0] = 2;
					$minihight [1] = 2;
					$minihight [2] = 2;
					$minihight [3] = 2;
					$minihight [4] = 1;
					break;
				case 8 :
					$minihight [0] = 1;
					$minihight [1] = 2;
					$minihight [2] = 2;
					$minihight [3] = 2;
					$minihight [4] = 1;
					break;
				case 7 :
					$minihight [0] = 1;
					$minihight [1] = 2;
					$minihight [2] = 2;
					$minihight [3] = 1;
					$minihight [4] = 1;
					break;
				default :
					echo "error hight:" . $hight;
					break;
			}

			$hs = 0;
			$ws = 0;

			foreach ( $minihight as $hightkey => $hightvalue ) {
				$ws = 0;
				foreach ( $miniwith as $withkey => $withvalue ) {
					$this->deal_data [$key] [$hightkey] [$withkey] = 0;
					$num = 0;
					// y
					for($i = $arrykey + $hs; $i < $arrykey + $hs + $hightvalue; ++ $i) {
						for($j = $ws; $j < $ws + $withvalue; ++ $j) {
							if (isset ( $value [$i] [$j] )) {
								$num += $value [$i] [$j];
							}
						}
					}
					$ws += $withvalue;
					$paret = intval ( $num / ($hightvalue * $withvalue) * 100 );
					$good = 43;
					switch ($hightvalue * $withvalue) {
						case 9 :
							$good = 22;
							break;
						case 8 :
							$good = 22;
							break;
						case 6 :
							$good = 22;
							break;
					}
					if ($paret > $good) {
						$this->deal_data [$key] [$hightkey] [$withkey] = 1;
					}
				}
				$hs += $hightvalue;
			}
		}
	}

	/**
	 * 过滤噪点
	 *
	 * @return boolean
	 */
	public function filter_info() {
		$data = array ();
		$num = 0;
		$b = false;
		$Continue = 0;
		$XStart = 0;
		for($y = 0; $y < $this->ih; ++ $y) {
			if ($y < 9 || $y > 16) {
				$xstart = - 1;
				$num = 0;
				for($x = 1; $x < $this->iw; ++ $x) {
					if ($this->pixels [$y] [$x] == 1) {
						if ($xstart == - 1) {
							$xstart = $x;
						}
					}
					if ($num > 8) {
						for($xt = $xstart; $xt < $this->iw; ++ $xt) {
							if ($this->pixels [$y] [$xt] == 1) {
								$this->pixels [$y] [$xt] = 0;
							} else {
								$x = $xt - 1;
								break;
							}
						}
					}
					if ($this->pixels [$y] [$x - 1] == 1 && $this->pixels [$y] [$x] == 1) {
						++ $num;
					} else {
						$xstart = - 1;
						$num = 0;
					}
				}
			}
		}

		// 如果1的周围数字不为1，修改为了0
		for($i = 0; $i < $this->ih; ++ $i) {
			for($j = 0; $j < $this->iw; ++ $j) {
				$num = 0;
				if ($this->pixels [$i] [$j] == 1) {
					// 上
					if (isset ( $this->pixels [$i - 1] [$j] )) {
						$num = $num + $this->pixels [$i - 1] [$j];
					}
					// 下
					if (isset ( $this->pixels [$i + 1] [$j] )) {
						$num = $num + $this->pixels [$i + 1] [$j];
					}
					// 左
					if (isset ( $this->pixels [$i] [$j - 1] )) {
						$num = $num + $this->pixels [$i] [$j - 1];
					}
					// 右
					if (isset ( $this->pixels [$i] [$j + 1] )) {
						$num = $num + $this->pixels [$i] [$j + 1];
					}
					// 上左
					if (isset ( $this->pixels [$i - 1] [$j - 1] )) {
						$num = $num + $this->pixels [$i - 1] [$j - 1];
					}
					// 上右
					if (isset ( $this->pixels [$i - 1] [$j + 1] )) {
						$num = $num + $this->pixels [$i - 1] [$j + 1];
					}
					// 下左
					if (isset ( $this->pixels [$i + 1] [$j - 1] )) {
						$num = $num + $this->pixels [$i + 1] [$j - 1];
					}
					// 下右
					if (isset ( $this->pixels [$i + 1] [$j + 1] )) {
						$num = $num + $this->pixels [$i + 1] [$j + 1];
					}
				}
				if ($num < 3) {
					$this->pixels [$i] [$j] = 0;
				}
			}
		}

		// 末尾部分处理
		for($j = 17; $j < $this->ih; ++ $j) {
			for($i = 51; $i < $this->iw; ++ $i) {
				$this->pixels [$j] [$i] = 0;
			}
		}
		for($j = 0; $j < 5; ++ $j) {
			for($i = 51; $i < $this->iw; ++ $i) {
				$this->pixels [$j] [$i] = 0;
			}
		}

		// X 坐标
		for($i = 0; $i < $this->iw; ++ $i) {
			// Y 坐标
			for($j = 0; $j < $this->ih; ++ $j) {
				if ($this->pixels [$j] [$i] == 1 || ($Continue > 0 && $Continue < 5)) {
					$b = true;
					++ $Continue;
					break;
				} else {
					$b = false;
				}
			}
			if ($b == true) {
				for($jj = 0; $jj < $this->ih; ++ $jj) {
					$data [$num] [$jj] [$XStart] = $this->pixels [$jj] [$i];
				}
				++ $XStart;
			} else {
				if ($Continue > 0) {
					$XStart = 0;
					$Continue = 0;
					++ $num;
				}
			}
		}
		// 粘连字符分割
		$inum = 0;
		for($num = 0; $num < count ( $data ); ++ $num) {
			$itemp = 5;
			$str = implode ( "", $data [$num] [$itemp] );
			// 超过标准长度
			if (strlen ( $str ) > $this->maxfontwith) {
				$len = (strlen ( $str ) + 1) / 2;
				$flen = strlen ( $str );
				$ih = 0;
				// $iih = 0;
				foreach ( $data [$num] as $key => $value ) {
					$ix = 0;
					$ixx = 0;
					foreach ( $value as $skey => $svalue ) {
						if ($skey < $len) {
							$this->data [$inum] [$ih] [$ix] = $svalue;
							++ $ix;
						}
						if ($skey > ($flen - $len - 1)) {
							$this->data [$inum + 1] [$ih] [$ixx] = $svalue;
							++ $ixx;
						}
					}
					++ $ih;
				}
				++ $inum;
			} else {
				$i = 0;
				foreach ( $data [$num] as $key => $value ) {
					$this->data [$inum] [$i] = $value;
					++ $i;
				}
			}
			++ $inum;
		}

		// 去掉0数据
		for($num = 0; $num < count ( $this->data ); ++ $num) {
			if (count ( $this->data [$num] ) != $this->ih) {
				foreach ( $this->data [$num] as $key => $value ) {
					$str = implode ( "", $value );
					echo $str;
					echo "\n";
				}
				return false;
			}

			for($i = 0; $i < $this->ih; ++ $i) {
				$str = implode ( "", $this->data [$num] [$i] );
				$pos = strpos ( $str, "1" );
				if ($pos === false) {
					unset ( $this->data [$num] [$i] );
				}
			}
		}
		return true;
	}

	/**
	 * 获取图片信息
	 *
	 * @param string $filename
	 * @return boolean multitype:unknown number Ambigous <>
	 */
	public function info($filename) {
		$imageinfo = getimagesize ( $filename );
		if ($imageinfo === false) return false;
		$imagetype = strtolower ( substr ( image_type_to_extension ( $imageinfo [2] ), 1 ) );
		$imagesize = filesize ( $filename );
		$info = array ('width' => $imageinfo [0],'height' => $imageinfo [1],'type' => $imagetype,'size' => $imagesize,'mime' => $imageinfo ['mime'] );
		return $info;
	}

	/**
	 * 检测系统环境以及文件
	 *
	 * @param string $image 文件路径
	 */
	private function check($image) {
		return extension_loaded ( 'gd' ) && preg_match ( "/\.(jpg|jpeg|gif|png|bmp)/i", $image, $m ) && file_exists ( $image ) && function_exists ( 'imagecreatefrom' . ($m [1] == 'jpg' ? 'jpeg' : $m [1]) );
	}
}
<?php
/**
 * Ueditor编辑器Api
 * @author Tongle Xu <xutongle@gmail.com> 2012-11-1
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: UeditorController.php 642 2013-07-29 08:56:39Z 85825770@qq.com $
 */
defined('IN_YUNCMS') or exit('No permission resources.');
class UeditorController {

	public function __construct(){

	}

	/**
	 * 获取视频
	 */
	public function get_movie(){
		$key =htmlspecialchars($_POST["searchKey"]);
		$type = htmlspecialchars($_POST["videoType"]);
		$html = file_get_contents('http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw='.$key.'&pageNo=1&pageSize=20&channelId='.$type.'&inDays=7&media=v&sort=s');
		echo $html;
	}
}
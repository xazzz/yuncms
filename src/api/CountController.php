<?php
/**
 * 点击统计API
 * @author Tongle Xu <xutongle@gmail.com> 2012-11-1
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: CountController.php 691 2013-07-30 04:12:44Z 85825770@qq.com $
 */
defined('IN_YUNCMS') or exit('No permission resources.');
class CountController{

	public $db;

	public function __construct(){
		$this->db = Loader::model('hits_model');
	}

	public function init(){
		if($_GET['modelid'] && $_GET['id']) {
			$model_arr = array();
			$model_arr = S('common/model');
			$modelid = intval($_GET['modelid']);
			$hitsid = 'c-'.$modelid.'-'.intval($_GET['id']);
			$r = $this->get_count($hitsid);
			if(!$r) exit;
			extract($r);
			$this->hits($hitsid);
			echo "\$('#todaydowns').html('$dayviews');";
			echo "\$('#weekdowns').html('$weekviews');";
			echo "\$('#monthdowns').html('$monthviews');";
		} elseif($_GET['application'] && $_GET['id']) {
			$application = $_GET['application'];
			if((preg_match('/([^a-z0-9_\-]+)/i',$application))) exit('1');
			$hitsid = $application.'-'.intval($_GET['id']);
			$r = $this->get_count($hitsid);
			if(!$r) exit;
			extract($r);
			$this->hits($hitsid);
		}
		exit("$('#hits').html('$views');");
	}

	/**
	 * 获取点击数量
	 * @param $hitsid
	 */
	private function get_count($hitsid) {
		$r = $this->db->getby_hitsid($hitsid);
		if(!$r) return false;
		return $r;
	}

	/**
	 * 点击次数统计
	 * @param $contentid
	 */
	public function hits($hitsid) {
		$r = $this->db->getby_hitsid($hitsid);
		if(!$r) return false;
		$views = $r['views'] + 1;
		$yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayviews'] : $r['yesterdayviews'];
		$dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', TIME)) ? ($r['dayviews'] + 1) : 1;
		$weekviews = (date('YW', $r['updatetime']) == date('YW', TIME)) ? ($r['weekviews'] + 1) : 1;
		$monthviews = (date('Ym', $r['updatetime']) == date('Ym', TIME)) ? ($r['monthviews'] + 1) : 1;
		$sql = array('views'=>$views,'yesterdayviews'=>$yesterdayviews,'dayviews'=>$dayviews,'weekviews'=>$weekviews,'monthviews'=>$monthviews,'updatetime'=>TIME);
		return $this->db->where(array('hitsid'=>$hitsid))->update($sql);
	}
}
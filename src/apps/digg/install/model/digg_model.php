<?php
defined('IN_YUNCMS') or exit('No permission resources.');
class digg_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'digg';
		$this->auto_check_fields = false;
		parent::__construct();
	}

	public function get($contentid, $fields = 'supports,againsts'){
		if(!$contentid) return false;
		$r = $this->where(array('id'=>$contentid))->field($fields)->find();
		if(!$r) {
			list($catid, $id) = id_decode($contentid);
			$content = Loader::model('content_model')->get_content($catid, $id);
			$r = array('id'=>$contentid,'contentid'=>$id,'catid'=>$catid,'title'=>$content['title'],'url'=>$content['url'],'supports'=>'0','againsts'=>'0');
			$this->insert($r);
		}
		return $r;
	}
}
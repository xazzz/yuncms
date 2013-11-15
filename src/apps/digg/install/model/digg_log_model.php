<?php
defined('IN_YUNCMS') or exit('No permission resources.');
class digg_log_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'digg_log';
		parent::__construct();
	}

	public function is_done($contentid){
		$where = array();
		$where['contentid'] = $contentid;
		if(cookie('userid')) $where['userid'] = cookie('userid');
		$where['ip'] = IP;
		return $this->where($where)->find();
	}

	public function add($contentid,$flag){
		$where = array();
		$where['contentid'] = $contentid;
		$where['flag'] = $flag;
		if(cookie('userid')) $where['userid'] = cookie('userid');
		if(cookie('username')) $where['username'] = cookie('username');
		$where['ip'] = IP;
		$where['datetime'] = TIME;
		return $this->insert($where);
	}
}
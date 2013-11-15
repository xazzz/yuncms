<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
// 模型缓存路径
if(!defined('CACHE_MODEL_PATH')) define ( 'CACHE_MODEL_PATH', DATA_PATH . 'model' . DIRECTORY_SEPARATOR );
class collection_node_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'collection_node';
		parent::__construct();
	}

}
?>
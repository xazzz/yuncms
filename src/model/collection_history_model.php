<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class collection_history_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'collection_history';
		parent::__construct();
	}
}
?>
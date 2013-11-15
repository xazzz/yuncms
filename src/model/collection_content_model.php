<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class collection_content_model extends Model {
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'collection_content';
		parent::__construct ();
	}
}
?>
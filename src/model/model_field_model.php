<?php
/**
 * 模型字段表
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-28
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: model_field_model.php 686 2013-07-30 03:54:01Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class model_field_model extends Model {

	public $table_name = '';

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'model_field';
		$this->auto_check_fields = false;
		parent::__construct ();
	}

	/**
	 * 删除字段
	 */
	public function drop_field($tablename, $field) {
		$tablename = $this->get_prefix () . $tablename;
		$fields = $this->get_fields ();
		if (in_array ( $field, array_keys ( $fields ) )) {
			return $this->db->execute ( "ALTER TABLE `$tablename` DROP `$field`;" );
		} else {
			return false;
		}
	}

	/**
	 * 改变数据表
	 */
	public function change_table($tablename = '') {
		if (! $tablename) return false;
		$this->table_name = $this->get_prefix () . $tablename;
		return true;
	}
}
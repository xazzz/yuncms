<?php
/**
 * UCenter远程数据库模型
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: uc_model.php 686 2013-07-30 03:54:01Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class uc_model extends Model {
	public function __construct() {
		$db_config = array (
				'hostname' => C ( 'system', 'uc_dbhost' ),
				'port' => 3306,
				'driver' => 'mysql',
				'database' => C ( 'system', 'uc_dbname' ) ,
				'username' => C ( 'system', 'uc_dbuser' ),
				'password' => C ( 'system', 'uc_dbpw' ),
				'charset' => C ( 'system', 'uc_dbcharset' ),
				'prefix' => C ( 'system', 'uc_dbtablepre' ),
				'pconnect' => false,
				'autoconnect' => false );
		$this->table_name = 'members';
		parent::__construct ( $db_config );
	}
}
?>
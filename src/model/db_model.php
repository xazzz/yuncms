<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-3-29
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: db_model.php 685 2013-07-30 03:53:05Z 85825770@qq.com $
 */
class db_model extends Model {
	public $table_name = '';
	public function __construct($setting) {
		$dbsource = S ( 'common/dbsource' );
		parent::__construct ( $dbsource [$setting] );
	}

	public function sql_query($sql) {
		if (! empty ( $this->prefix )) $sql = str_replace ( '#dbprefix#', $this->prefix, $sql );
		return parent::query ( $sql );
	}
}
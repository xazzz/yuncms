<?php
/**
 * 数据源
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-5
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: dbsource_model.php 685 2013-07-30 03:53:05Z 85825770@qq.com $
 */
class dbsource_model extends Model {

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'dbsource';
        parent::__construct();
    }
}
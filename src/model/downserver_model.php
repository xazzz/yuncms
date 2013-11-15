<?php
/**
 * 下载服务器
 * @author Tongle Xu <xutongle@gmail.com> 2012-5-31
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: downserver_model.php 685 2013-07-30 03:53:05Z 85825770@qq.com $
 */
class downserver_model extends Model {
    public $table_name = '';

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'downserver';
        parent::__construct ();
    }
}
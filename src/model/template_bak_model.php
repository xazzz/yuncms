<?php
/**
 * 模版备份表
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: template_bak_model.php 623 2013-07-29 03:40:03Z 85825770@qq.com $
 */
defined('IN_YUNCMS') or exit('No permission resources.');
class template_bak_model extends Model {

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'template_bak';
        parent::__construct();
    }
}
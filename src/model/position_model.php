<?php
/**
 * 推荐位表
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-1
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: position_model.php 686 2013-07-30 03:54:01Z 85825770@qq.com $
 */
defined('IN_YUNCMS') or exit('No permission resources.');
class position_model extends Model {

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'position';
        parent::__construct();
    }
}
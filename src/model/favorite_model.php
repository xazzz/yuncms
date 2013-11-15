<?php
defined('IN_YUNCMS') or exit('No permission resources.');
/**
 * 收藏
 * @author Tongle Xu <xutongle@gmail.com> 2012-7-3
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: favorite_model.php 685 2013-07-30 03:53:05Z 85825770@qq.com $
 */
class favorite_model extends Model {
    public $table_name = '';
    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'favorite';
        parent::__construct();
    }
}
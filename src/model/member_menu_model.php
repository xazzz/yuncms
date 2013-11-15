<?php
defined('IN_YUNCMS') or exit('No permission resources.');
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-7-2
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: member_menu_model.php 686 2013-07-30 03:54:01Z 85825770@qq.com $
 */
class member_menu_model extends Model {
    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'member_menu';
        parent::__construct();
    }
}
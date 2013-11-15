<?php
defined('IN_YUNCMS') or exit('No permission resources.');
/**
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-13
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: mood_model.php 631 2013-07-29 06:47:30Z 85825770@qq.com $
 */

class mood_model extends Model {

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'mood';
        parent::__construct();
    }
}
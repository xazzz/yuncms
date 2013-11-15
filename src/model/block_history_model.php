<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-6
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: block_history_model.php 685 2013-07-30 03:53:05Z 85825770@qq.com $
 */
class block_history_model extends Model {

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'block_history';
        parent::__construct();
    }
}
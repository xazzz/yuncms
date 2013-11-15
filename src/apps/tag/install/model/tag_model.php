<?php
/**
 * 标签表
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-5
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: tag_model.php 645 2013-07-29 09:06:40Z 85825770@qq.com $
 */
class tag_model extends Model {

    public function __construct() {
        $this->options = 'default';
        $this->table_name = 'tag';
        parent::__construct();
    }
}
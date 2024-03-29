<?php
/**
 * 单页表
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-3
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: page_model.php 686 2013-07-30 03:54:01Z 85825770@qq.com $
 */
class page_model extends Model {

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'page';
        parent::__construct ();
    }

    public function create_html($catid) {
        $this->html = Loader::lib('content:html');
        $this->html->category($catid,1);
    }
}
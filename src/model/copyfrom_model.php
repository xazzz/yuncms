<?php
/**
 * 来源
 * @author Tongle Xu <xutongle@gmail.com> 2012-5-31
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: copyfrom_model.php 685 2013-07-30 03:53:05Z 85825770@qq.com $
 */
class copyfrom_model extends Model {
    public $table_name = '';

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'copyfrom';
        parent::__construct ();
    }
}
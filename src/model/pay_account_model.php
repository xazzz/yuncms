<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: pay_account_model.php 686 2013-07-30 03:54:01Z 85825770@qq.com $
 */
class pay_account_model extends Model {
    public $table_name = '';
    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'pay_account';
        parent::__construct();
    }
}
<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
/**
 * SNS关系绑定
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-7-2
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: member_bind_model.php 686 2013-07-30 03:54:01Z 85825770@qq.com $
 */
class member_bind_model extends Model {

    public $table_name = '';

    public function __construct() {
        $this->setting = 'default';
        $this->table_name = 'member_bind';
        parent::__construct ();
    }

    public function check(){

    }

    public function bind($member, $where) {
        $member['addtime'] = TIME;
        $member_bind_check = $this->member_bind->where ( $where )->find();
        if ($member_bind_check) {
            return $this->member_bind->update ( $member, $where );
        } else {
            return $this->member_bind->insert ( $member );
        }
    }
}
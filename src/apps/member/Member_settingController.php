<?php
/**
 * 会员模块设置
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: Member_settingController.php 677 2013-07-30 03:43:44Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );

class Member_settingController extends Web_Admin {

    private $db;

    public function __construct() {
        parent::__construct ();
        $this->db = Loader::model ( 'application_model' );
    }

    public function manage() {
        if (isset ( $_POST ['dosubmit'] )) {
            $_POST ['info'] ['denyusername'] = isset ( $_POST ['info'] ['denyusername'] ) ? new_stripslashes ( trim ( $_POST ['info'] ['denyusername'] ) ) : '';
            $_POST ['info'] ['denyusername'] = explode ( "\r\n", $_POST ['info'] ['denyusername'] );
            $_POST ['info'] ['denyemail'] = isset ( $_POST ['info'] ['denyemail'] ) ? new_stripslashes ( trim ( $_POST ['info'] ['denyemail'] ) ) : '';
            $_POST ['info'] ['denyemail'] = explode ( "\r\n", $_POST ['info'] ['denyemail'] );
            $this->db->set_setting('member',$_POST ['info']);
            S ( 'member/member_setting', $_POST ['info'] );
            showmessage ( L ( 'operation_success' ), HTTP_REFERER );
        } else {
            $show_validator = $show_header = true;
            $show_scroll = true;
            $member_setting = $this->db->get_setting('member');
            include $this->view ( 'member_setting' );
        }
    }
}
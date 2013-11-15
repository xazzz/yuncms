<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );

/**
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: SpendController.php 672 2013-07-30 03:31:18Z 85825770@qq.com $
 */

class SpendController extends Web_Admin {
    private $db;

    public function __construct() {
        $this->db = Loader::model('pay_spend_model');
        parent::__construct();
    }

    public function init() {
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $where =  "";
        if (isset($_GET['dosubmit'])) {
            $username = isset($_GET['username']) && trim($_GET['username']) ? trim($_GET['username']) : '';
            $op = isset($_GET['op']) && trim($_GET['op']) ? trim($_GET['op']) : '';
            $user_type = isset($_GET['user_type']) && intval($_GET['user_type']) ? intval($_GET['user_type']) : '';
            $op_type = isset($_GET['op_type']) && intval($_GET['op_type']) ? intval($_GET['op_type']) : '';
            $type = isset($_GET['type']) && intval($_GET['type']) ? intval($_GET['type']) : '';
            $endtime = isset($_GET['endtime'])  &&  trim($_GET['endtime']) ? strtotime(trim($_GET['endtime'])) : '';
            $starttime = isset($_GET['starttime']) && trim($_GET['starttime']) ? strtotime(trim($_GET['starttime'])) : '';
            if (!empty($starttime) && empty($endtime)) {
                $endtime = TIME;
            }
            if (!empty($starttime) && !empty($endtime) && $endtime < $starttime) {
                showmessage(L('wrong_time_over_time_to_time_less_than'));
            }
            if (!empty($username) && $user_type == 1) {
            	$where['username'] = $username;
            }
            if (!empty($username) && $user_type == 2) {
            	$where['userid'] = $username;
            }
            if (!empty($starttime)) {
            	$where['creat_at'] = array('between',$starttime.','.$endtime);
            }
            if (!empty($op) && $op_type == 1) {
            	$where['op_username']=$op;
            } elseif (!empty($op) && $op_type == 2) {
            	$where['op_userid']=$op;
            }
            if (!empty($type)) {
            	$where['type']=$type;
            }
        }
        $list = $this->db->where($where)->order('id desc')->listinfo($page);
        $pages = $this->db->pages;
        include $this->view('spend_list');
    }
}
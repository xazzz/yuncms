<?php
/**
 * Ucneter配置
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-4
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: Ucenter_settingController.php 677 2013-07-30 03:43:44Z 85825770@qq.com $
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );

class Ucenter_settingController extends Web_Admin {
    private $db;

    public function __construct() {
        parent::__construct ();
        $this->db = Loader::model ( 'application_model' );
    }

    public function init() {
        if (isset ( $_POST ['dosubmit'] )) {
            $setuc = isset ( $_POST ['setuc'] ) ? $_POST ['setuc'] : '';
            Base_Config::modify ( 'system', $setuc );
            unset ( $setuc ['uc_use'] );
            // 保持进UC配置文件
            $setuc ['uc_dbtablepre'] = $setuc ['uc_dbname'] . '.' . $setuc ['uc_dbtablepre'];
            $uc_config = '<?php ' . "\ndefine('UC_CONNECT', 'mysql');\n";
            foreach ( $setuc as $k => $v ) {
                $uc_config .= "define('" . strtoupper ( $k ) . "', '$v');\n";
            }
            $uc_config .= "define('UC_PPP', '20');\n";
            $uc_config_filepath = WEKIT_PATH . 'config' . DIRECTORY_SEPARATOR . 'uc_config.php';
            @file_put_contents ( $uc_config_filepath, $uc_config );
            showmessage ( L ( 'operation_success' ), HTTP_REFERER );
        } else {
            $uc_config = C ( 'system' );
            extract ( $uc_config );
            include $this->view ( 'member_ucenter' );
        }
    }

    /**
     * 测试UCenter数据库设置
     * Enter description here .
     * ..
     */
    public function public_myqsl_test() {
        $host = isset ( $_POST ['host'] ) && trim ( $_POST ['host'] ) ? trim ( $_POST ['host'] ) : exit ( '0' );
        $password = isset ( $_POST ['password'] ) && trim ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : exit ( '0' );
        $username = isset ( $_POST ['username'] ) && trim ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : exit ( '0' );
        if (@mysql_connect ( $host, $username, $password )) {
            exit ( '1' );
        } else {
            exit ( '0' );
        }
    }
}
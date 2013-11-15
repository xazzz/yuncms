<?php
defined('IN_YUNCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',DATA_PATH.'model'.DIRECTORY_SEPARATOR);

/**
 * 文件下载
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-12
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: DownController.php 690 2013-07-30 04:11:08Z 85825770@qq.com $
 */
class DownController {
    private $db;
    private $auth_key;

    public function __construct() {
        $this->db = Loader::model('content_model');
        $this->auth_key = C('framework','auth_key');
    }

    public function init() {
        $a_k = trim($_GET['a_k']);
        if(!isset($a_k)) showmessage(L('illegal_parameters'));
        $a_k = String::authcode($a_k, 'DECODE', $this->auth_key);
        if(empty($a_k)) showmessage(L('illegal_parameters'));
        unset($i,$m,$f);
        parse_str($a_k);
        if(isset($i)) $i = $id = intval($i);
        if(!isset($m)) showmessage(L('illegal_parameters'));
        if(!isset($modelid)||!isset($catid)) showmessage(L('illegal_parameters'));
        if(empty($f)) showmessage(L('url_invalid'));
        $allow_visitor = 1;
        $MODEL = S('common/model');
        $tablename = $this->db->table_name = $this->db->get_prefix().$MODEL[$modelid]['tablename'];
        $this->db->table_name = $tablename.'_data';
        $rs = $this->db->getby_id($id);
        $CATEGORYS = S('common/category_content');

        $this->category = $CATEGORYS[$catid];
        $this->category_setting = string2array($this->category['setting']);

        //检查文章会员组权限
        $groupids_view = '';
        if (isset($rs['groupids_view']) && !empty($rs['groupids_view'])) $groupids_view = explode(',', $rs['groupids_view']);
        if($groupids_view && is_array($groupids_view)) {
            $_groupid = cookie('_groupid');
            $_groupid = intval($_groupid);
            if(!$_groupid) {
                $forward = urlencode(Base_Request::get_url());
                showmessage(L('login_website'),SITE_URL.'index.php?app=member&controller=index&action=login&forward='.$forward);
            }
            if(!in_array($_groupid,$groupids_view)) showmessage(L('no_priv'));
        } else {
            //根据栏目访问权限判断权限
            $_priv_data = $this->_category_priv($catid);
            if($_priv_data=='-1') {
                $forward = urlencode(Base_Request::get_url());
                showmessage(L('login_website'),SITE_URL.'index.php?app=member&controller=index&action=login&forward='.$forward);
            } elseif($_priv_data=='-2') {
                showmessage(L('no_priv'));
            }
        }
        //阅读收费 类型
        $paytype = !empty($rs['paytype']) ? $rs['paytype'] : '0';
        $readpoint = !empty($rs['readpoint']) ? $rs['readpoint'] : '0';
        if($readpoint || $this->category_setting['defaultchargepoint']) {
            if(!$readpoint) {
                $readpoint = $this->category_setting['defaultchargepoint'];
                $paytype = $this->category_setting['paytype'];
            }
            //检查是否支付过
            $allow_visitor = self::_check_payment($catid.'_'.$id,$paytype,$catid);
            if(!$allow_visitor) {
                $http_referer = urlencode(Base_Request::get_url());
                $allow_visitor = String::authcode($catid.'_'.$id.'|'.$readpoint.'|'.$paytype).'&http_referer='.$http_referer;
            } else {
                $allow_visitor = 1;
            }
        }
        if(preg_match('/(php|phtml|php3|php4|jsp|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i',$f) || strpos($f, ":\\")!==FALSE || strpos($f,'..')!==FALSE) showmessage(L('url_error'));
        if(strpos($f, 'http://') !== FALSE || strpos($f, 'ftp://') !== FALSE || strpos($f, '://') === FALSE) {
            $yun_auth_key = md5($this->auth_key.$_SERVER['HTTP_USER_AGENT']);
            $a_k = urlencode(String::authcode("i=$i&d=$d&s=$s&t=".TIME."&ip=".IP."&m=".$m."&f=$f&modelid=".$modelid, 'ENCODE', $yun_auth_key));
            $downurl = '?app=content&controller=down&action=download&a_k='.$a_k;
        } else {
            $downurl = $f;
        }
        header('HTTP/1.1 301 Moved Permanently');//发出301头部
       	header('Location: '.$downurl);//跳转到你希望的地址格式
    }

    public function download() {
        $a_k = trim($_GET['a_k']);
        $yun_auth_key = md5($this->auth_key.$_SERVER['HTTP_USER_AGENT']);
        $a_k = String::authcode($a_k, 'DECODE', $yun_auth_key);
        if(empty($a_k)) showmessage(L('illegal_parameters'));
        unset($i,$m,$f,$t,$ip);
        parse_str($a_k);
        if(isset($i)) $downid = intval($i);
        if(!isset($m)) showmessage(L('illegal_parameters'));
        if(!isset($modelid)) showmessage(L('illegal_parameters'));
        if(empty($f)) showmessage(L('url_invalid'));
        if(!$i || $m<0) showmessage(L('illegal_parameters'));
        if(!isset($t)) showmessage(L('illegal_parameters'));
        if(!isset($ip)) showmessage(L('illegal_parameters'));
        $starttime = intval($t);
        if(preg_match('/(php|phtml|php3|php4|jsp|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i',$f) || strpos($f, ":\\")!==FALSE || strpos($f,'..')!==FALSE) showmessage(L('url_error'));
        $fileurl = trim($f);
        if(!isset($downid) || empty($fileurl) || !preg_match("/[0-9]{10}/", $starttime) || !preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $ip) || $ip != IP) showmessage(L('illegal_parameters'));
        $endtime = TIME - $starttime;
        if($endtime > 3600) showmessage(L('url_invalid'));
        if($m) $fileurl = trim($s).trim($fileurl);
        //远程文件
        if(strpos($fileurl, ':/') && (strpos($fileurl, C('attachment','upload_url')) === false)) {
            header("Location: $fileurl");
        } else {
            if($d == 0) {
                header("Location: ".$fileurl);
            } else {
                $fileurl = str_replace(array(C('attachment','upload_url'),'/'), array(C('attachment','upload_path'),DIRECTORY_SEPARATOR), $fileurl);
                $filename = basename($fileurl);
                //处理中文文件
                if(preg_match("/^([\s\S]*?)([\x81-\xfe][\x40-\xfe])([\s\S]*?)/", $fileurl)) {
                    $filename = str_replace(array("%5C", "%2F", "%3A"), array("\\", "/", ":"), urlencode($fileurl));
                    $filename = urldecode(basename($filename));
                }
                $ext = File::get_suffix($filename);
                $filename = date('Ymd_his').String::rand_string(3).'.'.$ext;
                File::down($fileurl, $filename);
            }
        }
    }

    /**
     * 检查支付状态
     */
    private function _check_payment($flag,$paytype,$catid) {
        $_userid = cookie('_userid');
        $_username = cookie('_username');
        $CATEGORYS = S('common/category_content');
        $this->category = $CATEGORYS[$catid];
        $this->category_setting = string2array($this->category['setting']);
        if(!$_userid) return false;
        Loader::lib('pay:spend',false);
        $setting = $this->category_setting;
        $repeatchargedays = intval($setting['repeatchargedays']);
        if($repeatchargedays) {
            $fromtime = TIME - 86400 * $repeatchargedays;
            $r = spend::spend_time($_userid,$fromtime,$flag);
            if($r['id']) return true;
        }
        return false;
    }

    /**
     * 检查阅读权限
     *
     */
    private function _category_priv($catid) {
        $catid = intval($catid);
        if(!$catid) return '-2';
        $_groupid = cookie('_groupid');
        $_groupid = intval($_groupid);
        if($_groupid==0) $_groupid = 8;
        $this->category_priv_db = Loader::model('category_priv_model');
        $result = $this->category_priv_db->where(array('catid'=>$catid,'is_admin'=>0,'action'=>'visit'))->select();
        if($result) {
            if(!$_groupid) return '-1';
            foreach($result as $r) {
                if($r['roleid'] == $_groupid) return '1';
            }
            return '-1';
        } else {
            return '1';
        }
    }
}
<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-12
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: Bank.php 672 2013-07-30 03:31:18Z 85825770@qq.com $
 */
if (isset($set_modules) && $set_modules == TRUE){
    $i = isset($modules) ? count($modules) : 0;
    $modules[$i]['code']    = basename(__FILE__, '.php');
    $modules[$i]['name']    = L('bank_transfer', '', 'pay');
    $modules[$i]['desc']    = L('transfer', '', 'pay');
    $modules[$i]['is_cod']  = '0';
    $modules[$i]['is_online']  = '0';
    $modules[$i]['author']  = '	YUNCMS开发团队';
    $modules[$i]['website'] = '';
    $modules[$i]['version'] = '1.0.0';
    $modules[$i]['config']  = array();
    return;
}
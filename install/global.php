<?php
/**
 * @author Tongle Xu <xutongle@gmail.com> 2012-6-20
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: global.php 681 2013-07-30 03:47:24Z 85825770@qq.com $
 */
function format_textarea($string) {
    return nl2br ( str_replace ( ' ', '&nbsp;', htmlspecialchars ( $string ) ) );
}

function dir_writeable($dir) {
    $writeable = 0;
    if (is_dir ( $dir )) {
        if ($fp = @fopen ( "$dir/chkdir.test", 'w' )) {
            @fclose ( $fp );
            @unlink ( "$dir/chkdir.test" );
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}

function _sql_execute($sql) {
    $sqls = _sql_split ( $sql );
    if (is_array ( $sqls )) {
        foreach ( $sqls as $sql ) {
            if (trim ( $sql ) != '') {
                Loader::model('get_model')->execute($sql);
            }
        }
    } else {
        Loader::model('get_model')->execute($sql);
    }
    return true;
}

function _sql_split($sql) {
    $sql = str_replace ( "\r", "\n", $sql );
    $ret = array ();
    $num = 0;
    $queriesarray = explode ( ";\n", trim ( $sql ) );
    unset ( $sql );
    foreach ( $queriesarray as $query ) {
        $ret [$num] = '';
        $queries = explode ( "\n", trim ( $query ) );
        $queries = array_filter ( $queries );
        foreach ( $queries as $query ) {
            $str1 = substr ( $query, 0, 1 );
            if ($str1 != '#' && $str1 != '-')
                $ret [$num] .= $query;
        }
        $num ++;
    }
    return $ret;
}
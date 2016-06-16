<?php

/**
 *
 * 不同的访问途径，加载不同的视图。
 *
 *
 *
 *
 *
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*global $_G;
echo '<pre>';
var_dump($_G);
echo '</pre>';

exit();*/
if($_G['uid'] <=0 ){
    header('location:http://bbs.wuwenfu.cn/');
}


if(!checkmobile()){
    //pc
    include_once template('htt_greatwall:index');
}else{
    //mobile
//    echo 11;

    include_once template('htt_greatwall:index_mobile');
}



?>
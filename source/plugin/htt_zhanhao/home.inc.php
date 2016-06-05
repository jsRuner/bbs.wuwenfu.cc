<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;

loadcache('plugin');

$var = $_G['cache']['plugin'];
$is_open =  $var['htt_zhanhao']['is_open'];
$is_open =  $var['htt_zhanhao']['is_open'];
$is_open =  $var['htt_zhanhao']['is_open'];
$is_open =  $var['htt_zhanhao']['is_open'];
$copyright =  $var['htt_zhanhao']['copyright'];


/*2个页面
一个是列表。列表直接显示账号。账号和密码处于加密状态。

点击提取按钮，可以直接提取。

如果无法提取，则提示分享网站。

只显示100个。


*/
/*
echo '<pre>';
var_dump($_G['setting']['bbname']);
var_dump($_G);
echo '</pre>';
exit();*/
//读取分类信息。
$cid = isset($_GET['cid'])?intval($_GET['cid']):0;

$categorys= C::t('#htt_zhanhao#category')->fetch_all();

if(empty($categorys)){
    die('no cate');
}

if($cid ==0){
    $cid = $categorys[0]['id'];
}


$newcategorys = array();
foreach($categorys as $k=>$v){
    $newcategorys[$v['id']] = $v;
}

$date = date('Y-m-d');


//查询账号与密码
$srchadd ='';
$page = 1;
$ppp = 100;

$zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);

//print_r($zhanhaos);





include_once template('htt_zhanhao:home');

exit();



?>
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


/*2��ҳ��
һ�����б��б�ֱ����ʾ�˺š��˺ź����봦�ڼ���״̬��

�����ȡ��ť������ֱ����ȡ��

����޷���ȡ������ʾ������վ��

ֻ��ʾ100����


*/
/*
echo '<pre>';
var_dump($_G['setting']['bbname']);
var_dump($_G);
echo '</pre>';
exit();*/
//��ȡ������Ϣ��
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


//��ѯ�˺�������
$srchadd ='';
$page = 1;
$ppp = 100;

$zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);

//print_r($zhanhaos);





include_once template('htt_zhanhao:home');

exit();



?>
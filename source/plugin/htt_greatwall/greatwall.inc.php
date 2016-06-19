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

global $_G;


loadcache('plugin');

$var = $_G['cache']['plugin'];
$plugin_switch = $var['htt_greatwall']['switch'];

if($plugin_switch !=1 ){
    die('插件处于关闭状态');
}




/*global $_G;
echo '<pre>';
var_dump($_G);
echo '</pre>';

exit();*/
if($_G['uid'] <=0 ){

    showmessage('需要登录以后才可以参加活动',$_G['siteurl']);
//    header('location:http://bbs.wuwenfu.cn/');
}

$project_id = intval($_GET['project']);
if($project_id <0){
    die('请指定要访问的项目');
}

//查询项目的时间。
$project = C::t('#htt_greatwall#project')->fetch_by_pid($project_id);

//项目不存在
if(!$project){
    die('活动不存在');
}

$config = json_decode($project['config'],true);
$project['start_date'] = $config['start_date'];
$project['end_date'] = $config['end_date'];

if( $project['end_date'] < date('Y-m-d H:i:s')  ){
    die('活动已经结束');
}


if( $project['start_date'] > date('Y-m-d H:i:s')  ){
    die('活动还没有开始');
}

//查询用户是否已经中奖了。如果已经提交了中奖的信息。则界面上显示。其他的。
 $prize_logs = C::t('#htt_greatwall#prize_log')->fetch_all_by_search(' AND project_id = '.$project_id.' AND member_id = '.$_G[uid],0,1000);
//只取一条中奖记录。默认只能参与一次
If($prize_logs){
    $prize_log = $prize_logs[0];
}




//读取设置的红包奖品。需要有6个奖品。不中奖也算一个奖品。例如0元红包。如果没有6个奖品，则不足的全部设置为0红包

#获取奖品列表。
$search = ' AND project_id = '.intval($_GET['project']).' AND prizes_nums != 0 AND status = \'1\'';
$prize_arr = C::t('#htt_greatwall#prize')->fetch_all_by_search($search,0,1000);




if(count($prize_arr) < 6){
    die('奖品设置错误:必须有6个奖品设置');
}

//一等奖
$prize[1] = $prize_arr[0]['price'];
//二等奖
$prize[4] = $prize_arr[1]['price'];
$prize[12] = $prize_arr[1]['price'];
//三等奖
$prize[6] = $prize_arr[2]['price'];
$prize[9] = $prize_arr[2]['price'];

$prize[2] = $prize_arr[3]['price'];
$prize[11] = $prize_arr[3]['price'];


$prize[3] = $prize_arr[4]['price'];
$prize[8] = $prize_arr[4]['price'];
$prize[7] = $prize_arr[4]['price'];



$prize[5] = $prize_arr[5]['price'];
$prize[10] = $prize_arr[5]['price'];











if(!checkmobile()){
    //pc
    include_once template('htt_greatwall:index');
}else{
    //mobile
//    echo 11;

    include_once template('htt_greatwall:index_mobile');
}



?>
<?php

/**
 *
 * ��ͬ�ķ���;�������ز�ͬ����ͼ��
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
    die('������ڹر�״̬');
}




/*global $_G;
echo '<pre>';
var_dump($_G);
echo '</pre>';

exit();*/
if($_G['uid'] <=0 ){

    showmessage('��Ҫ��¼�Ժ�ſ��Բμӻ',$_G['siteurl']);
//    header('location:http://bbs.wuwenfu.cn/');
}

$project_id = intval($_GET['project']);
if($project_id <0){
    die('��ָ��Ҫ���ʵ���Ŀ');
}

//��ѯ��Ŀ��ʱ�䡣
$project = C::t('#htt_greatwall#project')->fetch_by_pid($project_id);

//��Ŀ������
if(!$project){
    die('�������');
}

$config = json_decode($project['config'],true);
$project['start_date'] = $config['start_date'];
$project['end_date'] = $config['end_date'];

if( $project['end_date'] < date('Y-m-d H:i:s')  ){
    die('��Ѿ�����');
}


if( $project['start_date'] > date('Y-m-d H:i:s')  ){
    die('���û�п�ʼ');
}

//��ѯ�û��Ƿ��Ѿ��н��ˡ�����Ѿ��ύ���н�����Ϣ�����������ʾ�������ġ�
 $prize_logs = C::t('#htt_greatwall#prize_log')->fetch_all_by_search(' AND project_id = '.$project_id.' AND member_id = '.$_G[uid],0,1000);
//ֻȡһ���н���¼��Ĭ��ֻ�ܲ���һ��
If($prize_logs){
    $prize_log = $prize_logs[0];
}




//��ȡ���õĺ����Ʒ����Ҫ��6����Ʒ�����н�Ҳ��һ����Ʒ������0Ԫ��������û��6����Ʒ�������ȫ������Ϊ0���

#��ȡ��Ʒ�б�
$search = ' AND project_id = '.intval($_GET['project']).' AND prizes_nums != 0 AND status = \'1\'';
$prize_arr = C::t('#htt_greatwall#prize')->fetch_all_by_search($search,0,1000);




if(count($prize_arr) < 6){
    die('��Ʒ���ô���:������6����Ʒ����');
}

//һ�Ƚ�
$prize[1] = $prize_arr[0]['price'];
//���Ƚ�
$prize[4] = $prize_arr[1]['price'];
$prize[12] = $prize_arr[1]['price'];
//���Ƚ�
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
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/4/23
 * Time: 2016-4-23 8:53:02
 * description:
 *
 * 
 * 
 * ��������
 * ��ע��ȡ����ע
 * 
 *
 *
 */
//http://bbs.wuwenfu.cc/plugin.php?id=htt_baidu:guanzhu ���ʸ�ҳ���url
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}




if ($_GET['formhash']!= FORMHASH) {
    showmessage('undefined_action');
}

global $_G;
loadcache('plugin');
$var = $_G['cache']['plugin'];
$cache_time =  $var['htt_baidu']['cache_time'];
$credit_title =  $var['htt_baidu']['credit_title'];
$level_title =  $var['htt_baidu']['level_title'];

$show_style =  $var['htt_baidu']['show_style']; // 1���� 2�ȼ� 3���߶�
$del_credit =  $var['htt_baidu']['del_credit']; //ɾ������ 1���� 2ɾ��  todo ��Ч����

$show_num =  $var['htt_baidu']['show_num']; //��ע�� 1��ʾ 2����ʾ


#���û�е�¼������ʾ��Ҫ��¼��



$uid = intval($_G['uid']);

if ($uid<=0) {
	showmessage(lang('plugin/htt_baidu','yes_guanzhu_login'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>2,'alert'=>'right','showmsg'=>true,'msgtype'=>2));
	return;
}

// $fid = $_GET['fid'];
//����ע��
$fid = intval(getgpc('fid','G'));




$favid = $fid;

// $guanzhu = $_GET['guanzhu']; #�������ǹ�ע.todo:���������ж�

#��ѯ�Ƿ���ڡ�
$guanzhuinfo = '';
$query = DB::query("SELECT * FROM  ".DB::table("httbaidu")." WHERE  `fid`=$fid and `uid`=$uid ");
while($item = DB::fetch($query)) {
	// var_dump($item);
	$guanzhuinfo = $item;
}

$extrajs = '';



if(empty($guanzhuinfo)){

	$insert_array = array(
		'uid'=>$uid,
		'fid'=>$fid,
		'dateline'=>time(),
		);
	DB::insert('httbaidu',$insert_array);

	$guanzhustr = lang("plugin/htt_baidu",'yes_guanzhu');

	$extrajs = '<script type="text/javascript">$("a_guanzhu_text").innerHTML="'.$guanzhustr.'";$("number_guanzhu_num").innerHTML = parseInt($("number_guanzhu_num").innerHTML)+1;</script>';
showmessage(lang('plugin/htt_baidu','guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>3,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));


}else{
	#ɾ������.�޸�Ϊ״̬������status Ĭ��1 ��ʾ��ע 0��ʾȡ����ע
	#ֻҪ��ע�ˣ���ض����ڼ�¼�����ڵ�ȡ��ֻ���޸�״̬��todo ����ʵ�֡�

	DB::query("delete from ".DB::table("httbaidu")." where `uid`=$uid and `fid`=$fid");

	$guanzhustr = lang("plugin/htt_baidu",'no_guanzhu');


	// $extrajs = '<script type="text/javascript">window.setTimeout("window.location.reload();",3000);</script>';
	$extrajs = '<script type="text/javascript">$("a_guanzhu_text").innerHTML="'.$guanzhustr.'";$("number_guanzhu_num").innerHTML = parseInt($("number_guanzhu_num").innerHTML)-1;</script>';
	// showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'),'http://bbs.wuwenfu.cc/forum.php?mod=forumdisplay&fid=36',array(),array('timeout'=>'3','alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'function'));
	showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>3,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));
	
}





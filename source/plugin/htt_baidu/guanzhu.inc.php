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
#���û�е�¼������ʾ��Ҫ��¼��


$uid = intval($_G['uid']);

// if (empty($uid) || $uid<=0) {
// 	showmessage('321321321', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1);
// 	// return;
// 	exit();
// }
if ($uid<=0) {
		// showmessage('321321321', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));
	// cpmsg('1111', '', 'error');
	showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>2,'alert'=>'right','showmsg'=>true,'msgtype'=>2));
	return;
}

#��ȡ������uid fid 
// $uid = $_GET['uid'];




$fid = $_GET['fid'];




$favid = $fid;

$guanzhu = $_GET['guanzhu']; #�������ǹ�ע.todo:���������ж�

#��ѯ�Ƿ���ڡ�
$guanzhuinfo = '';
$query = DB::query("SELECT * FROM  `pre_httbaidu` WHERE  `fid`=$fid and `uid`=$uid LIMIT 0 , 30");
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

	// $extrajs = '<script type="text/javascript">window.setTimeout("window.location.reload();",3000); </script>';
	$extrajs = '<script type="text/javascript">$("a_guanzhu_text").innerHTML="'.$guanzhustr.'";$("number_guanzhu_num").innerHTML = parseInt($("number_guanzhu_num").innerHTML)+1;</script>';

//��Ҫ�о�һ����Ĳ�������.Ҫ֧��ִ��js.�޸�״̬.û��ˢ��ҳ�档���µ��쳣
// showmessage(lang('plugin/htt_baidu','guanzhu_success'),'http://bbs.wuwenfu.cc/forum.php?mod=forumdisplay&fid=36',array(),array('timeout'=>'3','alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'function'));
showmessage(lang('plugin/htt_baidu','guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>2,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));


}else{
	#ɾ������
	DB::query("delete from `pre_httbaidu` where `uid`=$uid and `fid`=$fid");

	$guanzhustr = lang("plugin/htt_baidu",'no_guanzhu');


	// $extrajs = '<script type="text/javascript">window.setTimeout("window.location.reload();",3000);</script>';
	$extrajs = '<script type="text/javascript">$("a_guanzhu_text").innerHTML="'.$guanzhustr.'";$("number_guanzhu_num").innerHTML = parseInt($("number_guanzhu_num").innerHTML)-1;</script>';
	// showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'),'http://bbs.wuwenfu.cc/forum.php?mod=forumdisplay&fid=36',array(),array('timeout'=>'3','alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'function'));
	showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>2,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));
	
}





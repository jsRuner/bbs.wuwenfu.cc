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

#��ȡ������uid fid 
$uid = $_GET['uid'];
$fid = $_GET['fid'];

$guanzhu = $_GET['guanzhu']; #�������ǹ�ע.todo:���������ж�

#��ѯ�Ƿ���ڡ�
$guanzhuinfo = '';
$query = DB::query("SELECT * FROM  `pre_httbaidu` WHERE  `fid`=$fid and `uid`=$uid LIMIT 0 , 30");
while($item = DB::fetch($query)) {
	// var_dump($item);
	$guanzhuinfo = $item;
}

if(empty($guanzhuinfo)){

	$insert_array = array(
		'uid'=>$uid,
		'fid'=>$fid,
		'dateline'=>time(),
		);
	DB::insert('httbaidu',$insert_array);

//��Ҫ�о�һ����Ĳ�������.Ҫ֧��ִ��js.�޸�״̬.û��ˢ��ҳ�档���µ��쳣
showmessage(lang('plugin/htt_baidu','guanzhu_success'),'',array(),array('alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'false'));


}else{
	#ɾ������
	DB::query("delete from `pre_httbaidu` where `uid`=$uid and `fid`=$fid");
	showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'),'',array(),array('alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'false'));
	
}





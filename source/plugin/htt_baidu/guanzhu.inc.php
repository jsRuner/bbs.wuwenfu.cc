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

$guanzhu = $_GET['guanzhu']; #�������ǹ�ע

if($guanzhu!='yes'){

	$insert_array = array(
		'uid'=>$uid,
		'fid'=>$fid,
		'dateline'=>time(),
		);
	DB::insert('httbaidu',$insert_array);

}else{
	#ɾ������
	DB::query("delete from `pre_httbaidu` where `uid`=$uid and `fid`=$fid");
	
}

//��Ҫ�о�һ����Ĳ�������.Ҫ֧��ִ��js.�޸�״̬
showmessage('do_success', '', array(), array('showdialog'=>1, 'showmsg' => true, 'closetime' => true, 'locationtime' => 3));





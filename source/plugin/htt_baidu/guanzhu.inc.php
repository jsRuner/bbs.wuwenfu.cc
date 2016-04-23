<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/4/23
 * Time: 2016-4-23 8:53:02
 * description:
 *
 * 
 * 
 * 接收请求
 * 关注和取消关注
 * 
 *
 *
 */
//http://bbs.wuwenfu.cc/plugin.php?id=htt_baidu:guanzhu 访问该页面的url
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if ($_GET['formhash']!= FORMHASH) {
    showmessage('undefined_action');
}

#获取参数。uid fid 
$uid = $_GET['uid'];
$fid = $_GET['fid'];

$guanzhu = $_GET['guanzhu']; #存在则是关注

if($guanzhu!='yes'){

	$insert_array = array(
		'uid'=>$uid,
		'fid'=>$fid,
		'dateline'=>time(),
		);
	DB::insert('httbaidu',$insert_array);

}else{
	#删除操作
	DB::query("delete from `pre_httbaidu` where `uid`=$uid and `fid`=$fid");
	
}

//需要研究一下这的参数传递.要支持执行js.修改状态
showmessage('do_success', '', array(), array('showdialog'=>1, 'showmsg' => true, 'closetime' => true, 'locationtime' => 3));





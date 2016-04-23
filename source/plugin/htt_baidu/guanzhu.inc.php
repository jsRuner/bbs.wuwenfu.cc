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

$guanzhu = $_GET['guanzhu']; #存在则是关注.todo:不根据它判断

#查询是否存在。
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

//需要研究一下这的参数传递.要支持执行js.修改状态.没有刷新页面。导致的异常
showmessage(lang('plugin/htt_baidu','guanzhu_success'),'',array(),array('alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'false'));


}else{
	#删除操作
	DB::query("delete from `pre_httbaidu` where `uid`=$uid and `fid`=$fid");
	showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'),'',array(),array('alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'false'));
	
}





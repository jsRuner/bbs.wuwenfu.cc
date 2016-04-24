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

$favid = $fid;

$guanzhu = $_GET['guanzhu']; #存在则是关注.todo:不根据它判断

#查询是否存在。
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

//需要研究一下这的参数传递.要支持执行js.修改状态.没有刷新页面。导致的异常
// showmessage(lang('plugin/htt_baidu','guanzhu_success'),'http://bbs.wuwenfu.cc/forum.php?mod=forumdisplay&fid=36',array(),array('timeout'=>'3','alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'function'));
showmessage(lang('plugin/htt_baidu','guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>2,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));


}else{
	#删除操作
	DB::query("delete from `pre_httbaidu` where `uid`=$uid and `fid`=$fid");

	$guanzhustr = lang("plugin/htt_baidu",'no_guanzhu');


	// $extrajs = '<script type="text/javascript">window.setTimeout("window.location.reload();",3000);</script>';
	$extrajs = '<script type="text/javascript">$("a_guanzhu_text").innerHTML="'.$guanzhustr.'";$("number_guanzhu_num").innerHTML = parseInt($("number_guanzhu_num").innerHTML)-1;</script>';
	// showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'),'http://bbs.wuwenfu.cc/forum.php?mod=forumdisplay&fid=36',array(),array('timeout'=>'3','alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'function'));
	showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>2,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));
	
}





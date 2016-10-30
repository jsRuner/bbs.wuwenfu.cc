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

global $_G;
loadcache('plugin');
$var = $_G['cache']['plugin'];
$cache_time =  $var['htt_baidu']['cache_time'];
$credit_title =  $var['htt_baidu']['credit_title'];
$level_title =  $var['htt_baidu']['level_title'];

$show_style =  $var['htt_baidu']['show_style']; // 1积分 2等级 3两者都
$del_credit =  $var['htt_baidu']['del_credit']; //删除积分 1保留 2删除  todo 无效设置

$show_num =  $var['htt_baidu']['show_num']; //关注量 1显示 2不显示

$guanzhu_button_txt =  $var['htt_baidu']['guanzhu_button_txt']; //关注按钮的文字
$rank_num =  $var['htt_baidu']['rank_num']; //排行榜显示的人数


#如果没有登录，则提示需要登录。



$uid = intval($_G['uid']);

if ($uid<=0) {
	showmessage(lang('plugin/htt_baidu','yes_guanzhu_login'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>2,'alert'=>'right','showmsg'=>true,'msgtype'=>2));
	return;
}

$fid = $_GET['fid'];




$favid = $fid;

$guanzhu = $_GET['guanzhu']; #存在则是关注.todo:不根据它判断

#查询是否存在。
$guanzhuinfo = '';
$query = DB::query("SELECT * FROM  ".DB::table("httbaidu")." WHERE  `fid`=$fid and `uid`=$uid LIMIT 0 , 30");
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

	$guanzhustr = lang("plugin/htt_baidu",'yes_guanzhu').$guanzhu_button_txt;

	$extrajs = '<script type="text/javascript">$("a_guanzhu_text").innerHTML="'.$guanzhustr.'";$("number_guanzhu_num").innerHTML = parseInt($("number_guanzhu_num").innerHTML)+1;</script>';
showmessage(lang('plugin/htt_baidu','guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>3,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));


}else{
	#删除操作.修改为状态操作。status 默认1 表示关注 0表示取消关注
	#只要关注了，则必定存在记录。后期的取消只是修改状态。todo 后期实现。

	DB::query("delete from ".DB::table("httbaidu")." where `uid`=$uid and `fid`=$fid");

	$guanzhustr = lang("plugin/htt_baidu",'no_guanzhu').$guanzhu_button_txt;


	// $extrajs = '<script type="text/javascript">window.setTimeout("window.location.reload();",3000);</script>';
	$extrajs = '<script type="text/javascript">$("a_guanzhu_text").innerHTML="'.$guanzhustr.'";$("number_guanzhu_num").innerHTML = parseInt($("number_guanzhu_num").innerHTML)-1;</script>';
	// showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'),'http://bbs.wuwenfu.cc/forum.php?mod=forumdisplay&fid=36',array(),array('timeout'=>'3','alert'=>'right','showmsg'=>true,'msgtype'=>2,'closetime'=>2,'handle'=>'function'));
	showmessage(lang('plugin/htt_baidu','quxiao_guanzhu_success'), dreferer(), array('id' => $id, 'favid' => $favid), array('closetime'=>3,'alert'=>'right','showmsg'=>true,'msgtype'=>2, 'extrajs' => $extrajs));
	
}





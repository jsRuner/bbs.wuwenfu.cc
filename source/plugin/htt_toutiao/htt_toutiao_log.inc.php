<?php
/**
 *	[糗事百科] (C)2016-2099 Powered by 北岸的云.
 *	Version: 1.0
 *	Date: 2016-4-18 21:22
 *	http://bbs.wuwenfu.cc/plugin.php?id=htt_toutiao:guanzhu
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

require_once libfile('function/forumlist');
loadcache('forums');

define('PMODURL', 'action=plugins&operation&config&identifier=htt_toutiao&pmod=htt_toutiao_log&ac=');

$action = $_GET['ac'];

switch ($action) {
	case 'delall':
		# code...
		DB::query("delete FROM ".DB::table("htt_toutiao_log"));
		updatecache(array('plugin', 'setting'));
		cpmsg(lang('plugin/htt_toutiao', 'show_del_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_toutiao&pmod=htt_toutiao_log', 'succeed');
		
		break;
	

	case 'del':

		if(submitcheck('submit')) {
		foreach($_GET['delete'] as $delete) {
			DB::query("delete FROM ".DB::table("htt_toutiao_log")." where `id`= $delete");
		}
		updatecache(array('plugin', 'setting'));
		cpmsg(lang('plugin/htt_toutiao', 'show_del_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_toutiao&pmod=htt_toutiao_log', 'succeed');
		}
	break;
	
	default:
		$level_list = array();
    	$query = DB::query("SELECT * FROM  ".DB::table("htt_toutiao_log")." order by `stime` desc  ");
		while($item = DB::fetch($query)) {
			$level_list[] = $item;
		}
		showtips(lang('plugin/htt_toutiao', 'qsbk_tips'));
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_toutiao&pmod=htt_toutiao_log&ac=del', 'enctype');
		showtableheader();
		echo '<tr class="header"><th></th><th>'.lang('plugin/htt_toutiao', 'stime').'</th><th>'.
			lang('plugin/htt_toutiao', 'raw_content').'</th><th>'.
			lang('plugin/htt_toutiao', 'content').'</th><th>'.
			lang('plugin/htt_toutiao', 'num').'</th><th>'.
			lang('plugin/htt_toutiao', 'ids').'</th>
			<th></th></tr>';
		foreach($level_list as $tid => $level) {
			echo '<tr class="hover">
			<th class="td25"><input class="checkbox" type="checkbox" name="delete['.$level['id'].']" value="'.$level['id'].'"></th>
			<th>'.date('Y-m-d H:i:s',$level['stime']).'</th>
			<th>'.strlen($level['raw_content']).'</th>
			<th>'.strlen($level['content']).'</th>
			<th>'.$level['num'].'</th>
			<th>'.$level['ids'].'</th></tr>
			';
		}
		$add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_toutiao&pmod=htt_toutiao_log&ac=delall\'" value="'.lang('plugin/htt_toutiao', 'show_delall').'" />';
		
		if($level_list) {
			showsubmit('submit', lang('plugin/htt_toutiao', 'show_del'), $add, '', $multipage);
		} 
		showtablefooter();
		showformfooter();
		break;
}
?>
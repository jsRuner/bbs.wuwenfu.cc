<?php
/**
 *	[鐧惧害璐村惂] (C)2016-2099 Powered by 鍖楀哺鐨勪簯.
 *	Version: 1.0
 *	Date: 2016-4-18 21:22
 *	http://bbs.wuwenfu.cc/plugin.php?id=htt_baidu:guanzhu
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

require_once libfile('function/forumlist');
loadcache('forums');

define('PMODURL', 'action=plugins&operation&config&identifier=htt_baidu&pmod=baidu&ac=');

$action = $_GET['ac'];


switch ($action) {
	case 'add':
		if(!submitcheck('submit')) {
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=baidu&ac=add', 'enctype');
		showtableheader();
		showsetting(lang('plugin/htt_baidu', 'floor'), 'floor', '', 'text');
		showsetting(lang('plugin/htt_baidu', 'ceil'), 'ceil', '', 'text');
		showsetting(lang('plugin/htt_baidu', 'level_title'), 'level_title', '', 'text');
		showsubmit('submit');
		showtablefooter();
		showformfooter();

	}else{
		//检查参数是否为空.上限和下限必须要设置一个。默认为-1 标识无穷大.
		if((!$_GET['floor'] && !$_GET['ceil']) || !$_GET['level_title']) {
			cpmsg(lang('plugin/htt_baidu', 'show_addlevel_error'), '', 'error');
		}
		//插入数据库。
		$insert_array = array(
			'floor'=>$_GET['floor'],
			'ceil'=>empty($_GET['ceil'])?-1:$_GET['ceil'],
			'leveltitle'=>$_GET['level_title'],
			'dateline'=>time(),
			);
		DB::insert("httbaidu_level",$insert_array);
		cpmsg(lang('plugin/htt_baidu', 'show_addlevel_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=baidu', 'succeed');
	}

	case 'del':

		if(submitcheck('submit')) {
		foreach($_GET['delete'] as $delete) {
			// echo $delete;
			
			DB::query("delete FROM pre_httbaidu_level where `id`= $delete");
		}
		updatecache(array('plugin', 'setting'));
		cpmsg(lang('plugin/htt_baidu', 'show_dellevel_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=baidu', 'succeed');
		}


	break;
	default:
		$level_list = array();
    	$query = DB::query("SELECT * FROM  `pre_httbaidu_level` WHERE  1 order by `floor` asc  ");
		while($item = DB::fetch($query)) {
			// var_dump($item);
			$level_list[] = $item;
		}
		
		// arsort($level_list);
		showtips(lang('plugin/htt_baidu', 'baidu_tips'));
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=baidu&ac=del', 'enctype');
		showtableheader();
		echo '<tr class="header"><th></th><th>'.lang('plugin/htt_baidu', 'floor').'</th><th>'.
			lang('plugin/htt_baidu', 'ceil').'</th><th>'.
			lang('plugin/htt_baidu', 'level_title').'</th>
			<th></th></tr>';
		foreach($level_list as $tid => $level) {
			echo '<tr class="hover">
			<th class="td25"><input class="checkbox" type="checkbox" name="delete['.$level['id'].']" value="'.$level['id'].'"></th><th>'.$level['floor'].'</th>
			<th>'.$level['ceil'].'</th>
			<th>'.$level['leveltitle'].'</th></tr>
			';
		}
		$add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=baidu&ac=add\'" value="'.lang('plugin/htt_baidu', 'show_addlevel').'" />';
		if($level_list) {
			showsubmit('submit', lang('plugin/htt_baidu', 'show_dellevel'), $add, '', $multipage);
		} else {
			showsubmit('', '', 'td', $add);
		}
		showtablefooter();
		showformfooter();
		break;
}
?>
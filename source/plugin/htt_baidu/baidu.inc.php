<?php
/**
 *	[百度贴吧] (C)2016-2099 Powered by 北岸的云.
 *	Version: 1.0
 *	Date: 2016-4-18 21:22
 *	http://bbs.wuwenfu.cc/plugin.php?id=htt_baidu:guanzhu
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('PMODURL', 'action=plugins&operation&config&identifier=htt_baidu&pmod=baidu&ac=');
//TODO - Insert your code here

//显示�个列表，显示�个添加按钮�显示一个删除按钮�按照下限进行排�
//type: list  add  addform

$action = $_GET['ac'];

 include_once template('htt_baidu:level_list');

switch ($action) {
	case 'add':
		if(!submitcheck('submit')) {

		// echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
		// $forumselect = "<select name=\"fid\">\n<option value=\"\">&nbsp;&nbsp;> ".cplang('select')."</option><option value=\"\">&nbsp;</option>".str_replace('%', '%%', forumselect(FALSE, 0, 0, TRUE)).'</select>';

		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=baidu&ac=add', 'enctype');
		showtableheader();
		showsetting(lang('plugin/htt_baidu', 'floor'), 'floor', '0', 'text');
		showsetting(lang('plugin/htt_baidu', 'ceil'), 'ceil', '0', 'text');
		showsetting(lang('plugin/htt_baidu', 'level_title'), 'level_title', '�ȼ�1', 'text');
		showsubmit('submit');
		showtablefooter();
		showformfooter();

	}else{
		//�������Ƿ�Ϊ��
		if(!$_GET['floor'] || !$_GET['ceil'] || !$_GET['level_title']) {
			cpmsg(lang('plugin/htt_baidu', 'show_addlevel_error'), '', 'error');
		}
		//�������ݿ⡣
		$insert_array = array(
			'floor'=>$_GET['floor'],
			'ceil'=>$_GET['ceil'],
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
		cpmsg(lang('plugin/htt_baidu', 'floor'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=baidu', 'succeed');
		}


	break;
	default:
		$level_list = array();
    	$query = DB::query("SELECT * FROM  `pre_httbaidu_level` WHERE  1 order by `ceil` ");
		while($item = DB::fetch($query)) {
			// var_dump($item);
			$level_list[] = $item;
		}
		
		arsort($level_list);
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
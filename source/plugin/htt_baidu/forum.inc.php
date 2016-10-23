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

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

require_once libfile('function/forumlist');
loadcache('forums');

define('PMODURL', 'action=plugins&operation&config&identifier=htt_baidu&pmod=forum&ac=');

$action = $_GET['ac'];
$query = C::t('forum_forum')->fetch_all_forum_for_sub_order();
$forums_datas = array();

$forums_datas_list = array();
foreach($query as $forum) {

    if($forum['type'] != 'group'){
        $forums_datas[] = array($forum['fid'],$forum['name']);
    }
    $forums_datas_list[$forum['fid']] = $forum['name'];
}



switch ($action) {
	case 'add':



		if(!submitcheck('submit')) {
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=forum&ac=add', 'enctype');
		showtableheader();
            $forums = array();
            $forums[0] = 'fid';
            $forums[1] = $forums_datas;
//            $forums[1] = array(
//                ['1','板块1'],['2','板块2']
//            );
		showsetting(lang('plugin/htt_baidu', 'forum_name'), $forums, '', 'select');

		showsetting(lang('plugin/htt_baidu', 'level_title'), 'level_titles', '', 'text','',0,'请输入需要自定义的名称,用分号隔开。例如等级一;等级二;等级三');
		showsubmit('submit');
		showtablefooter();
		showformfooter();

	}else{
		if((!$_GET['fid'] && !$_GET['level_titles'])) {
			cpmsg(lang('plugin/htt_baidu', 'show_forum_addlevel_error'), '', 'error');
		}
            //todo 避免重复设置
		$insert_array = array(
			'fid'=>$_GET['fid'],
			'level_titles'=>$_GET['level_titles'],
			);
		DB::insert("httbaidu_forum",$insert_array);
		cpmsg(lang('plugin/htt_baidu', 'show_forum_addlevel_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=forum', 'succeed');
	}

	case 'del':

		if(submitcheck('submit')) {
		foreach($_GET['delete'] as $delete) {
			// echo $delete;
			
			DB::query("delete FROM ".DB::table("httbaidu_forum")." where `id`= $delete");
		}
		updatecache(array('plugin', 'setting'));
		cpmsg(lang('plugin/htt_baidu', 'show_forum_dellevel_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=forum', 'succeed');
		}


	break;
	default:
		$level_list = array();
    	$query = DB::query("SELECT * FROM  ".DB::table("httbaidu_forum")." WHERE  1   ");
		while($item = DB::fetch($query)) {
			// var_dump($item);
			$level_list[] = $item;
		}
		
		// arsort($level_list);
		showtips(lang('plugin/htt_baidu', 'baidu_tips_forum'));
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=forum&ac=del', 'enctype');
		showtableheader();
		echo '<tr class="header"><th></th><th>'.lang('plugin/htt_baidu', 'forum_name').'</th><th>'.
			lang('plugin/htt_baidu', 'level_title').'</th>
			<th></th></tr>';
		foreach($level_list as $tid => $level) {
			echo '<tr class="hover">
			<th class="td25"><input class="checkbox" type="checkbox" name="delete['.$level['id'].']" value="'.$level['id'].'"></th><th>'.$forums_datas_list[$level['fid']].'</th>
			<th>'.$level['level_titles'].'</th></tr>
			';
		}
		$add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_baidu&pmod=forum&ac=add\'" value="'.lang('plugin/htt_baidu', 'show_forum_addlevel').'" />';
		if($level_list) {
			showsubmit('submit', lang('plugin/htt_baidu', 'show_forum_dellevel'), $add, '', $multipage);
		} else {
			showsubmit('', '', 'td', $add);
		}
		showtablefooter();
		showformfooter();
		break;
}
?>
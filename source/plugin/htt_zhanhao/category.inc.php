<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$Plang = $scriptlang['htt_zhanhao'];

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';
require_once libfile('function/forumlist');
loadcache('forums');

define('PMODURL', 'action=plugins&operation&config&identifier=htt_zhanhao&pmod=category&ac=');

$pluginurl	= ADMINSCRIPT.'?action=plugins&identifier=htt_zhanhao&do='.$pluginid;


$action = $_GET['ac'];


switch ($action) {
    case 'add':
        if(!submitcheck('submit')) {
            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category&ac=add', 'enctype');
            showtableheader();
            showsetting(lang('plugin/htt_zhanhao', 'category_title'), 'title', '', 'text');
            showsetting(lang('plugin/htt_zhanhao', 'category_info'), 'info', '', 'textarea');
            showsetting(lang('plugin/htt_zhanhao', 'sort'), 'sort', '', 'text');
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{
            if((!$_GET['title'] && !$_GET['info']) || !$_GET['sort']) {
                cpmsg(lang('plugin/htt_zhanhao', 'show_action_error'), '', 'error');
            }
            $insert_array = array(
                'title'=>$_GET['title'],
                'info'=>$_GET['info'],
                'sort'=>$_GET['sort'],
                'dateline'=>time(),
            );
            DB::insert("httzhanhao_category",$insert_array);
            cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category', 'succeed');
        }
        break;

    case 'editor':
        if(!submitcheck('submit')) {

            $cid = intval($_GET['category_id']);
            $query = DB::query("SELECT * FROM  ".DB::table("httzhanhao_category")." WHERE  `id` = $cid");
            $category_item = DB::fetch($query);

            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category&ac=editor&id='.$category_item['id'], 'enctype');
            showtableheader();
            showsetting(lang('plugin/htt_zhanhao', 'category_title'), 'title',$category_item['title'], 'text');
            showsetting(lang('plugin/htt_zhanhao', 'category_info'), 'info', $category_item['info'], 'textarea');
            showsetting(lang('plugin/htt_zhanhao', 'sort'), 'sort', $category_item['sort'], 'text');
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{
            if(!$_GET['title'] || !$_GET['sort']) {
                cpmsg(lang('plugin/htt_zhanhao', 'show_action_error'), '', 'error');
            }
            $insert_array = array(
                'title'=>$_GET['title'],
                'info'=>$_GET['info'],
                'sort'=>$_GET['sort'],
//                'dateline'=>time(),
            );
            DB::update("httzhanhao_category",$insert_array,array('id'=>$_GET['id']));
            cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category', 'succeed');
        }
        break;

    case 'del':
        C::t('#htt_zhanhao#category')->delete_by_id($_GET['category_id']);
        ajaxshowheader();
        echo $Plang['show_action_succeed'];
        ajaxshowfooter();
/*
        if(submitcheck('submit')) {
            foreach($_GET['delete'] as $delete) {
                DB::query("delete FROM ".DB::table("httzhanhao_category")." where `id`= $delete");
            }
            updatecache(array('plugin', 'setting'));
            cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category', 'succeed');
        }*/
//        exit();




    default:
        $level_list = array();
        $query = DB::query("SELECT * FROM  ".DB::table("httzhanhao_category")." WHERE  1 order by `sort` asc  ");
        while($item = DB::fetch($query)) {
            $level_list[] = $item;
        }
        showtips(lang('plugin/htt_zhanhao', 'htt_zhanhao_tips'));
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category&ac=del', 'enctype');
        showtableheader();
        echo '<tr class="header"><th>'.lang('plugin/htt_zhanhao', 'category_title').'</th><th>'.
            lang('plugin/htt_zhanhao', 'category_info').'</th><th>'.
            lang('plugin/htt_zhanhao', 'dateline').'</th><th>'.lang('plugin/htt_zhanhao', 'sort').'
            </th>
			<th>'.lang('plugin/htt_zhanhao', 'show_action').'
			</th></tr>';
        $i = 0;
        foreach($level_list as $tid => $level) {
            $i++;
            echo '<tr class="hover">
			<th>'.$level['title'].'</th>
			<th>'.$level['info'].'</th>
			<th>'. date("Y-m-d H:i:s",$level['dateline']) .'</th>
			<th>'.$level['sort'].'</th>
			<th>'. '<a href="admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category&ac=editor&category_id=' . trim($level["id"]) . '">' . lang('plugin/htt_zhanhao', 'show_editor') . '</a>'.'
			'.'<a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category&ac=del&category_id=' . trim($level["id"]) . '">[' .$lang['delete'] . ']</a>'.'

			</th>

			</tr>
			';
        }
        $add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=category&ac=add\'" value="'.lang('plugin/htt_zhanhao', 'show_add').'" />';
       /* if($level_list) {
            showsubmit('submit', lang('plugin/htt_zhanhao', 'show_del'), $add, '', $multipage);
        } else {

            showsubmit('', '', 'td', $add);
        }*/
        showsubmit('', '', 'td', $add);
        showtablefooter();
        showformfooter();
        break;
}


?>
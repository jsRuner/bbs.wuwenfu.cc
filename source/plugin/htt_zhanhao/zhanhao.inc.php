<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: admincp.inc.php 29364 2012-04-09 02:51:41Z monkey $
 */

if(!defined('IN_DISCUZ') ) {
    exit('Access Denied');
}


global $_G;


if($_GET['op'] == 'fetch'){

    if ($_GET['formhash'] != FORMHASH) {
        showmessage('undefined_action');
    }
    if($_G['uid'] <=0  || $_G['uid'] != $_GET['uid']){
        showmessage(lang('plugin/htt_zhanhao','need_login'));
    }

    //直接直接方向，获取账号成功。todo:需要检查是否次数还剩余。
    $zhanhao = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_id(intval($_GET['zid']));
    if($zhanhao['status'] == 1){
        include_once template('htt_zhanhao:tip');
    }else{
        //这里记录。
        $insert_array = array(
            'username'=>$_G['username'],
            'zid'=>intval($_GET['zid']),
            'dateline'=>time(),
            'ip'=>$_G['clientip'],
        );
        C::t('#htt_zhanhao#record')->insert($insert_array);
        //需要去改变账号的状态

        C::t('#htt_zhanhao#zhanhao')->update_status_by_id(intval($_GET['zid']),1);

        showmessage(lang('plugin/htt_zhanhao','show_fetech_error_03').'<br>'.$zhanhao['username'].'/'.$zhanhao['password'].'');
    }

    exit();


}

if( !defined('IN_ADMINCP')){
    exit('Access Denied');
}

$Plang = $scriptlang['htt_zhanhao'];
//分类的下拉。
$query = DB::query("SELECT `id`,`title` FROM  ".DB::table("httzhanhao_category")." WHERE  1");
while($category_item = DB::fetch($query)){
    $category_list[] = $category_item;
}

//需要处理一下。
$categorys = array('cid', $category_list);



if($_GET['op'] == 'add') {
    if(!submitcheck('submit')) {
        echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao&op=add');
        showtableheader();
        showsetting(lang('plugin/htt_zhanhao', 'deplay_time'), 'deplay_time', '', 'calendar','',0,'',1);
        showsetting(lang('plugin/htt_zhanhao', 'username'), 'username', '', 'text');
        showsetting(lang('plugin/htt_zhanhao', 'password'), 'password', '', 'text');
        showsetting(lang('plugin/htt_zhanhao', 'category_title'),$categorys, '', 'select');
        showsetting(lang('plugin/htt_zhanhao', 'many_username'),'many_usernames', '', 'textarea','',0,lang('plugin/htt_zhanhao','many_username_comment'));
        showsubmit('submit');
        showtablefooter();
        showformfooter();

    }else{

        //如果存在批量的账号。则其他的忽略。1-2 空格分割
        if($_GET['many_usernames']){




            $many_zhanhaos = explode(';',trim($_GET['many_usernames'],';'));

            foreach($many_zhanhaos as $zhanhao_str){
                $zhanhao = explode('-',trim($zhanhao_str,' '));
                $insert_array = array(
                    'username'=>$zhanhao[0],
                    'password'=>$zhanhao[1],
                    'deplay_time'=>strtotime($_GET['deplay_time']),
                    'cid'=>intval($_GET['cid']),
                    'dateline'=>time(),
                );
                DB::insert("httzhanhao_zhanhao",$insert_array);
            }

            cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao', 'succeed');
            exit();
        }

        if((!$_GET['username'] && !$_GET['password'])) {
            cpmsg(lang('plugin/htt_zhanhao', 'show_action_error'), '', 'error');
        }
        $insert_array = array(
            'username'=>addslashes($_GET['username']),
            'password'=>addslashes($_GET['password']),
            'deplay_time'=>strtotime($_GET['deplay_time']),
            'cid'=>intval($_GET['cid']),
            'dateline'=>time(),
        );
        DB::insert("httzhanhao_zhanhao",$insert_array);
        cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao', 'succeed');

    }

    exit();

}elseif($_GET['op'] == 'del_many'){


    if(!submitcheck('submit')) {
//        选择分类。删除该分类的帐号。
        echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao&op=del_many', 'enctype');
        showtableheader();
        showsetting(lang('plugin/htt_zhanhao', 'category_title'),$categorys, '', 'select');
        showsubmit('submit');
        showtablefooter();
        showformfooter();

    }else{

        $insert_array = array(
            'cid'=>intval($_GET['cid']),
            'status'=>1,
        );
        DB::delete("httzhanhao_zhanhao",$insert_array);
        cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao', 'succeed');

    }
    exit();
}



elseif($_GET['op'] == 'delete') {
    if ($_GET['formhash'] != FORMHASH) {
        showmessage('undefined_action');
    }
    C::t('#htt_zhanhao#zhanhao')->delete_by_id(intval($_GET['id']));
    ajaxshowheader();
    echo $Plang['show_action_succeed'];
    ajaxshowfooter();
}

$ppp = 100;
$resultempty = FALSE;
$srchadd = $searchtext = $extra = $srchid = '';
$page = max(1, intval($_GET['page']));

//存在则追加参数。用户  分类  状态。
if(!empty($_GET['username'])){
//    $srchadd .= "AND username='".$_GET['username']."'";
//    $extra .= '&username='.$_GET['username'];
    $srchadd .= "AND username='".addslashes($_GET['username'])."'";
    $extra .= '&username='.addslashes($_GET['username']);


}

if(!empty($_GET['cid'])){
//    $srchadd .= "AND cid=".$_GET['cid'];
//    $extra .= '&cid='.$_GET['cid'];
   $srchadd .= "AND cid=".intval($_GET['cid']);
    $extra .= '&cid='.intval($_GET['cid']);
}

$statary = array(-1 => $Plang['status'], 0 => $Plang['fetching'], 1 => $Plang['fetched']);

$status = isset($_GET['status']) ? intval($_GET['status']) : -1;


$cate_select = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao'.$extra.'&cid=\' + this.value">';
$new_cate_list = array();

$cate_select .= '<option value="0"'.( !isset($_GET['cid']) ? ' selected' : '').'>'.lang('plugin/htt_zhanhao','category_all').'</option>';

foreach($category_list as $k => $v) {
    $k = $v['id'];
    $cate_select .= '<option value="'.$k.'"'.($k == $_GET['cid'] ? ' selected' : '').'>'.$v['title'].'</option>';
    $new_cate_list[$v['id']] = $v['title']; //为了后续取得分类的标题。

}
$cate_select .= '</select>';






if( $status >= 0) {
    $srchadd .= " AND status='$status'";
    $searchtext .= $Plang['search'].$statary[$status].$Plang['statuss'];
}




if($searchtext) {
    $searchtext = '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao">'.$Plang['search'].'</a>&nbsp'.$searchtext;
}

loadcache('usergroups');

showtableheader();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao', 'repeatsubmit');
showsubmit('repeatsubmit', $Plang['search'], $lang['username'].': <input name="username" value="'.htmlspecialchars($_GET['username']).'" class="txt" />&nbsp;&nbsp;'.$Plang['category_title'].':'.$cate_select, $searchtext);
showformfooter();

$statselect = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao'.$extra.'&status=\' + this.value">';
foreach($statary as $k => $v) {
    $statselect .= '<option value="'.$k.'"'.($k == $status ? ' selected' : '').'>'.$v.'</option>';
}
$statselect .= '</select>';




echo '<tr class="header"><th>'.$Plang['username'].'</th><th>'.$lang['password'].'</th><th>'.$Plang['dateline'].'</th><th>'.$Plang['category_title'].'</th><th>'.$statselect.'</th><th></th></tr>';

if(!$resultempty) {

    $count = C::t('#htt_zhanhao#zhanhao')->count_by_search($srchadd);
    $zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);
    $i = 0;
    foreach($zhanhaos as $zhanhao) {

        $zhanhao['status_text']= $zhanhao['status']==0?$Plang['fetching']:$Plang['fetched'];
        $myrepeat['lastswitch'] = $zhanhao['lastswitch'] ? dgmdate($zhanhao['lastswitch']) : '';
        $zhanhao['usernameenc'] = rawurlencode($zhanhao['username']);
        $opstr = !$zhanhao['locked'] ? $Plang['normal'] : $Plang['lock'];
        $i++;
        echo '<tr><td>'.$zhanhao['username'].'</td>'.
            '<td>'.$zhanhao['password'].'</td>'.
            '<td>'.date('Y-m-d H:i:s',$zhanhao['dateline']).'</td>'.
            '<td>'.$new_cate_list[$zhanhao['cid']].'</td>'.
            '<td>'.$zhanhao['status_text'].'</td>'.
            '<td><a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&formhash='.FORMHASH.'&pmod=zhanhao&id='.$zhanhao['id'].'&op=delete">['.$lang['delete'].']</a></td></tr>';
    }
}
$add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao&op=add\'" value="'.lang('plugin/htt_zhanhao', 'show_add').'" />';
$del_many = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao&op=del_many\'" value="'.lang('plugin/htt_zhanhao', 'del_many').'" />';

if($zhanhaos) {
    showsubmit('', '', $del_many, $add);

}else{
    showsubmit('', '', $add);

}


showtablefooter();

echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=htt_zhanhao&pmod=zhanhao$extra");

?>
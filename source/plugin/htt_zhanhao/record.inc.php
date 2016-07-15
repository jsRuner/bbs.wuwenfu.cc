<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: admincp.inc.php 29364 2012-04-09 02:51:41Z monkey $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$Plang = $scriptlang['htt_zhanhao'];



if($_GET['op'] == 'add') {

}
elseif($_GET['op'] == 'del_many'){


    if(!submitcheck('submit')) {
//        选择日期 。删除这个日期时间段内的
        echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record&op=del_many', 'enctype');
        showtableheader();
        showsetting(lang('plugin/htt_zhanhao', 'start_time'),'start_time', '', 'calendar','',0,'',1);
        showsetting(lang('plugin/htt_zhanhao', 'end_time'),'end_time', '', 'calendar','',0,'',1);

        showsubmit('submit');
        showtablefooter();
        showformfooter();

    }else{


        DB::delete("httzhanhao_record",'dateline >'.strtotime($_GET['start_time']).' AND dateline <='.strtotime($_GET['end_time']));
        cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record', 'succeed');

    }
    exit();
}

elseif($_GET['op'] == 'delete') {
    //判断来源
    if ($_GET['formhash'] != FORMHASH) {
        showmessage('undefined_action');
    }
    C::t('#htt_zhanhao#record')->delete_by_id(intval($_GET['id']));
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
    $srchadd .= "AND username='".daddslashes($_GET['username'])."'";
    $extra .= '&username='.daddslashes($_GET['username']);





}



if($searchtext) {
    $searchtext = '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record">'.$Plang['search'].'</a>&nbsp'.$searchtext;
}

loadcache('usergroups');

showtableheader();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record', 'repeatsubmit');
showsubmit('repeatsubmit', $Plang['search'], $lang['username'].': <input name="username" value="'.dhtmlspecialchars($_GET['username']).'" class="txt" />&nbsp;&nbsp;', $searchtext);
showformfooter();



echo '<tr class="header"><th>'.$Plang['username'].'</th><th>'.$lang['ip'].'</th><th>'.$Plang['dateline'].'</th><th>'.$Plang['zhanhao'].'</th><th>'.$Plang['password'].'</th><th></th></tr>';

if(!$resultempty) {

    $count = C::t('#htt_zhanhao#record')->count_by_search($srchadd);
    $records = C::t('#htt_zhanhao#record')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);

    $zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all();

    $i = 0;
    foreach($records as $record) {



        $i++;
        echo '<tr>
<td>'.$record['username'].'</td>'.
            '<td>'.$record['ip'].'</td>'.
            '<td>'.date('Y-m-d H:i:s',$record['dateline']).'</td>'.
            '<td>'.$zhanhaos[$record['zid']]['username'].'</td>'.
            '<td>'.$zhanhaos[$record['zid']]['password'].'</td>'.
            '<td><a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&formhash='.FORMHASH.'&pmod=record&id='.$record['id'].'&op=delete">['.$lang['delete'].']</a></td>
            </tr>';
    }
}


$del_many = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record&op=del_many\'" value="'.lang('plugin/htt_zhanhao', 'del_many').'" />';

if($records){
    showsubmit('', '', $del_many);

}

showtablefooter();

echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=htt_zhanhao&pmod=record$extra");

?>
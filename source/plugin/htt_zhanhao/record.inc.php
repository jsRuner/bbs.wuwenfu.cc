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

/*
//分类的下拉。
$query = DB::query("SELECT `id`,`title` FROM  ".DB::table("httzhanhao_category")." WHERE  1");
while($category_item = DB::fetch($query)){
    $category_list[] = $category_item;
}

//需要处理一下。
$categorys = array('cid', $category_list);
*/


if($_GET['op'] == 'add') {
   /* if(!submitcheck('submit')) {
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record&op=add', 'enctype');
        showtableheader();
        showsetting(lang('plugin/htt_zhanhao', 'username'), 'username', '', 'text');
        showsetting(lang('plugin/htt_zhanhao', 'password'), 'password', '', 'text');
        showsetting(lang('plugin/htt_zhanhao', 'category_title'),$categorys, '', 'select');
        showsubmit('submit');
        showtablefooter();
        showformfooter();

    }else{
        if((!$_GET['username'] && !$_GET['password'])) {
            cpmsg(lang('plugin/htt_zhanhao', 'show_action_error'), '', 'error');
        }
        $insert_array = array(
            'username'=>$_GET['username'],
            'password'=>$_GET['password'],
            'cid'=>$_GET['cid'],
            'dateline'=>time(),
        );
        DB::insert("httzhanhao_zhanhao",$insert_array);
        cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record', 'succeed');
    }

    exit();*/

} elseif($_GET['op'] == 'delete') {
    C::t('#htt_zhanhao#record')->delete_by_id($_GET['id']);
    ajaxshowheader();
    echo $Plang['show_action_succeed'];
    ajaxshowfooter();
}

$ppp = 100;
$resultempty = FALSE;
$srchadd = $searchtext = $extra = $srchid = '';
$page = max(1, intval($_GET['page']));

//
//if(!empty($_GET['srchusername'])) {
//    $srchadd = "AND username='$srchusername'";
//} elseif(!empty($_GET['srchcategory'])) {
//    $extra = '&srchrepeat='.rawurlencode($_GET['srchrepeat']);
//    $srchadd = "AND username='".addslashes($_GET['username'])."'";
//    $searchtext = $Plang['search'].' "'.$_GET['srchrepeat'].'" '.$Plang['repeats'].'&nbsp;';
//}
//
//if($srchid) {
//    $extra = '&srchid='.$srchid;
//    $member ='xxx';
//    $searchtext = $Plang['search'].' "'.$member['username'].'" '.$Plang['repeatusers'].'&nbsp;';
//}

//存在则追加参数。用户  分类  状态。
if(!empty($_GET['username'])){
    $srchadd .= "AND username='".$_GET['username']."'";
    $extra .= '&username='.$_GET['username'];
}
/*
if(!empty($_GET['cid'])){
    $srchadd .= "AND cid=".$_GET['cid'];
    $extra .= '&cid='.$_GET['cid'];
}*/
/*
if(!empty($_GET['status'])){
    $srchadd .= "AND status=".$_GET['status'];
}*/

/*
$statary = array(-1 => $Plang['status'], 0 => $Plang['fetching'], 1 => $Plang['fetched']);
$status = isset($_GET['status']) ? intval($_GET['status']) : -1;*/

/*
$cate_select = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record'.$extra.'&cid=\' + this.value">';
$new_cate_list = array();

$cate_select .= '<option value="0"'.( !isset($_GET['cid']) ? ' selected' : '').'>all</option>';

foreach($category_list as $k => $v) {
    $k = $v['id'];
    $cate_select .= '<option value="'.$k.'"'.($k == $_GET['cid'] ? ' selected' : '').'>'.$v['title'].'</option>';
    $new_cate_list[$v['id']] = $v['title']; //为了后续取得分类的标题。

}
$cate_select .= '</select>';*/





/*
if(isset($status) && $status > 0) {
    $srchadd .= " AND status='$status'";
    $searchtext .= $Plang['search'].$statary[$status].$Plang['statuss'];
}
*/



if($searchtext) {
    $searchtext = '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record">'.$Plang['search'].'</a>&nbsp'.$searchtext;
}

loadcache('usergroups');

showtableheader();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record', 'repeatsubmit');
showsubmit('repeatsubmit', $Plang['search'], $lang['username'].': <input name="username" value="'.htmlspecialchars($_GET['username']).'" class="txt" />&nbsp;&nbsp;', $searchtext);
showformfooter();
/*
$statselect = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record'.$extra.'&status=\' + this.value">';
foreach($statary as $k => $v) {
    $statselect .= '<option value="'.$k.'"'.($k == $status ? ' selected' : '').'>'.$v.'</option>';
}
$statselect .= '</select>';*/




echo '<tr class="header"><th></th><th>'.$Plang['username'].'</th><th>'.$lang['ip'].'</th><th>'.$Plang['dateline'].'</th><th>'.$Plang['zhanhao'].'</th><th>'.$Plang['password'].'</th><th></th></tr>';

if(!$resultempty) {


//    echo $srchadd;
//    exit();

    $count = C::t('#htt_zhanhao#record')->count_by_search($srchadd);
    $records = C::t('#htt_zhanhao#record')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);

    $zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all();


//    输入名字可以搜索。输入分类可以搜索。选择状态可以搜索。
    //输入名字时。则根据 =  分类根据id去查询。 状态根据status
//    print_r($zhanhaos);
//    exit();


    /*$uids = array();
    foreach($myrepeats as $myrepeat) {
        $uids[] = $myrepeat['uid'];
    }
    $users = C::t('common_member')->fetch_all($uids);*/
    $i = 0;
    foreach($records as $record) {



        $i++;
        echo '<tr>
<td>'.$record['username'].'</td>'.
            '<td>'.$record['ip'].'</td>'.
            '<td>'.date('Y-m-d H:i:s',$record['dateline']).'</td>'.
            '<td>'.$zhanhaos[$record['zid']]['username'].'</td>'.
            '<td>'.$zhanhaos[$record['zid']]['password'].'</td>'.
            '<td><a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record&id='.$record['id'].'&op=delete">['.$lang['delete'].']</a></td>
            </tr>';
    }
}
/*$add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=record&op=add\'" value="'.lang('plugin/htt_zhanhao', 'show_add').'" />';
showsubmit('', '', 'td', $add);*/
showtablefooter();

echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=htt_zhanhao&pmod=record$extra");

?>
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
/*
    include template('common/header_ajax');
    echo"aaaaaa";
    include template('common/footer_ajax');*/
/*
 * �ȼ���
 * Ȼ����uid
 * �ټ���˺��Ƿ����
 *
 * �����õĴ����Ƿ�.
 *
 * ÿ�������ȡ3�Ρ����ڲ��ã�������������
 *
 *
 * �ȸ��ݼ�¼ ���� ��ȡ�˼��Ρ�
 *
 * Ȼ���ѯʣ�������
 *
 *
 *
 *
 * ��Ҫһ�����洢��uid ���� dateline
 *
 *
 * ��
 *
 *
 * �����򷵻� ��ϲ�㣬�˺ź������Ƕ���
 * �������򷵻أ�������˼���˺Ÿոձ�����ȡ����
 *
 *
 *
 *
 *
 * */

    if ($_GET['formhash'] != FORMHASH) {
        showmessage('undefined_action');
    }
    if($_G['uid'] <=0  || $_G['uid'] != $_GET['uid']){
        showmessage(lang('plugin/htt_zhanhao','need_login'));
    }

    //ֱ��ֱ�ӷ��򣬻�ȡ�˺ųɹ���todo:��Ҫ����Ƿ������ʣ�ࡣ
    $zhanhao = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_id($_GET['zid']);

//    print_r($zhanhao);
//    exit();
    //1��ʾʹ���ˡ�
    if($zhanhao['status'] == 1){
//        showmessage(lang('plugin/htt_zhanhao','show_fetech_error_01'));


        include_once template('htt_zhanhao:tip');

    }else{
        //�����¼��
        $insert_array = array(
            'username'=>$_G['username'],
            'zid'=>$_GET['id'],
            'dateline'=>time(),
            'ip'=>$_G['clientip'],
        );
        C::t('#htt_zhanhao#record')->insert($insert_array);
        //��Ҫȥ�ı��˺ŵ�״̬

        C::t('#htt_zhanhao#zhanhao')->update_status_by_id($_GET['zid'],1);

        showmessage(lang('plugin/htt_zhanhao','show_fetech_error_03').'<br>'.$zhanhao['username'].'/'.$zhanhao['password'].'');
    }










    exit();


}

if( !defined('IN_ADMINCP')){
    exit('Access Denied');
}




$Plang = $scriptlang['htt_zhanhao'];


//�����������
$query = DB::query("SELECT `id`,`title` FROM  ".DB::table("httzhanhao_category")." WHERE  1");
while($category_item = DB::fetch($query)){
    $category_list[] = $category_item;
}

//��Ҫ����һ�¡�
$categorys = array('cid', $category_list);



if($_GET['op'] == 'add') {
    if(!submitcheck('submit')) {
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao&op=add', 'enctype');
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
        cpmsg(lang('plugin/htt_zhanhao', 'show_action_succeed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao', 'succeed');
    }

    exit();

} elseif($_GET['op'] == 'delete') {
    C::t('#htt_zhanhao#zhanhao')->delete_by_id($_GET['id']);
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

//������׷�Ӳ������û�  ����  ״̬��
if(!empty($_GET['username'])){
    $srchadd .= "AND username='".$_GET['username']."'";
    $extra .= '&username='.$_GET['username'];
}

if(!empty($_GET['cid'])){
    $srchadd .= "AND cid=".$_GET['cid'];
    $extra .= '&cid='.$_GET['cid'];
}
/*
if(!empty($_GET['status'])){
    $srchadd .= "AND status=".$_GET['status'];
}*/


$statary = array(-1 => $Plang['status'], 0 => $Plang['fetching'], 1 => $Plang['fetched']);
$status = isset($_GET['status']) ? intval($_GET['status']) : -1;


$cate_select = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao'.$extra.'&cid=\' + this.value">';
$new_cate_list = array();

$cate_select .= '<option value="0"'.( !isset($_GET['cid']) ? ' selected' : '').'>all</option>';

foreach($category_list as $k => $v) {
    $k = $v['id'];
    $cate_select .= '<option value="'.$k.'"'.($k == $_GET['cid'] ? ' selected' : '').'>'.$v['title'].'</option>';
    $new_cate_list[$v['id']] = $v['title']; //Ϊ�˺���ȡ�÷���ı��⡣

}
$cate_select .= '</select>';






if(isset($status) && $status > 0) {
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


//    echo $srchadd;
//    exit();

    $count = C::t('#htt_zhanhao#zhanhao')->count_by_search($srchadd);
    $zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);
//    �������ֿ�������������������������ѡ��״̬����������
    //��������ʱ������� =  �������idȥ��ѯ�� ״̬����status
//    print_r($zhanhaos);
//    exit();


    /*$uids = array();
    foreach($myrepeats as $myrepeat) {
        $uids[] = $myrepeat['uid'];
    }
    $users = C::t('common_member')->fetch_all($uids);*/
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
            '<td><a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao&id='.$zhanhao['id'].'&op=delete">['.$lang['delete'].']</a></td></tr>';
    }
}
$add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_zhanhao&pmod=zhanhao&op=add\'" value="'.lang('plugin/htt_zhanhao', 'show_add').'" />';
showsubmit('', '', 'td', $add);
showtablefooter();

echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=htt_zhanhao&pmod=zhanhao$extra");

?>
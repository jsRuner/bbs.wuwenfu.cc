<?php

/**
 * �����admin����
 *
 *
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

global $_G;

loadcache('plugin');

$plugin_lang = array(
    'project'=>'��Ŀ',
    'username'=>'�û���',
//    'password'=>'����',
    'name'=>'��ʵ����',
    'mobile'=>'�ֻ�����',
    'form_check_error'=>'����д������Ϣ',
);

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

switch ($ac){
    case 'add'://���
        if(!submitcheck('submit')) {

            $query = DB::query("SELECT `id`,`name` FROM  ".DB::table("greatwall_project")." WHERE  1");
            while($project = DB::fetch($query)){
                $projects[] = $project;
            }
            $projects = array('project_id',$projects);

            echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee&ac=add', 'enctype');
            showtableheader();
            showsetting($plugin_lang['project'],$projects, '', 'select');
            showsetting($plugin_lang['username'], 'username', '', 'text');
//            showsetting($plugin_lang['password'],'password', '', 'text');
            showsetting($plugin_lang['name'],'name', '', 'text');
            showsetting($plugin_lang['mobile'],'mobile', '', 'text');
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{

            if(!$_GET['name'] && !$_GET['username']) {
                cpmsg($plugin_lang['form_check_error'], '', 'error');
            }

            //������Ҫ���username �Ƿ����.
            $rs = C::t('common_member')->fetch_by_username($_GET['username']);
            if(!$rs){
                cpmsg('�����ڸ��ʺ�', '', 'error');
            }


            $config = array(
                'start_date'=>$_GET['start_date'],
                'end_date'=>$_GET['end_date'],
            );

            $insert_array = array(
                'username'=>$_GET['username'],
//                'password'=>$_GET['password'],
                'name'=>$_GET['name'],
                'mobile'=>$_GET['mobile'],
                'updated'=>date('Y-m-d H:i:s'),
                'created'=>date('Y-m-d H:i:s'),
                'project_id'=>$_GET['project_id'],
            );
            C::t('#htt_greatwall#employee')->insert($insert_array);
            cpmsg('�����ɹ�', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee', 'succeed');

        }

        break;
    case 'del': //ɾ��

        if(submitcheck('submit')) {
            foreach($_GET['delete'] as $delete) {
                C::t('#htt_greatwall#employee')->delete($delete);
            }
            cpmsg('�����ɹ�', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee', 'succeed');
        }

        break;
    case 'edit': //�༭�����úͽ��ò�����

        $eid = intval($_GET['eid']);

        $employee = C::t('#htt_greatwall#employee')->fetch_by_eid($eid);






        if(!submitcheck('submit')) {

            $query = DB::query("SELECT `id`,`name` FROM  ".DB::table("greatwall_project")." WHERE  1");
            while($project = DB::fetch($query)){
                $projects[] = $project;
            }
            $projects = array('project_id',$projects);

            echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee&ac=edit&eid='.$eid, 'enctype');
            showtableheader();
            showsetting($plugin_lang['project'],$projects, $employee['project_id'], 'select');
            showsetting($plugin_lang['username'], 'username',$employee['username'], 'text');
//            showsetting($plugin_lang['password'],'password',$employee['password'], 'text');
            showsetting($plugin_lang['name'],'name',$employee['name'], 'text');
            showsetting($plugin_lang['mobile'],'mobile',$employee['mobile'], 'text');
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{

            if(!$_GET['username']) {
                cpmsg($plugin_lang['form_check_error'], '', 'error');
            }

            //������Ҫ���username �Ƿ����.
            $rs = C::t('common_member')->fetch_by_username($_GET['username']);
            if(!$rs){
                cpmsg('�����ڸ��ʺ�', '', 'error');
            }

            $eid = intval($_GET['eid']);

            $insert_array = array(
                'username'=>$_GET['username'],
//                'password'=>$_GET['password'],
                'name'=>$_GET['name'],
                'mobile'=>$_GET['mobile'],
                'updated'=>date('Y-m-d H:i:s'),
                'project_id'=>$_GET['project_id'],
            );
            C::t('#htt_greatwall#employee')->update($eid,$insert_array);
            cpmsg('�����ɹ�', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee', 'succeed');

        }






        break;
    default: //��ʾ�б�.����ҳ�ġ�

        $extra = $search = '';

        $ppp = 100;
        $page = max(1, intval($_GET['page']));
        $count = C::t('#htt_greatwall#employee')->count_by_search($search);
        $employees = C::t('#htt_greatwall#employee')->fetch_all_by_search($search,($page - 1) * $ppp, $ppp);



        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee&ac=del', 'enctype');
        showtableheader();
        echo '<tr class="header"><th></th><th>'.$plugin_lang['username'].'</th><th>'.
//            $plugin_lang['password'].'</th><th>'.
            $plugin_lang['name'].'</th><th>'.
            $plugin_lang['mobile'].'</th><th></th></tr>';
        foreach($employees as $eid => $employee) {
            echo '<tr class="hover">
<th class="td25"><input class="checkbox" type="checkbox" name="delete['.$employee['id'].']" value="'.$employee['id'].'"></th>
            <th>'.$employee['username'].'</th>
                <th>'.
                $employee['name'].'</th>
                <th>'.
                $employee['mobile'].'</th>
                <th>'.
                '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee&ac=edit&eid='.$employee['id'].'">�༭</a></th>
                </tr>';
        }
        $add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=employee&ac=add\'" value="���" />';
        if($employees) {
            showsubmit('submit','ɾ��', $add, '', $multipage);
        } else {
            showsubmit('', '', 'td', $add);
        }
        showtablefooter();
        showformfooter();


        echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=htt_greatwall&pmod=employee$extra");

        break;
}


?>
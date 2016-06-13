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
    'name'=>'�����',
    'start_date'=>'���ʼʱ��',
    'project_type'=>'�����',
    'end_date'=>'�����ʱ��',
);

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

switch ($ac){
    case 'add'://���
        if(!submitcheck('submit')) {
            echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=add', 'enctype');
            showtableheader();
            showsetting('��Ŀ����', 'name', '', 'text');
            showsetting('��ʼʱ��','start_date', '', 'calendar', '', 0, '', 1);
            showsetting('����ʱ��','end_date', '', 'calendar', '', 0, '', 1);
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{

            if(!$_GET['name'] && !$_GET['start_date']&&!$_GET['end_date']) {
                cpmsg('����д������Ϣ', '', 'error');
            }

            $config = array(
                'start_date'=>$_GET['start_date'],
                'end_date'=>$_GET['end_date'],
            );

            $insert_array = array(
                'name'=>$_GET['name'],
                'project_type'=>'nolg03',
                'config'=>json_encode($config),
                'updated'=>date('Y-m-d H:i:s'),
                'created'=>date('Y-m-d H:i:s'),
            );
            C::t('#htt_greatwall#project')->insert($insert_array);
            cpmsg('�����ɹ�', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project', 'succeed');

        }

        break;
    case 'del': //ɾ��

        if(submitcheck('submit')) {
            foreach($_GET['delete'] as $delete) {
                C::t('#htt_greatwall#project')->delete($delete);
            }
            cpmsg('�����ɹ�', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project', 'succeed');
        }

        break;
    case 'edit': //�༭�����úͽ��ò�����
        if(!submitcheck('submit')) {

            $project = C::t('#htt_greatwall#project')->fetch_by_pid($_GET['pid']);

            $config = json_decode($project['config'],true);
            $project['start_date'] = $config['start_date'];
            $project['end_date'] = $config['end_date'];



            echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=edit&pid='.$_GET['pid'], 'enctype');
            showtableheader();
            showsetting('��Ŀ����', 'name', $project['name'], 'text');
            showsetting('��ʼʱ��','start_date', $project['end_date'], 'calendar', '', 0, '', 1);
            showsetting('����ʱ��','end_date', $project['end_date'], 'calendar', '', 0, '', 1);
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{

            if(!$_GET['name'] && !$_GET['start_date']&&!$_GET['end_date']) {
                cpmsg('����д������Ϣ', '', 'error');
            }

            $config = array(
                'start_date'=>$_GET['start_date'],
                'end_date'=>$_GET['end_date'],
            );

            $pid = intval($_GET['pid']);

            $insert_array = array(
                'name'=>$_GET['name'],
                'config'=>json_encode($config),
                'updated'=>date('Y-m-d H:i:s'),
            );

            C::t('#htt_greatwall#project')->update($pid,$insert_array);
            cpmsg('�����ɹ�', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project', 'succeed');

        }






        break;
    default: //��ʾ�б�.����ҳ�ġ�

        $extra = $search = '';

        $ppp = 100;
        $page = max(1, intval($_GET['page']));
        $count = C::t('#htt_greatwall#project')->count_by_search($search);
        $projects = C::t('#htt_greatwall#project')->fetch_all_by_search($search,($page - 1) * $ppp, $ppp);
        $project_types = array(
            'nolg03'=>'�޼�����Ʒ3ѡ1'
        );


        showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=del', 'enctype');
        showtableheader();
        echo '<tr class="header"><th></th><th>'.$plugin_lang['name'].'</th><th>'.
            $plugin_lang['project_type'].'</th><th>'.
            $plugin_lang['start_date'].'</th><th>'.
            $plugin_lang['end_date'].'</th><th></th></tr>';
        foreach($projects as $pid => $project) {
            echo '<tr class="hover">
<th class="td25"><input class="checkbox" type="checkbox" name="delete['.$project['id'].']" value="'.$project['id'].'"></th>
            <th><a href="forum.php?mod=viewthread&tid='.$project['id'].'" target="_blank">'.$project['name'].'</a></th>
            <th>'.
                $project_types[$project['project_type']].'</th>
                <th>'.
                $project['created'].'</th>
                <th>'.
                $project['updated'].'</th>
                <th>'.
                '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=edit&pid='.$project['id'].'">�༭</a></th>
                </tr>';
        }
        $add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=add\'" value="���" />';
        if($projects) {
            showsubmit('submit','ɾ��', $add, '', $multipage);
        } else {
            showsubmit('', '', 'td', $add);
        }
        showtablefooter();
        showformfooter();


        echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=htt_greatwall&pmod=project$extra");

        break;
}


?>
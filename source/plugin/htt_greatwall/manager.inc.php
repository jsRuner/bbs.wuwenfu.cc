<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/13
 * Time: 16:42
 * description:
 *
 *
 */



if(!defined('IN_DISCUZ')) {
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

        var_dump($projects);



        break;
}




?>
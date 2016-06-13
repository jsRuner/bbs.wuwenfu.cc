<?php

/**
 * 活动管理。admin负责。
 *
 *
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;

loadcache('plugin');

$plugin_lang = array(
    'name'=>'活动标题',
    'start_date'=>'活动开始时间',
    'project_type'=>'活动类型',
    'end_date'=>'活动结束时间',
);

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

switch ($ac){
    case 'add'://添加
        if(!submitcheck('submit')) {
            echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=add', 'enctype');
            showtableheader();
            showsetting('项目名称', 'name', '', 'text');
            showsetting('开始时间','start_date', '', 'calendar', '', 0, '', 1);
            showsetting('结束时间','end_date', '', 'calendar', '', 0, '', 1);
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{

            if(!$_GET['name'] && !$_GET['start_date']&&!$_GET['end_date']) {
                cpmsg('请填写完整信息', '', 'error');
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
            cpmsg('操作成功', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project', 'succeed');

        }

        break;
    case 'del': //删除

        if(submitcheck('submit')) {
            foreach($_GET['delete'] as $delete) {
                C::t('#htt_greatwall#project')->delete($delete);
            }
            cpmsg('操作成功', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project', 'succeed');
        }

        break;
    case 'edit': //编辑。启用和禁用操作。
        if(!submitcheck('submit')) {

            $project = C::t('#htt_greatwall#project')->fetch_by_pid($_GET['pid']);

            $config = json_decode($project['config'],true);
            $project['start_date'] = $config['start_date'];
            $project['end_date'] = $config['end_date'];



            echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
            showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=edit&pid='.$_GET['pid'], 'enctype');
            showtableheader();
            showsetting('项目名称', 'name', $project['name'], 'text');
            showsetting('开始时间','start_date', $project['end_date'], 'calendar', '', 0, '', 1);
            showsetting('结束时间','end_date', $project['end_date'], 'calendar', '', 0, '', 1);
            showsubmit('submit');
            showtablefooter();
            showformfooter();

        }else{

            if(!$_GET['name'] && !$_GET['start_date']&&!$_GET['end_date']) {
                cpmsg('请填写完整信息', '', 'error');
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
            cpmsg('操作成功', 'action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project', 'succeed');

        }






        break;
    default: //显示列表.带分页的。

        $extra = $search = '';

        $ppp = 100;
        $page = max(1, intval($_GET['page']));
        $count = C::t('#htt_greatwall#project')->count_by_search($search);
        $projects = C::t('#htt_greatwall#project')->fetch_all_by_search($search,($page - 1) * $ppp, $ppp);
        $project_types = array(
            'nolg03'=>'无记名奖品3选1'
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
                '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=edit&pid='.$project['id'].'">编辑</a></th>
                </tr>';
        }
        $add = '<input type="button" class="btn" onclick="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=htt_greatwall&pmod=project&ac=add\'" value="添加" />';
        if($projects) {
            showsubmit('submit','删除', $add, '', $multipage);
        } else {
            showsubmit('', '', 'td', $add);
        }
        showtablefooter();
        showformfooter();


        echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=htt_greatwall&pmod=project$extra");

        break;
}


?>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/13
 * Time: 16:42
 * description: 论坛管理员的项目管理。提供 编辑操作。
 *
 *
 */

//error_reporting(E_ALL);

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

global $_G;

loadcache('plugin');

//include_once 'source/function/function_admincp.php';
include_once 'source/function/function_core.php';

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
        if(!$_GET['submit']) {

            $project = C::t('#htt_greatwall#project')->fetch_by_pid($_GET['pid']);

            $config = json_decode($project['config'],true);
            $project['start_date'] = $config['start_date'];
            $project['end_date'] = $config['end_date'];

            include_once template('htt_greatwall:manager_project_edit');



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
                'status' => $_GET['status'],
            );


            C::t('#htt_greatwall#project')->update($pid,$insert_array);

//            cpmg('操作成功');
//            echo 11;

            showmessage('操作成功', '/plugin.php?id=htt_greatwall:manager', 'succeed');

        }








        break;
    default: //显示主页面

        $extra = $search = '';


        $ppp = 100;
        $page = max(1, intval($_GET['page']));
        $count = C::t('#htt_greatwall#project')->count_by_search($search);
        $projects = C::t('#htt_greatwall#project')->fetch_all_by_search($search,($page - 1) * $ppp, $ppp);
        $project_types = array(
            'nolg03'=>'无记名奖品3选1'
        );

        $project_statuss = array(
            '-1'=>'删除',
            '0'=>'待审',
            '1'=>'启用',
        );


        foreach($projects as $key =>$project){
            $config = json_decode($project['config'],true);
            $project['start_date'] = $config['start_date'];
            $project['end_date'] = $config['end_date'];
            $projects[$key] = $project;
        }

        include_once template('htt_greatwall:manager_index');



        break;
}




?>
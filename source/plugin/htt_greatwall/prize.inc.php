<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/13
 * Time: 16:42
 * description: ��̳����Ա����Ŀ�����ṩ �༭������
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
    'name'=>'�����',
    'start_date'=>'���ʼʱ��',
    'project_type'=>'�����',
    'end_date'=>'�����ʱ��',
);


$prize_statuss = array(
    '-1'=>'ɾ��',
    '0'=>'����',
    '1'=>'����',
);

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';

//��ѯ����Ŀ�б�
$query = DB::query("SELECT `id`,`name` FROM  ".DB::table("greatwall_project")." WHERE  1");
while($project = DB::fetch($query)){
    $projects[] = $project;
}


switch ($ac){
    case 'add'://���
        if(!$_POST['submit']) {


            include_once template('htt_greatwall:prize_add');

        }else{

//            error_reporting(E_ALL);

            if(!$_POST['name'] && !$_POST['prizes_nums']&&!$_POST['probability']) {
                showmessage('����д������Ϣ', '', 'error');
            }



            $insert_array = array(
                'name'=>$_POST['name'],
                'project_id'=>$_POST['project_id'],
                'prizes_nums'=>$_POST['prizes_nums'],
                'probability'=>$_POST['probability'],
                'created'=>date('Y-m-d H:i:s'),
                'updated'=>date('Y-m-d H:i:s'),
                'status' => $_POST['status'],
                'price'=>$_GET['price'],
            );


            C::t('#htt_greatwall#prize')->insert($insert_array);
            showmessage('�����ɹ�', '/plugin.php?id=htt_greatwall:prize', 'succeed');

        }

        break;

    case 'enabled': //����
        if($_GET['pid']) {

            $update_array = array(
                'status'=>1,
            );
            C::t('#htt_greatwall#prize')->update($_GET['pid'],$update_array);
            showmessage('�����ɹ�', '/plugin.php?id=htt_greatwall:prize', 'succeed');
        }


        break;
    case 'disable': //����
        if($_GET['pid']) {

            $update_array = array(
                'status'=>0,
            );
            C::t('#htt_greatwall#prize')->update($_GET['pid'],$update_array);
            showmessage('�����ɹ�', '/plugin.php?id=htt_greatwall:prize', 'succeed');
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
        if(!$_GET['submit']) {

            $prize = C::t('#htt_greatwall#prize')->fetch_by_pid($_GET['pid']);



            include_once template('htt_greatwall:prize_edit');



        }else{


            if(!$_GET['name'] && !$_GET['prizes_nums']&&!$_GET['probability']) {
                cpmsg('����д������Ϣ', '', 'error');
            }



            $pid = intval($_GET['pid']);

            $insert_array = array(
                'name'=>$_GET['name'],
                'project_id'=>$_GET['project_id'],
                'price'=>$_GET['price'],
                'prizes_nums'=>$_GET['prizes_nums'],
                'probability'=>$_GET['probability'],
                'updated'=>date('Y-m-d H:i:s'),
                'status' => $_GET['status'],
            );


            C::t('#htt_greatwall#prize')->update($pid,$insert_array);

//            cpmg('�����ɹ�');
//            echo 11;

            showmessage('�����ɹ�', '/plugin.php?id=htt_greatwall:prize', 'succeed');

        }








        break;
    default: //��ʾ��ҳ��

        $extra = $search = ' AND status != -1';


        $ppp = 20;
        $page = max(1, intval($_GET['page']));
        $count = C::t('#htt_greatwall#prize')->count_by_search($search);
        $prizes = C::t('#htt_greatwall#prize')->fetch_all_by_search($search,($page - 1) * $ppp, $ppp);





        include_once template('htt_greatwall:prize_index');



        break;
}




?>
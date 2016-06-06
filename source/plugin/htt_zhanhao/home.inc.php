<?php


if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

global $_G;

loadcache('plugin');

$var = $_G['cache']['plugin'];
$share_rate = $var['htt_zhanhao']['share_rate']; //����ı����� 2:1 ��ʾ2�η���ֻ��1����ȡ��
$share_rates = explode(':', $share_rate); //��һ��Ԫ���Ƿ������ �ڶ���Ԫ������ȡ������


$fetch_num = $var['htt_zhanhao']['fetch_num']; //��ȡ������


$right_adv1 = $var['htt_zhanhao']['right_adv1']; //��ȡ������
$right_adv2 = $var['htt_zhanhao']['right_adv2']; //��ȡ������

$seo_key = $var['htt_zhanhao']['seo_key']; //seo

//echo $right_adv1;
//
//exit();


//echo $fetch_num;
//exit();

$max_num = $var['htt_zhanhao']['max_num']; //�����ȡ��������ͨ��������������ȡ��Ρ�
$groupstr = $var['htt_zhanhao']['groups'];
$groups = array_filter(unserialize($groupstr));
$members_bygroup = C::t('common_member')->fetch_all_by_groupid($groups);//����Ļ�Ա����
$uids = array();
foreach ($members_bygroup as $item) {
    $uids[] = $item['uid'];
}


$copyright = $var['htt_zhanhao']['copyright'];

//���յ�ʱ���
$st = strtotime(date('Y-m-d') . ' 00:00:00');
$et = strtotime(date('Y-m-d') . ' 23:59:59');


/*2��ҳ��
һ�����б��б�ֱ����ʾ�˺š��˺ź����봦�ڼ���״̬��

�����ȡ��ť������ֱ����ȡ��

����޷���ȡ������ʾ������վ��

ֻ��ʾ100����


*/
/*
echo '<pre>';
var_dump($_G['setting']['bbname']);
var_dump($_G);
echo '</pre>';
exit();*/
//��ȡ������Ϣ��


$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;

$categorys = C::t('#htt_zhanhao#category')->fetch_all();

if (empty($categorys)) {
    die(lang('plugin/htt_zhanhao','no_set_category'));
}

if ($cid == 0) {
    $cid = $categorys[0]['id'];
}


$newcategorys = array();
foreach ($categorys as $k => $v) {
    $newcategorys[$v['id']] = $v;
}

//���cid���ڷ��෶Χ����������һ��
if(!in_array($cid,array_keys($newcategorys))){
    $ids = array_keys($newcategorys);
    $cid = $ids[0];
}


$date = date('Y-m-d');


//��ѯ�˺�������
$srchadd = '';
//$srchadd = ' AND status = 0 ';
$srchadd .= ' AND cid = '.$cid;
$page = 1;
$ppp = 100;

$zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);


$myzhanhaos = False;
if(intval($_G['uid']) >0){
    //��ѯ�Լ��ļ�¼��
    $search .= "AND username = '" .addslashes( $_G['username'] ). "'";

    $myzhanhao_num = C::t('#htt_zhanhao#record')->count_by_search($search);

    //�����¼���ࡣ��ɾ��ǰ��ļ�¼��
    if(count($myzhanhao_num) > 10){
        //ɾ�������0��֮ǰ����ȡ��¼��
        $qiantian = strtotime(date("Y-m-d")." 00:00:00");
        C::t('#htt_zhanhao#record')->delete_by_username_time(addslashes( $_G['username'] ),$qiantian);
    }

    $myzhanhaos =  C::t('#htt_zhanhao#record')->fetch_all_by_search($search, ($page - 1) * $ppp, $ppp);


}




//����Ƿ����ҳ�档ȡ���Ӽ�¼
if($_GET['op'] == 'share'){

    //���ж�ip�Ƿ��Ѿ���ʹ�ù������ʹ�ù�������Ч��û��ʹ�ù��������
    //���㵱ǰ������ȡ�Ĵ�����
    $search = '';
    $search .= "AND username = '" .addslashes( $_G['username']) . "'"; //����ʱ�䡣�û��������㡣
    $search .= "AND ip = '" . $_G['clientip'] . "'"; //����ʱ�䡣�û��������㡣
    $search .= "AND dateline BETWEEN $st AND $et"; //����ʱ�䡣�û��������㡣


    $share_num = C::t('#htt_zhanhao#share')->count_by_search($search);

    if($share_num > 0){
        //������ʲô������
    }else{
        //����������롣
        $insert_array = array(
            'username' => addslashes($_G['username']),
            'dateline' => time(),
            'ip' => $_G['clientip'],
        );
        C::t('#htt_zhanhao#share')->insert($insert_array);

    }

}




if ($_GET['op'] == 'fetch') {

    if ($_GET['formhash'] != FORMHASH) {
        $msg = lang('plugin/htt_zhanhao', 'undefined_action');
        include_once template('htt_zhanhao:tip');
        exit();
    }
    if ($_G['uid'] <= 0 || $_G['uid'] != $_GET['uid']) {
        $msg = lang('plugin/htt_zhanhao', 'yes_zhanhao_login');
        include_once template('htt_zhanhao:tip');
        exit();
    }

    if (!in_array($_G['uid'], $uids)) {
        $msg = lang('plugin/htt_zhanhao', 'not_in_groups');
        include_once template('htt_zhanhao:tip');
        exit();
    }


    /*
     * �����жϡ�ÿ��3�Ρ���ȡ�������õ��ļ���
     * ���ж��Ƿ���Ȩ�ޡ��Ƿ����ڷ��顣
     * �ټ��������ȡ�Ĵ����������ֵ�Ƚϣ��������ֵ�Ƚϡ�����
     *
     * ��Ҫ�������Ĵ�������Ҫ�洢��id username ip dateline �����¼��
     *
     * ÿ����ȡ�������������ת����ȡ����+Ĭ�ϵ����ʹ���
     *
     *
     * */
    $search = "AND username = '" . addslashes($_G['username']) . "'"; //����ʱ�䡣�û��������㡣

    $search = '';
    $search .= "AND username = '" . addslashes($_G['username']) . "'"; //����ʱ�䡣�û��������㡣
    $search .= "AND dateline BETWEEN $st AND $et"; //����ʱ�䡣�û��������㡣

    $fetched_num = C::t('#htt_zhanhao#record')->count_by_search($search);

    if ($fetched_num > $max_num) {
        $msg = lang('plugin/htt_zhanhao', 'over_max_num');
        include_once template('htt_zhanhao:tip');
        exit();
    }

    //���㵱ǰ������ȡ�Ĵ�����
    $search = '';
    $search .= "AND username = '" . addslashes($_G['username']) . "'"; //����ʱ�䡣�û��������㡣
    $search .= "AND dateline BETWEEN $st AND $et"; //����ʱ�䡣�û��������㡣
    $share_num = C::t('#htt_zhanhao#share')->count_by_search($search);
    //���������ȡ����
    $fetching_num = floor($share_num / $share_rates[0] * $share_rates[1]);


    if ($fetched_num >= $fetching_num + $fetch_num) {
        $msg = lang('plugin/htt_zhanhao', 'over_fetch_num');
        include_once template('htt_zhanhao:tip');
        exit();
    }


    //ֱ��ֱ�ӷ��򣬻�ȡ�˺ųɹ���todo:��Ҫ����Ƿ������ʣ�ࡣ
    $zhanhao = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_id(intval($_GET['zid']));

//    print_r($zhanhao);
//    exit();
    //1��ʾʹ���ˡ�2016��6��5�� ȥ���ʺű���ȡ�����ѡ�
//    if ($zhanhao['status'] == 1) {
    if (false) {
//        showmessage(lang('plugin/htt_zhanhao','show_fetech_error_01'));

//        $msg = lang('plugin/htt_zhanhao', 'show_fetech_error_01');


    } else {
        //�����¼��ͬһ���ʺš�ͬһ������ȡ�������ظ���¼
        $sea = " AND username ='".addslashes($_G['username'])."'";
        $sea .= " AND zid =".intval($_GET['zid']);
        $fnum = C::t('#htt_zhanhao#record')->count_by_search($sea);

        //ֻ�в����ڵ�����ټ�¼
        if($fnum <=0){

            $insert_array = array(
                'username' => addslashes($_G['username']),
                'zid' => intval($_GET['zid']),
                'dateline' => time(),
                'ip' => $_G['clientip'],
            );
            C::t('#htt_zhanhao#record')->insert($insert_array);
            //��Ҫȥ�ı��˺ŵ�״̬

            C::t('#htt_zhanhao#zhanhao')->update_status_by_id(intval($_GET['zid']), 1);
        }



        $msg = lang('plugin/htt_zhanhao', 'show_fetech_error_03') . '<br>' .lang('plugin/htt_zhanhao', 'zhanhao').':<b style="color:red;">'. $zhanhao['username'] . '</b><br>'.lang('plugin/htt_zhanhao', 'password').':<b style="color:red;">' . $zhanhao['password'] . '</b>';

//        showmessage(lang('plugin/htt_zhanhao', 'show_fetech_error_03') . '<br>' . $zhanhao['username'] . '/' . $zhanhao['password'] . '');
    }

    include_once template('htt_zhanhao:tip');

    exit();


}






include_once template('htt_zhanhao:home');

exit();


?>
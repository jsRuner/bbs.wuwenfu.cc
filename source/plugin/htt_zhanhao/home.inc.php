<?php


if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

global $_G;

loadcache('plugin');

$var = $_G['cache']['plugin'];
$share_rate = $var['htt_zhanhao']['share_rate']; //分享的比例。 2:1 表示2次分享，只能1次提取。
$share_rates = explode(':', $share_rate); //第一个元素是分享次数 第二个元素是提取次数。


$fetch_num = $var['htt_zhanhao']['fetch_num']; //提取次数。


$right_adv1 = $var['htt_zhanhao']['right_adv1']; //提取次数。
$right_adv2 = $var['htt_zhanhao']['right_adv2']; //提取次数。

$seo_key = $var['htt_zhanhao']['seo_key']; //seo

//echo $right_adv1;
//
//exit();


//echo $fetch_num;
//exit();

$max_num = $var['htt_zhanhao']['max_num']; //最多提取次数。即通过分享。最多可以提取多次。
$groupstr = $var['htt_zhanhao']['groups'];
$groups = array_filter(unserialize($groupstr));
$members_bygroup = C::t('common_member')->fetch_all_by_groupid($groups);//该组的会员资料
$uids = array();
foreach ($members_bygroup as $item) {
    $uids[] = $item['uid'];
}


$copyright = $var['htt_zhanhao']['copyright'];

//今日的时间戳
$st = strtotime(date('Y-m-d') . ' 00:00:00');
$et = strtotime(date('Y-m-d') . ' 23:59:59');


/*2个页面
一个是列表。列表直接显示账号。账号和密码处于加密状态。

点击提取按钮，可以直接提取。

如果无法提取，则提示分享网站。

只显示100个。


*/
/*
echo '<pre>';
var_dump($_G['setting']['bbname']);
var_dump($_G);
echo '</pre>';
exit();*/
//读取分类信息。


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

//如果cid不在分类范围里。则随机其中一个
if(!in_array($cid,array_keys($newcategorys))){
    $ids = array_keys($newcategorys);
    $cid = $ids[0];
}


$date = date('Y-m-d');


//查询账号与密码
$srchadd = '';
//$srchadd = ' AND status = 0 ';
$srchadd .= ' AND cid = '.$cid;
$page = 1;
$ppp = 100;

$zhanhaos = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_search($srchadd, ($page - 1) * $ppp, $ppp);


$myzhanhaos = False;
if(intval($_G['uid']) >0){
    //查询自己的记录。
    $search .= "AND username = '" .addslashes( $_G['username'] ). "'";

    $myzhanhao_num = C::t('#htt_zhanhao#record')->count_by_search($search);

    //如果记录过多。则删除前天的记录。
    if(count($myzhanhao_num) > 10){
        //删除今天的0点之前的提取记录。
        $qiantian = strtotime(date("Y-m-d")." 00:00:00");
        C::t('#htt_zhanhao#record')->delete_by_username_time(addslashes( $_G['username'] ),$qiantian);
    }

    $myzhanhaos =  C::t('#htt_zhanhao#record')->fetch_all_by_search($search, ($page - 1) * $ppp, $ppp);


}




//如果是分享的页面。取增加记录
if($_GET['op'] == 'share'){

    //先判断ip是否已经被使用过。如果使用过，则无效。没有使用过，则插入
    //计算当前可以领取的次数。
    $search = '';
    $search .= "AND username = '" .addslashes( $_G['username']) . "'"; //根据时间。用户名。计算。
    $search .= "AND ip = '" . $_G['clientip'] . "'"; //根据时间。用户名。计算。
    $search .= "AND dateline BETWEEN $st AND $et"; //根据时间。用户名。计算。


    $share_num = C::t('#htt_zhanhao#share')->count_by_search($search);

    if($share_num > 0){
        //存在则什么都不做
    }else{
        //不存在则插入。
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
     * 次数判断。每日3次。读取的是配置的文件。
     * 先判断是否有权限。是否属于分组。
     * 再计算今天领取的次数。与最低值比较，再与最高值比较。即可
     *
     * 需要计算分享的次数。需要存储。id username ip dateline 分享记录表。
     *
     * 每日提取次数：分享次数转换提取次数+默认的赠送次数
     *
     *
     * */
    $search = "AND username = '" . addslashes($_G['username']) . "'"; //根据时间。用户名。计算。

    $search = '';
    $search .= "AND username = '" . addslashes($_G['username']) . "'"; //根据时间。用户名。计算。
    $search .= "AND dateline BETWEEN $st AND $et"; //根据时间。用户名。计算。

    $fetched_num = C::t('#htt_zhanhao#record')->count_by_search($search);

    if ($fetched_num > $max_num) {
        $msg = lang('plugin/htt_zhanhao', 'over_max_num');
        include_once template('htt_zhanhao:tip');
        exit();
    }

    //计算当前可以领取的次数。
    $search = '';
    $search .= "AND username = '" . addslashes($_G['username']) . "'"; //根据时间。用户名。计算。
    $search .= "AND dateline BETWEEN $st AND $et"; //根据时间。用户名。计算。
    $share_num = C::t('#htt_zhanhao#share')->count_by_search($search);
    //换算次数。取整数
    $fetching_num = floor($share_num / $share_rates[0] * $share_rates[1]);


    if ($fetched_num >= $fetching_num + $fetch_num) {
        $msg = lang('plugin/htt_zhanhao', 'over_fetch_num');
        include_once template('htt_zhanhao:tip');
        exit();
    }


    //直接直接方向，获取账号成功。todo:需要检查是否次数还剩余。
    $zhanhao = C::t('#htt_zhanhao#zhanhao')->fetch_all_by_id(intval($_GET['zid']));

//    print_r($zhanhao);
//    exit();
    //1表示使用了。2016年6月5日 去掉帐号被提取的提醒。
//    if ($zhanhao['status'] == 1) {
    if (false) {
//        showmessage(lang('plugin/htt_zhanhao','show_fetech_error_01'));

//        $msg = lang('plugin/htt_zhanhao', 'show_fetech_error_01');


    } else {
        //这里记录。同一个帐号。同一个人提取，则不用重复记录
        $sea = " AND username ='".addslashes($_G['username'])."'";
        $sea .= " AND zid =".intval($_GET['zid']);
        $fnum = C::t('#htt_zhanhao#record')->count_by_search($sea);

        //只有不存在的情况再记录
        if($fnum <=0){

            $insert_array = array(
                'username' => addslashes($_G['username']),
                'zid' => intval($_GET['zid']),
                'dateline' => time(),
                'ip' => $_G['clientip'],
            );
            C::t('#htt_zhanhao#record')->insert($insert_array);
            //需要去改变账号的状态

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
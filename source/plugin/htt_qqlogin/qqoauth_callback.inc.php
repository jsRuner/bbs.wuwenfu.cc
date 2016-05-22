<?php

///*global $_G;
//print_r( C::T('common_usergroup'));
//$_G['group'] = C::T('common_usergroup')->fetch_all('10');
//print_r($_G);
//exit();

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require libfile('function/member');
require libfile('class/member');



global $_G;
if($_G['setting']['bbclosed']) {
    if(($_GET['action'] != 'activation' && !$_GET['activationauth']) || !$_G['setting']['closedallowactivation'] ) {
        showmessage('register_disable', NULL, array(), array('login' => 1));
    }
}

loadcache(array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional', 'fields_register', 'ipctrl'));
require_once libfile('function/misc');
require_once libfile('function/profile');
if(!function_exists('sendmail')) {
    include libfile('function/mail');
}
loaducenter();

//
//print_r($_G['setting']);
//exit();
//echo a:1:{s:6:"system";i:1;}

//var_dump( unserialize('a:1:{s:6:"system";i:1;}'));

//echo 'please wait';
//exit();
//error_reporting(E_ALL);

//加载config配置。

//require_once("config/config_ucenter.php");
//require_once("uc_client/client.php");
//require_once("../../API/qqConnectAPI.php");
//require_once("source/plugin/htt_qqlogin/API/qqConnectAPI.php");
//$qc = new QC();
////echo $qc->qq_callback();
//echo '---';
////echo $qc->get_openid();
//
//$ret = $qc->get_user_info();
//
//echo "<pre>";
//var_dump($ret);
//echo "</pre>";
//2016年5月21日，这里直接访问。表示已经获取到用户的数据了。
//先获取用户的id，如果已经存在。
//http://bbs.wuwenfu.cc/plugin.php?id=htt_qqlogin:qqoauth_callback
$openid = 'A28E0D85CABCA15C4DCD8255';

//去表查询该id对应的用户。查到则模拟该用户登录。没有查到，则进入注册逻辑，自动注册。然后登录。
// id openid uid 这3个字段是必须的。
//用户必须填写一个邮箱。用户与密码自动产生。
//相关的表 pre_common_member ok
 // pre_common_member_count ok
/*
pre_common_member_field_forum ok
pre_common_member_field_home ok
pre_common_member_newprompt 用户新提醒数量表 ok
pre_common_member_profile  ok
pre_common_member_status ok
pre_ucenter_members  ok
pre_ucenter_memberfields ok
先实现一个效果。QQ登录以后注册。然后QQ登录就是登录操作即可。
先查询，不存在，则执行一系统插入操作。存在则调用登录逻辑。

测试。访问一次。则应当自动注册了某个账号。
然后使用该账号可以登录系统。

测试结果是可以登录，但是提示需要激活。然后表common_member中无数据，登录以后才有数据。
第二次测试。也是如此。需要激活。
激活是因为common_member没有用户数据。

common_member的插入操作是关联的。同时会填充其他几个表。
这里系统消息出现故障。没有通知。

2016年5月22日新增系统通知。

*/
global $_G;

//
//echo $_G['clientip'];
//echo $_G['remoteport'];
//exit();

$query = DB::query("SELECT * FROM  ".DB::table("httqqlogin")." WHERE  `openid`= '$openid'");
$qqinfo = array();
if($item = DB::fetch($query)) {

    echo 22;
    $qqinfo = $item;
    //执行登录，这里是打印。
    print_r($qqinfo);
    exit();

}





//先操作ucenter_members表。
$username = 'wuwenfu018';
$password = '123456';
$email = '12345678912345678912345@qq.com';
$questionid = '';
$answer = '';
//addslashes() 函数返回在预定义字符之前添加反斜杠的字符串。
$uid = uc_user_register(addslashes($username), $password, $email, $questionid, $answer, $_G['clientip']);
//echo 'uid ='.$uid;
//2016年5月21日 需要保存uid  pre_httqqlogin中去
//$uid = 8;
$_G['uid'] = $uid;


$insert_array = array(
    'uid'=>$uid,
    'openid'=>$openid,
    'access_token'=>'',
    'dateline'=>TIMESTAMP,
);
DB::insert("httqqlogin",$insert_array);


//如果uid已经存在。则提示冲突了。
/*if(getuserbyuid($uid, 1)) {
    if(!$activation) {
        uc_user_delete($uid);
    }
    showmessage('profile_uid_duplicate', '', array('uid' => $uid));
}*/
//ucenter_member 与common_member密码不同。
/*$invite = getinvite();
if($invite && $this->setting['inviteconfig']['invitegroupid']) {
    $groupinfo['groupid'] = $this->setting['inviteconfig']['invitegroupid'];
}

$init_arr = array('credits' => explode(',', $this->setting['initcredits']), 'profile'=>$profile, 'emailstatus' => $emailstatus);

error_reporting(E_ALL);
*/
//$init_arr = array('credits'=>0,'newprompt'=>1);
C::t('common_member')->insert($uid, $username, md5(random(10)), $email, $_G['clientip'], 10);
//C::t('common_member')->update($uid,array('newprompt'=>1));

/*echo C::t('common_member')->insert(
    array(
        'uid'=>$uid,
        'username'=>$username,
        'password'=>md5(random(10)), //该密码没有用。
        'email'=>$email,
        'groupid'=>10,
        'regdate'=>TIMESTAMP,
        'credits'=>0,//用户的积分.登录的时候会产生积分，这里应当是0
        'timeoffset'=>'9999',//时区校正
        'newprompt'=>1, //新消息提醒的数量。
    ),true
);*/


C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'port' => $_G['remoteport'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));

//默认不用审核用户。pre_common_member_validate
//pre_common_invite 邀请表

/*C::t('common_member_profile')->insert(
    array(
        'uid'=>$uid,
    )
);*/
//用户新提醒数量表
C::t('common_member_newprompt')->insert(
    array(
        'uid'=>$uid,
        'data'=>serialize(array('system'=>1))
    )
);

/*//论坛扩展字段
C::t('common_member_field_forum')->insert(
    array(
        'uid'=>$uid,
    )
);

//家园扩展
C::t('common_member_field_home')->insert(
    array(
        'uid'=>$uid,
    )
);*/
//用户统计表
//C::t('common_member_count')->insert(
//    array(
//        'uid'=>$uid,
//        'extcredits2'=>2 //金钱。
//    )
//);


//趋势统计
include_once libfile('function/stat');
updatestat('register');

$setting = $_G['setting'];
$welcomemsg = & $setting['welcomemsg'];
$welcomemsgtitle = & $setting['welcomemsgtitle'];
$welcomemsgtxt = & $setting['welcomemsgtxt'];

if($welcomemsg && !empty($welcomemsgtxt)) {
    $welcomemsgtitle = replacesitevar($welcomemsgtitle);
    $welcomemsgtxt = replacesitevar($welcomemsgtxt);
    if($welcomemsg == 1) {
        $welcomemsgtxt = nl2br(str_replace(':', '&#58;', $welcomemsgtxt));
        notification_add($uid, 'system', $welcomemsgtxt, array('from_id' => 0, 'from_idtype' => 'welcomemsg'), 1);
    } elseif($welcomemsg == 2) {
        sendmail_cron($email, $welcomemsgtitle, $welcomemsgtxt);
    } elseif($welcomemsg == 3) {
        sendmail_cron($email, $welcomemsgtitle, $welcomemsgtxt);
        $welcomemsgtxt = nl2br(str_replace(':', '&#58;', $welcomemsgtxt));
        notification_add($uid, 'system', $welcomemsgtxt, array('from_id' => 0, 'from_idtype' => 'welcomemsg'), 1);
    }
}
//登录逻辑。
$_G['member'] = array(
    'username'=>$username,
    'uid'=>$uid,
);
//pre_common_usergroup

//fetch_all_by_groupid

$_G['group'] = C::t('common_usergroup')->fetch_all('10')[10];

$result = userlogin($username, $password, $_GET['questionid'], $_GET['answer'], 'username', $_G['clientip']);

//登录状态。
setloginstatus($result['member'], 2592000);

$referer = dreferer();
$ucsynlogin = $setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
$param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['member']['uid']);
showmessage('login_succeed', $referer ? $referer : './', $param, array('showdialog' => 1, 'locationtime' => true, 'extrajs' => $ucsynlogin));








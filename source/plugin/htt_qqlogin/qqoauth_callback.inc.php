<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function strFilter($str){
    $str = str_replace('`', '', $str);
    $str = str_replace('·', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('！', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('￥', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('……', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('（', '', $str);
    $str = str_replace('）', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('——', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('【', '', $str);
    $str = str_replace('】', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('；', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('：', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('“', '', $str);
    $str = str_replace('”', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('，', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('《', '', $str);
    $str = str_replace('》', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('。', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('、', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('？', '', $str);
    $str = preg_replace("/\s/","",$str);
    $str = cutstr($str,8,'');
    return trim($str);
}


function connect_login($connect_member) {
    global $_G;

    if(!($member = getuserbyuid($connect_member['uid'], 1))) {
        return false;
    } else {
        if(isset($member['_inarchive'])) {
            C::t('common_member_archive')->move_to_master($member['uid']);
        }
    }
    require_once libfile('function/member');
    $cookietime = 1296000;
    setloginstatus($member, $cookietime);
    dsetcookie('connect_login', 1, $cookietime);
    dsetcookie('connect_is_bind', '1', 31536000);
    dsetcookie('connect_uin', $connect_member['conopenid'], 31536000);
    return true;
}

function get_avatar($uid, $size = 'middle', $type = '') {
    $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
    $uid = abs(intval($uid));
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    $typeadd = $type == 'real' ? '_real' : '';
    return 'uc_server/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}

function set_home($uid, $dir = '.') {
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    !is_dir($dir.'/'.$dir1) && mkdir($dir.'/'.$dir1, 0777);
    !is_dir($dir.'/'.$dir1.'/'.$dir2) && mkdir($dir.'/'.$dir1.'/'.$dir2, 0777);
    !is_dir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3) && mkdir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3, 0777);
}

function downqqimg($url,$filename){
    ob_start();
    $img = dfsockopen($url);
    $size = strlen($img);
    $fp2=@fopen($filename, "a");
    fwrite($fp2,$img);
    fclose($fp2);
}

function htt_random_str($length=5){
    $hash = '';
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $max = strlen($chars) - 1;
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

function htt_random_int($length=5){
    $hash = '';
    $chars = '0123456789';
    $max = strlen($chars) - 1;
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

global $_G;
global $lang;
require libfile('function/member');
require libfile('class/member');
if($_G['setting']['bbclosed']) {
    if(($_GET['action'] != 'activation' && !$_GET['activationauth']) || !$_G['setting']['closedallowactivation'] ) {
        showmessage('register_disable', NULL, array(), array('login' => 1));
    }
}
loadcache(array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional', 'fields_register', 'ipctrl'));
require_once libfile('function/misc');
require_once libfile('function/profile');
require_once("source/plugin/htt_qqlogin/API/qqConnectAPI.php");
if(!function_exists('sendmail')) {
    include libfile('function/mail');
}

loaducenter();
loadcache('plugin');
$var = $_G['cache']['plugin'];
$appid =  $var['htt_qqlogin']['appid'];
$appkey =  $var['htt_qqlogin']['key'];
$callback = trim( $_G['siteurl'],'/').'/plugin.php?id=htt_qqlogin:qqoauth_callback';
$suffix_length =  $var['htt_qqlogin']['suffix_length']; //后缀长度。

if($temp = getcookie('con_request_uri')){
    $referer = $temp;
}else{
    $referer = dreferer();
}


$qc = new QC();
$qc->set_config($appid,$appkey,$callback);
$access_token = $qc->qq_callback();
$openid = $qc->get_openid();
//避免sql注入
$openid = daddslashes($openid);
$query = DB::query("SELECT * FROM  ".DB::table("httqqlogin")." WHERE  `openid`= '$openid'");

$qqinfo = array();
if($item = DB::fetch($query)) {
    $qqinfo = $item;
    $members = C::t('common_member')->fetch_all_username_by_uid($qqinfo['uid']);
    $username = $members[$qqinfo['uid']];
    $members = C::t('common_member')->fetch_by_username($username);
    $uid = $qqinfo['uid'];

    //绑定的QQ被使用了。
    if($_G['uid'] > 0 && $uid != $_G['uid']){
        showmessage(lang('plugin/htt_qqlogin', 'have_bind_qq'),$_G['siteurl']);
        exit();
    }


    $password = $qqinfo['password'];

    $connect_member = array();
    $connect_member['uid'] = $qqinfo['uid']; //QQConnect的access token
    $connect_member['conuin'] = $qqinfo['access_token'];//QQConnect的access token
    $connect_member['conuinsecret'] = '';//'QQConnect的access token secret'
    $connect_member['conopenid'] = $openid;//'QQConnect的openid


    $params['mod'] = 'login';
    connect_login($connect_member);
    loadcache('usergroups');
    $usergroups = $_G['cache']['usergroups'][$_G['groupid']]['grouptitle'];
    $param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle']);

    C::t('common_member_status')->update($connect_member['uid'], array('lastip'=>$_G['clientip'], 'lastvisit'=>TIMESTAMP, 'lastactivity' => TIMESTAMP));
    $ucsynlogin = '';
    if($_G['setting']['allowsynlogin']) {
        loaducenter();
        $ucsynlogin = uc_user_synlogin($_G['uid']);
    }

    dsetcookie('stats_qc_login', 3, 86400);
    showmessage('login_succeed', $referer, $param, array('extrajs' => $ucsynlogin));
    exit();
}

//先操作ucenter_members表。
$qc = new QC($access_token,$openid);
$qc->set_config($appid,$appkey,$callback);
$ret = $qc->get_user_info();
$nickname = $ret['nickname'];

//去空格。与特殊字符.至多保存4个字。
//2016年6月12日 这里根据需要改变编码。如果是gbk，就转换。否则不进行。
if($_G['charset'] == 'gbk'){

    $nickname =   iconv("utf-8", "gbk",$nickname);
}

$nickname = strFilter($nickname);





if($suffix_length <=0){
    $username = $nickname;
}else{
    $username = $nickname.'_'.random($suffix_length);
}

if(strlen($username)>15){
    $username = 'qq_'.time();
}


//绑定操作。
if ($_G['uid'] > 0 ) {
   $insert_array = array(
    'uid'=>$_G['uid'],
    'openid'=>$openid,
    'access_token'=>$access_token,
    'nickname'=>$nickname,
    'username'=>$username,
    'password'=>'',
    'photo'=>$ret['figureurl_qq_1'],
    'dateline'=>TIMESTAMP,
);
DB::insert("httqqlogin",$insert_array);
//退出重新登录。
    showmessage( lang('plugin/htt_qqlogin', 'bind_user_success_login'),'/member.php?mod=logging&action=logout&formhash='.FORMHASH);
exit();
}

$password = uniqid();
$email = time().'@qq.com';
$questionid = '';
$answer = '';
$uid = uc_user_register($username, $password, $email, $questionid, $answer, $_G['clientip']);
$_G['uid'] = $uid;
//保存头像到指定目录。
set_home($uid,'uc_server/data/avatar');
$avatar = get_avatar($uid,'small');

if(!file_exists($avatar)){
    downqqimg($ret['figureurl_qq_1'],$avatar);
}

if($uid <= 0) {
    if($uid == -1) {
        showmessage('profile_username_illegal');
    } elseif($uid == -2) {
        showmessage('profile_username_protect');
    } elseif($uid == -3) {
        //如果出现重复，则随机一次。如果还出现，则提示用户名重复。
        $username = $nickname.'_'.htt_random_str(1);

        if(strlen($username)>15){
            $username = 'qq_'.time();
        }

        $uid = uc_user_register($username, $password, $email, $questionid, $answer, $_G['clientip']);
        $_G['uid'] = $uid;
        //保存头像到指定目录。
        set_home($uid,'uc_server/data/avatar');
        $avatar = get_avatar($uid,'small');

        if($uid ==-3){
            showmessage('profile_username_duplicate');
        }
    } elseif($uid == -4) {
        showmessage('profile_email_illegal');
    } elseif($uid == -5) {
        showmessage('profile_email_domain_illegal');
    } elseif($uid == -6) {
        showmessage('profile_email_duplicate');
    } else {
        showmessage('undefined_action');
    }
}

$insert_array = array(
    'uid'=>$uid,
    'openid'=>$openid,
    'access_token'=>$access_token,
    'nickname'=>$nickname,
    'username'=>$username,
    'password'=>$password,
    'photo'=>$ret['figureurl_qq_1'],
    'dateline'=>TIMESTAMP,
);
DB::insert("httqqlogin",$insert_array);
C::t('common_member')->insert($uid, $username, md5(random(10)), $email, $_G['clientip'], 10);
C::t('common_member')->update($uid,array('avatarstatus'=>1));
C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'port' => $_G['remoteport'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));

//用户新提醒数量表
C::t('common_member_newprompt')->insert(
    array(
        'uid'=>$uid,
        'data'=>serialize(array('system'=>1))
    )
);

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
    $connect_member = array();
    $connect_member['uid'] = $uid; //QQConnect的access token
    $connect_member['conuin'] = $access_token;//QQConnect的access token
    $connect_member['conuinsecret'] = '';//'QQConnect的access token secret'
    $connect_member['conopenid'] = $openid;//'QQConnect的openid


    $params['mod'] = 'login';
    connect_login($connect_member);
    loadcache('usergroups');
    $usergroups = $_G['cache']['usergroups'][$_G['groupid']]['grouptitle'];
    $param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle']);

    C::t('common_member_status')->update($connect_member['uid'], array('lastip'=>$_G['clientip'], 'lastvisit'=>TIMESTAMP, 'lastactivity' => TIMESTAMP));
    $ucsynlogin = '';
    if($_G['setting']['allowsynlogin']) {
        loaducenter();
        $ucsynlogin = uc_user_synlogin($_G['uid']);
    }

    dsetcookie('stats_qc_login', 3, 86400);
    showmessage('login_succeed', $referer, $param, array('extrajs' => $ucsynlogin));

    exit();

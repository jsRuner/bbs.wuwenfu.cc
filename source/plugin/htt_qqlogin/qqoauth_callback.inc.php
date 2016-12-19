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
    // $str = cutstr($str,8,'');
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

$site_url = $var['htt_qqlogin']['site_url'];
if(empty($site_url)){
    $site_url = $site_url;
}
$site_url = rtrim($site_url,'/');


// $callback = trim( $site_url,'/').'/plugin.php?id=htt_qqlogin:qqoauth_callback';
$callback = $site_url.'/plugin.php?id=htt_qqlogin:qqoauth_callback';
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
        //判断用户名是否是QQ注册的。如果是。则进入表单。
        showmessage(lang('plugin/htt_qqlogin', 'have_bind_qq'),$site_url,array(),array('alert'=>'error'));
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


$nickname = preg_replace('|[^a-zA-Z0-9\x{4e00}-\x{9fa5}]|u', '', $nickname);

//如果为空。则使用。
if ($nickname == '') {
    # code...
    $nickname = 'qq_'.time();
}

$username=$nickname = mb_substr($nickname,0,15);

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
        showmessage( lang('plugin/htt_qqlogin', 'bind_user_success_login'), $site_url,array(),array('alert'=>'right'));
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
        //需要添加后缀。
        //如果当前长度为15，则需要截取。根据后台的设置。截取后缀长度。
        //如果低于15，则但是 超过 15-后缀长度，还是需要截取。还是需要截取后缀长度。
        //15  3 则需要截取。4个字符。 15+3+1 -15 = 4   应当保留 11个字符串  15 - 3-1 = 11
        //12  3 则需要截取。1个字符。 12+3+1 -15 = 1  应当保留 11个字符串 15 - 3 - 1 = 11
        //10  3 则不需要截取。 10+3+1 -15 = -1     
        
        $need_len = 15 - $suffix_length - 1 ;

        $username = mb_substr($nickname, 0,$need_len).'_'.random($suffix_length);

        echo "$username";

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

//这里发送系统通知。告诉对方可以绑定老账号和默认密码。
$notice_msg = lang('plugin/htt_qqlogin', 'passwd_notice');

$notice_msg = str_replace('passwd', $password, $notice_msg);

$notice_msg = str_replace('bindold', '<a href="'.$site_url.'/home.php?mod=spacecp&ac=plugin&op=profile&id=htt_qqlogin:bind_qq" target="_blank">绑定</a>', $notice_msg);

$notice_data = array(
    'uid'=>$uid,
    'type'=>'system',
    'new'=>1,
    'authorid'=>0,
    'author'=>'',
    // 'note'=>'你的默认密码是123456,请及时修改',
    'note'=>$notice_msg,
    'dateline'=>TIMESTAMP+2,
    'from_id'=>0,
    'from_idtype'=>'welcomemsg',
    'from_num'=>0,
    'category'=>3,
    );
DB::insert('home_notification',$notice_data);



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



//统计
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


//增加分类的提醒。
C::t('common_member')->increase($uid, array('newprompt' => 1));
$touid = $uid;
$categoryname = 'system';
$newprompt = C::t('common_member_newprompt')->fetch($touid);
if($newprompt) {
    $newprompt['data'] = unserialize($newprompt['data']);
    if(!empty($newprompt['data'][$categoryname])) {
        $newprompt['data'][$categoryname] = intval($newprompt['data'][$categoryname]) + 1;
    } else {
        $newprompt['data'][$categoryname] = 1;
    }
    C::t('common_member_newprompt')->update($touid, array('data' => serialize($newprompt['data'])));
} else {
    C::t('common_member_newprompt')->insert($touid, array($categoryname => 1));
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
    showmessage('login_succeed', $site_url, $param, array('extrajs' => $ucsynlogin));

    exit();

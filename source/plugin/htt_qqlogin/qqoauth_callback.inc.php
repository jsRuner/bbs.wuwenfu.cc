<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
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
    readfile($url);
    $img = ob_get_contents();
    ob_end_clean();
    $size = strlen($img);
    $fp2=@fopen($filename, "a");
    fwrite($fp2,$img);
    fclose($fp2);
}

function make_username($username){
    $username = $username.'_'.rand(1,100);
    $userinfo = C::t('common_member')->fetch_by_username($username);
    if(!empty($userinfo)){
        make_username($username);
    }
    if(strlen($username) > 15){
        $username = cutstr($username,13,'');
    }
    return $username;
}

/*error_reporting(E_ALL);

$uid = 49;
$avatar = get_avatar($uid,'small');

echo $avatar;

downqqimg('http://q.qlogo.cn/qqapp/101319474/A28E0D85CABCA15C4DCD820D408D6BB5/40',$avatar);

//file_put_contents($avatar,file_get_contents('http://q.qlogo.cn/qqapp/101319474/A28E0D85CABCA15C4DCD820D408D6BB5/40'));

exit();*/




global $_G;
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
$is_open =  $var['htt_qqlogin']['is_open'];

$appid =  $var['htt_qqlogin']['appid'];
$appkey =  $var['htt_qqlogin']['key'];
$callback =  $_G['siteurl'].'plugin.php?id=htt_qqlogin:qqoauth_callback';

if($is_open==2){
    die('qq is closed');
}

$qc = new QC();
$qc->set_config($appid,$appkey,$callback);
$access_token = $qc->qq_callback();
//
$openid = $qc->get_openid();

$query = DB::query("SELECT * FROM  ".DB::table("httqqlogin")." WHERE  `openid`= '$openid'");
$qqinfo = array();
if($item = DB::fetch($query)) {
    $qqinfo = $item;
    $members = C::t('common_member')->fetch_all_username_by_uid($qqinfo['uid']);
    $username = $members[$qqinfo['uid']];
    $members = C::t('common_member')->fetch_by_username($username);
    $uid = $qqinfo['uid'];
    $password = '123456';
//登录逻辑。
    $_G['member'] = array(
        'username'=>$username,
        'uid'=>$uid,
    );
    $_G['group'] = C::t('common_usergroup')->fetch_all('10')[10];

    $result = userlogin($username, $password, $_GET['questionid'], $_GET['answer'], 'username', $_G['clientip']);

//登录状态。
    setloginstatus($result['member'], 2592000);

    $referer = dreferer();
    $ucsynlogin = $setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
    $param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['member']['uid']);
    showmessage('login_succeed', $referer ? $referer : './', $param, array('showdialog' => 1, 'locationtime' => true, 'extrajs' => $ucsynlogin));
    exit();
}
//先操作ucenter_members表。

$qc = new QC($access_token,$openid);
$qc->set_config($appid,$appkey,$callback);
$ret = $qc->get_user_info();

$nickname = $ret['nickname'];

$nickname =   iconv("utf-8", "gbk",$nickname);

//$username = $nickname.'_'.time();

$username = make_username($nickname);



//echo $username;

$password = '123456';
$email = time().'@qq.com';
$questionid = '';
$answer = '';
$uid = uc_user_register($username, $password, $email, $questionid, $answer, $_G['clientip']);
//$uid = uc_user_register(addslashes($username), $password, $email, $questionid, $answer, $_G['clientip']);
$_G['uid'] = $uid;

//保存头像到指定目录。
set_home($uid,'uc_server/data/avatar');

$avatar = get_avatar($uid,'small');


//file_put_contents($avatar,file_get_contents($ret['figureurl_qq_1']));
downqqimg($ret['figureurl_qq_1'],$avatar);


$insert_array = array(
    'uid'=>$uid,
    'openid'=>$openid,
    'access_token'=>$access_token,
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
$_G['member'] = array(
    'username'=>$username,
    'uid'=>$uid,
);


$_G['group'] = C::t('common_usergroup')->fetch_all('10')[10];

$result = userlogin($username, $password, $_GET['questionid'], $_GET['answer'], 'username', $_G['clientip']);

//登录状态。
setloginstatus($result['member'], 2592000);

$referer = dreferer();
$ucsynlogin = $setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
$param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['member']['uid']);
showmessage('login_succeed', $referer ? $referer : './', $param, array('showdialog' => 1, 'locationtime' => true, 'extrajs' => $ucsynlogin));








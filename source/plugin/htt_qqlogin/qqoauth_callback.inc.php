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

//����config���á�

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
//2016��5��21�գ�����ֱ�ӷ��ʡ���ʾ�Ѿ���ȡ���û��������ˡ�
//�Ȼ�ȡ�û���id������Ѿ����ڡ�
//http://bbs.wuwenfu.cc/plugin.php?id=htt_qqlogin:qqoauth_callback
$openid = 'A28E0D85CABCA15C4DCD8255';

//ȥ���ѯ��id��Ӧ���û����鵽��ģ����û���¼��û�в鵽�������ע���߼����Զ�ע�ᡣȻ���¼��
// id openid uid ��3���ֶ��Ǳ���ġ�
//�û�������дһ�����䡣�û��������Զ�������
//��صı� pre_common_member ok
 // pre_common_member_count ok
/*
pre_common_member_field_forum ok
pre_common_member_field_home ok
pre_common_member_newprompt �û������������� ok
pre_common_member_profile  ok
pre_common_member_status ok
pre_ucenter_members  ok
pre_ucenter_memberfields ok
��ʵ��һ��Ч����QQ��¼�Ժ�ע�ᡣȻ��QQ��¼���ǵ�¼�������ɡ�
�Ȳ�ѯ�������ڣ���ִ��һϵͳ�����������������õ�¼�߼���

���ԡ�����һ�Ρ���Ӧ���Զ�ע����ĳ���˺š�
Ȼ��ʹ�ø��˺ſ��Ե�¼ϵͳ��

���Խ���ǿ��Ե�¼��������ʾ��Ҫ���Ȼ���common_member�������ݣ���¼�Ժ�������ݡ�
�ڶ��β��ԡ�Ҳ����ˡ���Ҫ���
��������Ϊcommon_memberû���û����ݡ�

common_member�Ĳ�������ǹ����ġ�ͬʱ���������������
����ϵͳ��Ϣ���ֹ��ϡ�û��֪ͨ��

2016��5��22������ϵͳ֪ͨ��

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
    //ִ�е�¼�������Ǵ�ӡ��
    print_r($qqinfo);
    exit();

}





//�Ȳ���ucenter_members��
$username = 'wuwenfu018';
$password = '123456';
$email = '12345678912345678912345@qq.com';
$questionid = '';
$answer = '';
//addslashes() ����������Ԥ�����ַ�֮ǰ��ӷ�б�ܵ��ַ�����
$uid = uc_user_register(addslashes($username), $password, $email, $questionid, $answer, $_G['clientip']);
//echo 'uid ='.$uid;
//2016��5��21�� ��Ҫ����uid  pre_httqqlogin��ȥ
//$uid = 8;
$_G['uid'] = $uid;


$insert_array = array(
    'uid'=>$uid,
    'openid'=>$openid,
    'access_token'=>'',
    'dateline'=>TIMESTAMP,
);
DB::insert("httqqlogin",$insert_array);


//���uid�Ѿ����ڡ�����ʾ��ͻ�ˡ�
/*if(getuserbyuid($uid, 1)) {
    if(!$activation) {
        uc_user_delete($uid);
    }
    showmessage('profile_uid_duplicate', '', array('uid' => $uid));
}*/
//ucenter_member ��common_member���벻ͬ��
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
        'password'=>md5(random(10)), //������û���á�
        'email'=>$email,
        'groupid'=>10,
        'regdate'=>TIMESTAMP,
        'credits'=>0,//�û��Ļ���.��¼��ʱ���������֣�����Ӧ����0
        'timeoffset'=>'9999',//ʱ��У��
        'newprompt'=>1, //����Ϣ���ѵ�������
    ),true
);*/


C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'port' => $_G['remoteport'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));

//Ĭ�ϲ�������û���pre_common_member_validate
//pre_common_invite �����

/*C::t('common_member_profile')->insert(
    array(
        'uid'=>$uid,
    )
);*/
//�û�������������
C::t('common_member_newprompt')->insert(
    array(
        'uid'=>$uid,
        'data'=>serialize(array('system'=>1))
    )
);

/*//��̳��չ�ֶ�
C::t('common_member_field_forum')->insert(
    array(
        'uid'=>$uid,
    )
);

//��԰��չ
C::t('common_member_field_home')->insert(
    array(
        'uid'=>$uid,
    )
);*/
//�û�ͳ�Ʊ�
//C::t('common_member_count')->insert(
//    array(
//        'uid'=>$uid,
//        'extcredits2'=>2 //��Ǯ��
//    )
//);


//����ͳ��
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
//��¼�߼���
$_G['member'] = array(
    'username'=>$username,
    'uid'=>$uid,
);
//pre_common_usergroup

//fetch_all_by_groupid

$_G['group'] = C::t('common_usergroup')->fetch_all('10')[10];

$result = userlogin($username, $password, $_GET['questionid'], $_GET['answer'], 'username', $_G['clientip']);

//��¼״̬��
setloginstatus($result['member'], 2592000);

$referer = dreferer();
$ucsynlogin = $setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
$param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['member']['uid']);
showmessage('login_succeed', $referer ? $referer : './', $param, array('showdialog' => 1, 'locationtime' => true, 'extrajs' => $ucsynlogin));








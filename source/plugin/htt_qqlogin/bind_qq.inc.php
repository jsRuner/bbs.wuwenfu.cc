<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;

loadcache('plugin');


/*
$plugin_lang = array(
    'bind_user_no_exist'=>'�󶨵��û�������',
    'bind_user_password_wrong'=>'�������',
    'bind_user_need_register'=>'�󶨹�����Ҫ��QQע��',
);*/

$plugin_lang = $Plang = $scriptlang['htt_qqlogin'];


//�Ȳ�ѯ�Ƿ�ΪQQע���û�
$qq_member = C::t('#htt_qqlogin#qqlogin')->fetch_by_uid($_G['uid']);
if(!$qq_member){
//    showmessage($plugin_lang['bind_user_need_register']);
}




//�ȼ���Ƿ���QQע����û���ֻ��QQע����û���Ȼ��ſ��԰������Ļ�Ա��
//����޸������룬��QQ��¼�޷�ʹ�á���Ҫ��������һ�Ρ�


if($_POST['username']) {
    //����ǰQQ��¼�������е�uid username password�޸�Ϊ�û��Լ���д�ġ�
    //��Ҫ�û��Ƿ���ں������Ƿ���ȡ�
//    $rs = C::t('common_member')->fetch_by_username($_POST['username']);
    $user = DB::fetch_first('SELECT * FROM  `pre_ucenter_members` WHERE  `username` LIKE  \''.addslashes($_POST['username']).'\'');

    if(empty($user)){
        //�󶨵��û�������
        showmessage($plugin_lang['bind_user_no_exist']);
    }

    $password = $_POST['password'];
    if($user['password'] != md5(md5($password).$user['salt'])){
        //˵���������
        showmessage($plugin_lang['bind_user_password_wrong']);
    }
    //���°󶨵����ϡ�
    $update_array = array(
        'uid'=>$user['uid'],
        'username'=>$user['username'],
        'password'=>$_POST['password'],
    );
    //����Ӧ����openid
    C::t('#htt_qqlogin#qqlogin')->update_by_openid($qq_member['openid'],$update_array);
    //�˳����µ�¼��
    showmessage($plugin_lang['bind_user_success_login'],'/member.php?mod=logging&action=logout&formhash='.FORMHASH);


}
/*
function check_login($username, $password, &$user) {
    $user = $this->get_user_by_username($username);
    if(empty($user['username'])) {
        return -1;
    } elseif($user['password'] != md5(md5($password).$user['salt'])) {
        return -2;
    }
    return $user['uid'];
}*/
?>
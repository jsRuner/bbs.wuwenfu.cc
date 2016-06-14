<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;

loadcache('plugin');


/*
$plugin_lang = array(
    'bind_user_no_exist'=>'绑定的用户不存在',
    'bind_user_password_wrong'=>'密码错误',
    'bind_user_need_register'=>'绑定功能需要先QQ注册',
);*/

$plugin_lang = $Plang = $scriptlang['htt_qqlogin'];


//先查询是否为QQ注册用户
$qq_member = C::t('#htt_qqlogin#qqlogin')->fetch_by_uid($_G['uid']);
if(!$qq_member){
//    showmessage($plugin_lang['bind_user_need_register']);
}




//先检测是否是QQ注册的用户。只有QQ注册的用户，然后才可以绑定其他的会员。
//如果修改了密码，则QQ登录无法使用。需要重新来绑定一次。


if($_POST['username']) {
    //将当前QQ登录的资料中的uid username password修改为用户自己填写的。
    //需要用户是否存在和密码是否相等。
//    $rs = C::t('common_member')->fetch_by_username($_POST['username']);
    $user = DB::fetch_first('SELECT * FROM  `pre_ucenter_members` WHERE  `username` LIKE  \''.addslashes($_POST['username']).'\'');

    if(empty($user)){
        //绑定的用户不存在
        showmessage($plugin_lang['bind_user_no_exist']);
    }

    $password = $_POST['password'];
    if($user['password'] != md5(md5($password).$user['salt'])){
        //说明密码错误。
        showmessage($plugin_lang['bind_user_password_wrong']);
    }
    //更新绑定的资料。
    $update_array = array(
        'uid'=>$user['uid'],
        'username'=>$user['username'],
        'password'=>$_POST['password'],
    );
    //索引应当是openid
    C::t('#htt_qqlogin#qqlogin')->update_by_openid($qq_member['openid'],$update_array);
    //退出重新登录。
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
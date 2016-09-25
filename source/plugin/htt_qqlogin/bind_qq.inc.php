<?php

function strFilter($str){
    $str = str_replace('`', '', $str);
    $str = str_replace('・', '', $str);
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
    $str = str_replace('――', '', $str);
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
    return trim($str);
}

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;
loadcache('plugin');
$plugin_lang = $scriptlang['htt_qqlogin'];
$qq_member = C::t('#htt_qqlogin#qqlogin')->fetch_by_uid($_G['uid']);
if(empty($qq_member)){
    $qq_member['nickname'] = lang('plugin/htt_qqlogin', 'no_bind_qq');
}


if($_POST['username']) {
    //将当前QQ登录的资料中的uid username password修改为用户自己填写的。
    //需要用户是否存在和密码是否相等。

    //过滤掉特殊符号
    $username = strFilter($_POST['username']);


    $user = DB::fetch_first('SELECT * FROM  '. DB::table("ucenter_members").' WHERE  `username` LIKE  \''.addslashes($username).'\'');

    if(empty($user)){
        //绑定的用户不存在
        showmessage(lang('plugin/htt_qqlogin', 'bind_user_no_exist'));
        // showmessage($plugin_lang['bind_user_no_exist']);
    }

    $password = $_POST['password'];
    if($user['password'] != md5(md5($password).$user['salt'])){
        //说明密码错误。
        showmessage(lang('plugin/htt_qqlogin', 'bind_user_password_wrong'));
        // showmessage($plugin_lang['bind_user_password_wrong']);
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
    showmessage( lang('plugin/htt_qqlogin', 'bind_user_success_login'),'/member.php?mod=logging&action=logout&formhash='.FORMHASH);


}





?>
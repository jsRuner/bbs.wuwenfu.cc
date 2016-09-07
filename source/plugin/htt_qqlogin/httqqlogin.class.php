<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_htt_qqlogin {

    function common() {

        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');


        define('IMGDIR','static/image/common');

        //如果没登录，同时非手机端
        if($_G['uid'] <=0 && !defined('IN_MOBILE') ){

            $_G['setting']['pluginhooks']['global_login_text'] =  '<a href="'.$site_url.'/plugin.php?id=htt_qqlogin:qqoauth" target="_top" rel="nofollow"><img src="'.IMGDIR.'/qq_login.gif" class="vm" /></a>';
        }

    }




    function global_login_extra(){
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');

        //登录过
        if( $_G['uid'] > 0){
            return '';
        }

        include_once template('htt_qqlogin:qqlogin_connect');
        return $qqlogin_html;
    }

    // 头部姓名 绑定QQ的提示。
    function global_usernav_extra1() {
        global $_G;
        include_once template('htt_qqlogin:module');
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');

        //插件没开启，用户没登录。
        if($_G['uid'] < 0){
            return '';
        }
        $query = DB::query("SELECT * FROM  ".DB::table("httqqlogin")." WHERE  `uid`= ".$_G['uid']);
        if($item = DB::fetch($query)) {
            # code...
            return '';
        }
        return tpl_global_usernav_extra1();
    }
}

class plugin_htt_qqlogin_member extends plugin_htt_qqlogin{

    function logging_method() {
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');
        if( $_G['uid'] > 0){
            return '';
        }
        include_once template('htt_qqlogin:qqlogin_simple_connect');
        return $qqlogin_simple_html;
    }

    function register_logging_method() {
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');
        if($_G['uid'] > 0){
            return '';
        }
        include_once template('htt_qqlogin:qqlogin_simple_connect');
        return $qqlogin_simple_html;
    }
}


?>
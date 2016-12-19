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
        
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');
        include_once template('htt_qqlogin:module');

        //插件没开启，用户没登录。
        if($_G['uid'] < 0){
            return '';
        }
        $query = DB::query("SELECT * FROM  ".DB::table("httqqlogin")." WHERE  `uid`= ".$_G['uid']);
        if($item = DB::fetch($query)) {
            # code...
            //存在QQ登录。要区分是否是QQ登录的用户。还是已经绑定了老帐号
            //如果不是老帐号，则提示关联。判断的依据。帐号时间与QQ登录中的时间对比。如果误差不超过1分钟。则说明需要绑定老帐号。
            //使用qq登录的用户。只有普通账号登录的，才会提示绑定qq登录。其他的则不显示。如何区分。从cookie区分。
            $member_query = DB::query("SELECT * FROM  ".DB::table("ucenter_members")." WHERE  `uid`= ".$_G['uid']);
            $member_info = DB::fetch($member_query);

            // if (abs($item['dateline']-$member_info['regdate']) <= 10) {
                # code...
                return '<a href="'.$site_url.'/home.php?mod=spacecp&ac=plugin&op=profile&id=htt_qqlogin:bind_qq" target="_blank"><img src="static/image/common/bb_qq.gif" class="qq_bind" align="absmiddle" alt=""></a>';
            // }
        }
        return tpl_global_usernav_extra1($site_url);
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
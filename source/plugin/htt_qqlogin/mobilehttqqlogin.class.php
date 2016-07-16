<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class mobileplugin_htt_qqlogin {
    function global_footer_mobile(){
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $is_open =  $var['htt_qqlogin']['is_open'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');

        //判断是否为登录界面。如果是，则显示登录按钮

        if($is_open==2 || $_G['uid'] >0 || $_GET['mod'] != 'logging' || $_GET['action'] != 'login'){
            return '';
        }

        include_once template('htt_qqlogin:qqlogin_mobile');
        return $qqlogin_mobile_html;
    }
}

?>
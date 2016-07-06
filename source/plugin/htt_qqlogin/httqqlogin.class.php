<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_htt_qqlogin {
    function global_login_extra(){
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $is_open =  $var['htt_qqlogin']['is_open'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');

        if($is_open==2){
            return '';
        }

        include_once template('htt_qqlogin:qqlogin');
        return $qqlogin_html;
    }

}

class plugin_htt_qqlogin_member extends plugin_htt_qqlogin{
    function logging_top(){
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $is_open =  $var['htt_qqlogin']['is_open'];
        $site_url = $var['htt_qqlogin']['site_url'];
        if(empty($site_url)){
            $site_url = $_G['siteurl'];
        }
        $site_url = rtrim($site_url,'/');
        if($is_open==2){
            return '';
        }
        include_once template('htt_qqlogin:qqlogin_simple');
        return $qqlogin_simple_html;
    }
}

?>
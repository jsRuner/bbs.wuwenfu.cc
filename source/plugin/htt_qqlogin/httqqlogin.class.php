<?php
/**
 *	[QQ��¼(htt_qqlogin.{modulename})] (C)2016-2099 Powered by ��������.
 *	Version: 1.0
 *	Date: 2016-5-20 23:46
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_htt_qqlogin {
	//TODO - Insert your code here

    function global_login_extra(){
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $is_open =  $var['htt_qqlogin']['is_open'];
        if($is_open==2){
            return '';
        }
        include_once template('htt_qqlogin:qqlogin');
        return $qqlogin_html;
    }

}

?>
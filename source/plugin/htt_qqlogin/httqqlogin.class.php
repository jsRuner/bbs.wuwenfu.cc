<?php
/**
 *	[QQµÇÂ¼(htt_qqlogin.{modulename})] (C)2016-2099 Powered by ±±°¶µÄÔÆ.
 *	Version: 1.0
 *	Date: 2016-5-20 23:46
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_htt_qqlogin {
	//TODO - Insert your code here

//    function global_header(){
//        global $_G;
//        return '21211';
//    }

    function global_login_extra(){
        global $_G;
        include_once template('htt_qqlogin:qqlogin');
        return $qqlogin_html;
    }

}

?>
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
        if($is_open==2 || $_G['uid'] >0){
            return '';
        }
      /*  return '
<a href="plugin.php?id=htt_qqlogin:qqoauth" style="display:block;text-align:center;margin: auto;"><img src="source/plugin/htt_qqlogin/template/image/bt_white_76.png" alt=""></a>
';*/
        include_once template('htt_qqlogin:qqlogin_mobile');
        return $qqlogin_mobile_html;
    }
}

?>
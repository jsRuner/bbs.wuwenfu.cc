<?php


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
?>
<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

//提示需要安装一些扩展。避免问题。mb_str扩展。

global $_G;

loadcache('plugin');
$var = $_G['cache']['plugin'];
$appid = $var['htt_qqlogin']['appid'];
$appkey = $var['htt_qqlogin']['key'];
$referer = dreferer();
dsetcookie('con_request_uri', $referer);

$callback = $_G['siteurl'] . 'plugin.php?id=htt_qqlogin:qqoauth_callback' . '&referer=' . urlencode($referer) . (!empty($_GET['isqqshow']) ? '&isqqshow=yes' : '');

if (defined('IN_MOBILE') || $_GET['oauth_style'] == 'mobile') {
    $callback .= '&display=mobile';
}


require_once("source/plugin/htt_qqlogin/API/qqConnectAPI.php");

$qc = new QC();
$qc->set_config($appid, $appkey, $callback);
$qc->qq_login();
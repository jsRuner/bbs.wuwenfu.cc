<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
global $_G;

loadcache('plugin');
$var = $_G['cache']['plugin'];
$is_open =  $var['htt_qqlogin']['is_open'];

$appid =  $var['htt_qqlogin']['appid'];
$appkey =  $var['htt_qqlogin']['key'];


$referer = dreferer();

dsetcookie('con_request_uri', $referer);


$callback =  $_G['siteurl'].'plugin.php?id=htt_qqlogin:qqoauth_callback'.'&referer=' . urlencode($referer) . (!empty($_GET['isqqshow']) ? '&isqqshow=yes' : '');

if(defined('IN_MOBILE') || $_GET['oauth_style'] == 'mobile') {
    $callback .= '&display=mobile';
}

if($is_open==2){
   die('qq is closed');
}

require_once("source/plugin/htt_qqlogin/API/qqConnectAPI.php");


$qc = new QC();

$qc->set_config($appid,$appkey,$callback);





$qc->qq_login();
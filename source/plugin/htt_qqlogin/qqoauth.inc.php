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
$callback =  $_G['siteurl'].'plugin.php?id=htt_qqlogin:qqoauth_callback';



if($is_open==2){
   die('qq is closed');
}

require_once("source/plugin/htt_qqlogin/API/qqConnectAPI.php");


$qc = new QC();

$qc->set_config($appid,$appkey,$callback);





$qc->qq_login();
<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: get.inc.php 33997 2013-09-17 06:46:37Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;
// $refererhost = parse_url($_SERVER['HTTP_REFERER']);
// $refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';
// if($refererhost['host'] != $_SERVER['HTTP_HOST'] || !extension_loaded('gd')){
// 	exit('Access Denied');
// }
$idhash = $_GET['idhash'];

//验证码的值。
$seccode = '123456';

$cvar['ltime'] = 30;

//记录验证码到数据库
$ssid = C::t('common_seccheck')->insert(array(
		    'dateline' => TIMESTAMP,
		    'code' => $seccode,
		    'succeed' => 0,
		    'verified' => 0,
		), true);

//插件本身校验验证码是否正确。
dsetcookie('seccode'.$idhash, authcode(strtoupper($seccode)."\t".(TIMESTAMP + $cvar['ltime'])."\t".$idhash."\t".FORMHASH, 'ENCODE', $_G['config']['security']['authkey']), 0, 1, true);

//后台验证次数。验证码的次数。
dsetcookie('seccode', $ssid.'.'.substr(md5($ssid.$_G['uid'].$_G['authkey']), 8, 18));



if(!$_G['setting']['nocacheheaders']) {
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
}

dheader("Content-type: image/png");
// echo $data;
$PSize = filesize('/Applications/MAMP/htdocs/bbs.wuwenfu.cn/source/plugin/htt_captcha/misc.png');
$picturedata = fread(fopen('/Applications/MAMP/htdocs/bbs.wuwenfu.cn/source/plugin/htt_captcha/misc.png', "r"), $PSize);
echo $picturedata;
// exit();

?>
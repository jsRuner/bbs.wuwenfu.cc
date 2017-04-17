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
$refererhost = parse_url($_SERVER['HTTP_REFERER']);
$refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';
if($refererhost['host'] != $_SERVER['HTTP_HOST'] || !extension_loaded('gd')){
	exit('Access Denied');
}

if(!$_G['setting']['nocacheheaders']) {
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
}

$cvar['font'] = '方正书宋简体.ttf';

require_once libfile('htt_captcha','plugin/htt_captcha');
header("Content-type:image/png");
$captcha5 = new Captcha();

//@设置验证码宽度
$captcha5->setWidth(100);
 
//@设置验证码高度
//$captcha5->setHeight(50);
 
//@设置字符个数
$captcha5->setTextNumber(2);

$fontcolor = '0x'.(sprintf('%02s', dechex (mt_rand(0, 255)))).(sprintf('%02s', dechex (mt_rand(0, 128)))).(sprintf('%02s', dechex (mt_rand(0, 255))));


//@设置字符颜色
// $captcha5->setFontColor('#ff0000');
$captcha5->setFontColor($fontcolor);
 
//@设置字号大小
//$captcha5->setFontSize(25);
 
//@设置字体
$captcha5->setFontFamily(DISCUZ_ROOT.'./source/plugin/htt_captcha/fonts/'.$cvar['font']);
 
//@设置语言
$captcha5->setTextLang('cn');
 
//@设置背景颜色
$fontcolor = '0x'.(sprintf('%02s', dechex (mt_rand(0, 255)))).(sprintf('%02s', dechex (mt_rand(0, 128)))).(sprintf('%02s', dechex (mt_rand(0, 255))));
// $captcha5->setBgColor($fontcolor);
 
//@设置干扰点数量
$captcha5->setNoisePoint(100);
 
//@设置干扰线数量
$captcha5->setNoiseLine(5);
 
//@设置是否扭曲
$captcha5->setDistortion(true);
 
//@设置是否显示边框
$captcha5->setShowBorder(true);
 
//输出验证码
$captcha5->initImage(); //创建基本图片

$code = $captcha5->createText();

$idhash = $_GET['idhash'];

//验证码的值。
$seccode = $code;

$cvar['ltime'] = 30; //30秒内有效果
//插件本身校验验证码是否正确。
dsetcookie('seccode'.$idhash, authcode(strtoupper($seccode)."\t".(TIMESTAMP + $cvar['ltime'])."\t".$idhash."\t".FORMHASH, 'ENCODE', $_G['config']['security']['authkey']), 0, 1, true);
$captcha5->createImage();

?>
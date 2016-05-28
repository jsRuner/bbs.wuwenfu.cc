<?php

/*==========================================================
 *	Plugin Name   ：onemary_job
 *	Plugin author : RaoLibao
 *	Updated date  : 2013-12-3
 *	Phone number  : (086)18650336706, (0591)83701411
 *	Other contact : QQ1609883787, Email 1609883787@qq.com
 *	AUTHOR URL    : http://www.onemary.com
 *	This is NOT a freeware, use is subject to license terms
=============================================================*/

header("content-type:text/html; charset=utf-8");

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_GET['nickName'] = iconv("UTF-8", $_G['charset'], $_GET['nickName']);
loaducenter();

if(!empty($_GET['nickName'])){
	$ucresult = uc_user_checkname($_GET['nickName']);
	if($ucresult > 0) {
		echo 'OK';
	} elseif($ucresult == -1) {
		echo 'register.fuhao.nickName';
	} elseif($ucresult == -2) {
		echo 'register.error.nickName';
	} elseif($ucresult == -3) {
		echo 'register.check.nickName';
	}
}

if(!empty($_GET['email'])){
	$ucresult = uc_user_checkemail($_GET['email']);
	if($ucresult > 0) {
		echo 'OK';
	} elseif($ucresult == -4) {
		echo 'register.error.email';
	} elseif($ucresult == -5) {
		echo 'register.buyunxu.email';
	} elseif($ucresult == -6) {
		echo 'register.beizhuche.email';
	}
}

if(!empty($_GET['validateCode'])){
	if(!check_seccode($_GET['validateCode'], $_GET['seccodehash'], 1, $_GET['$seccodemodid'])){
		echo 'register.error.validateCode';
	} else {
		echo 'register.ok.validateCode';
	}
}

?>
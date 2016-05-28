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

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
//echo "ss";
$i=1;
$gallery=C::t("#onemary_register#onemary_register_field")->get_open($i);
include template('onemary_register:registered');

?>
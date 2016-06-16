<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
if($_G['adminid'] < 1 ) exit('Access Denied:0032');
include template('milu_pick:help');
?>
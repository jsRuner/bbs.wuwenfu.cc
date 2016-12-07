<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
showtips(lang('plugin/htt_qqlogin', 'qq_help'));

include_once template('htt_qqlogin:qq_help');
?>
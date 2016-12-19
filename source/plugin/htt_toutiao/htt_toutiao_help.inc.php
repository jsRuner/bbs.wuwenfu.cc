<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
showtips(lang('plugin/htt_toutiao', 'htt_toutiao_help'));

include_once template('htt_toutiao:htt_toutiao_help');
?>
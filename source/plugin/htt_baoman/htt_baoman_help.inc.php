<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
showtips(lang('plugin/htt_baoman', 'htt_baoman_help'));

include_once template('htt_baoman:htt_baoman_help');
?>
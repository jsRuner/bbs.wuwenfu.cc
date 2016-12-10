<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
showtips(lang('plugin/htt_qsbk', 'htt_qsbk_help'));

include_once template('htt_qsbk:htt_qsbk_help');
?>
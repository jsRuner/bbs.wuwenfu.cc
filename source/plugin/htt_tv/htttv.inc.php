<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


global $_G;

loadcache('plugin');

$var = $_G['cache']['plugin'];


include_once template('htt_tv:tv');

?>
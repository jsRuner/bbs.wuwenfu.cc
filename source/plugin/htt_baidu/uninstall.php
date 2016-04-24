<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

runquery("
DROP TABLE IF EXISTS cdb_httbaidu");
runquery("
DROP TABLE IF EXISTS cdb_httbaidu_level");

$finish = TRUE;
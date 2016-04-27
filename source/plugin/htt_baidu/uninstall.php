<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

runquery("
DROP TABLE IF EXISTS cdb_httbaidu");
runquery("
DROP TABLE IF EXISTS cdb_httbaidu_level");

$cache_file =  DISCUZ_ROOT.'./data/sysdata/cache_htt_baidu.php';
if(file_exists($cache_file)){
	
@unlink($cache_file);
}

$finish = TRUE;
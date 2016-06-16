<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

runquery("
DROP TABLE IF EXISTS cdb_httzhanhao_zhanhao;
DROP TABLE IF EXISTS cdb_httzhanhao_category;
DROP TABLE IF EXISTS cdb_httzhanhao_record;
DROP TABLE IF EXISTS cdb_httzhanhao_share;
");




$finish = TRUE;
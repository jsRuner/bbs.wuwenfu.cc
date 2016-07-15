<?php
/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-04-24 15:14:57
 * @version $Id$
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
runquery("
ALTER TABLE  `cdb_httzhanhao_zhanhao` ADD  `deplay_time` INT( 11 ) NOT NULL COMMENT  '发布的时间' AFTER  `dateline`
");



$finish = TRUE;
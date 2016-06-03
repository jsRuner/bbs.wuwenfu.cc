<?php

if(!defined('IN_DISCUZ')) {	exit('Access Denied');}
$sql =<<<EOF

DROP TABLE IF EXISTS `pre_kylefu_spider_movie`;
DROP TABLE IF EXISTS `pre_kylefu_spider_movie_auto`;
DROP TABLE IF EXISTS `pre_kylefu_spider_movie_tag`;

EOF;
runquery($sql);
$finish = true;
?>
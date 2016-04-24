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
runquery("CREATE TABLE IF NOT EXISTS `cdb_httbaidu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `fid` int(10) NOT NULL,
  `dateline` int(10) NOT NULL,
  `credit` int(10) NOT NULL DEFAULT '1',
  `leveltitle` varchar(125) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;");


runquery("CREATE TABLE IF NOT EXISTS `cdb_httbaidu_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `floor` int(11) NOT NULL,
  `ceil` int(11) NOT NULL DEFAULT '-1',
  `leveltitle` varchar(125) NOT NULL,
  `dateline` int(11) NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;");

$finish = TRUE;
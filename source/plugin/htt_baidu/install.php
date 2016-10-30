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


$default_level_titles = explode(';',$installlang['default_level_titles']);



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


runquery("CREATE TABLE IF NOT EXISTS `cdb_httbaidu_forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `level_titles` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;");


runquery("INSERT INTO `cdb_httbaidu_level` (`id`, `floor`, `ceil`, `leveltitle`, `dateline`) VALUES
(1, 0, 100, '".$default_level_titles[0]."', 1476501725),
(2, 100, 200, '".$default_level_titles[1]."', 1476501744),
(3, 200, -1, '".$default_level_titles[2]."', 1476501754);
");


$finish = TRUE;
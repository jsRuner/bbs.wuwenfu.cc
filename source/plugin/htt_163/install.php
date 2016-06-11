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

CREATE TABLE IF NOT EXISTS `cdb_htt163_music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `music_id` int(11) NOT NULL,
  `music_p` tinyint(1) NOT NULL,
  `dateline` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;






");



$finish = TRUE;
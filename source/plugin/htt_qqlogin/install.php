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
runquery("CREATE TABLE IF NOT EXISTS `cdb_httqqlogin` (
  `uid` int(11) NOT NULL,
  `openid` varchar(125) NOT NULL,
  `access_token` varchar(125) NOT NULL,
  `dateline` int(11) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `openid` (`openid`)
) ENGINE=MyISAM;");



$finish = TRUE;
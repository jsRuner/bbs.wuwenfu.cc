<?php

/*==========================================================
 *	Plugin Name   ：onemary_job
 *	Plugin author : RaoLibao
 *	Updated date  : 2013-12-3
 *	Phone number  : (086)18650336706, (0591)83701411
 *	Other contact : QQ1609883787, Email 1609883787@qq.com
 *	AUTHOR URL    : http://www.onemary.com
 *	This is NOT a freeware, use is subject to license terms
=============================================================*/

if(!defined('IN_DISCUZ')) {
	exit('Access denied');
}
$sql = <<<EOT


CREATE TABLE IF NOT EXISTS `cdb_onemary_register_info` (
  `uid` int(10) NOT NULL,
  `groupid` int(4) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `cdb_onemary_register_field` (
  `gallery` tinyint(4) NOT NULL,
  `val` text NOT NULL,
  `open` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `new_groupid` tinyint(3) unsigned NOT NULL,
  `title` text NOT NULL,
  `bannerurl` text NOT NULL,
  `bannerimg` varchar(255) NOT NULL,
  `bgimg` varchar(255) NOT NULL,
  `gallery_name` varchar(25) NOT NULL,
  `bannertitle` varchar(255) NOT NULL,
  `regverify` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`gallery`)
) ENGINE=MyISAM;

INSERT INTO `cdb_onemary_register_field` (`gallery`, `val`, `open`, `new_groupid`, `title`, `bannerurl`, `bannerimg`, `bgimg`,`gallery_name`, `bannertitle`, `regverify`) VALUES
(1, '', 1, 10, '', '', '','', '', '', 0),
(2, '', 0, 10, '', '', '','', '', '', 0),
(3, '', 0, 10, '', '', '', '','', '', 0),
(4, '', 0, 10, '', '', '','', '', '', 0),
(5, '', 0, 10, '', '', '','', '', '', 0),
(6, '', 0, 10, '', '', '','', '', '', 0),
(7, '', 0, 10, '', '', '','', '', '', 0),
(8, '', 0, 10, '', '', '','', '', '', 0);

EOT;
runquery($sql);
$finish = true;
?>

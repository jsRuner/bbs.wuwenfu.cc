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

CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_record` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL COMMENT '提取者用户名',
  `zid` int(11) NOT NULL COMMENT '提取的账号id',
  `dateline` int(11) NOT NULL,
  `ip` varchar(125) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_category` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `title` varchar(15) NOT NULL COMMENT '分类标题',
  `info` varchar(125) NOT NULL COMMENT '分类描叙',
  `dateline` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分类状态。0表示启用1表示禁用',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_zhanhao` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL COMMENT '账号',
  `password` varchar(125) NOT NULL COMMENT '密码',
  `dateline` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '账号状态。0表示未被领取 1表示已经领取',
  `cid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;



CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_share` (
   `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL COMMENT '分析者的用户名',
  `dateline` int(11) NOT NULL,
  `ip` varchar(125) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;






");



$finish = TRUE;
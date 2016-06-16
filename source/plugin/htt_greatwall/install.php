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
  `title` varchar(25) NOT NULL COMMENT '分类标题',
  `info` varchar(2048) NOT NULL COMMENT '分类描叙',
  `dateline` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分类状态。0表示启用1表示禁用',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_zhanhao` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL COMMENT '账号',
  `password` varchar(25) NOT NULL COMMENT '密码',
  `dateline` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '账号状态。0表示未被领取 1表示已经领取',
  `cid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;



CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_share` (
   `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL COMMENT '分享者的用户名',
  `dateline` int(11) NOT NULL,
  `ip` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;






");



runquery("

CREATE TABLE IF NOT EXISTS `cdb_greatwall_employee` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `db_lang` varchar(20) DEFAULT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `is_super` enum('0','1') NOT NULL DEFAULT '0',
  `project_id` int(10) NOT NULL DEFAULT '0',
  `staff_no` varchar(50) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `intro` varchar(200) DEFAULT NULL,
  `config` longtext,
  `category_permit` longtext,
  `article_permit` varchar(255) DEFAULT NULL,
  `page_permit` longtext,
  `title` varchar(64) DEFAULT NULL,
  `gender` enum('0','1','2') NOT NULL DEFAULT '0',
  `birthday` date DEFAULT NULL,
  `province` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `district` varchar(64) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `addr` varchar(255) DEFAULT NULL,
  `zip` varchar(6) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `salt` varchar(6) NOT NULL,
  `status` enum('-1','0','1') NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `logincount` int(10) DEFAULT '0',
  `errors` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM   ;



CREATE TABLE IF NOT EXISTS `cdb_greatwall_game_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL DEFAULT '0',
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `to_member_id` bigint(20) NOT NULL DEFAULT '0',
  `point` int(10) NOT NULL DEFAULT '0',
  `is_draw` smallint(6) NOT NULL DEFAULT '0',
  `photo` varchar(128) DEFAULT NULL,
  `config` text,
  `status` enum('-1','0','1') NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ;



CREATE TABLE IF NOT EXISTS `cdb_greatwall_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `social_type` varchar(32) DEFAULT NULL,
  `openid` varchar(64) NOT NULL,
  `nickname` varchar(64) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `addr` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `gender` enum('0','1','2') NOT NULL DEFAULT '0',
  `province` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `district` varchar(64) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `regip` varchar(32) DEFAULT NULL,
  `point` int(10) NOT NULL DEFAULT '0',
  `subscribe` tinyint(1) NOT NULL DEFAULT '0',
  `config` text,
  `status` enum('-1','0','1') NOT NULL DEFAULT '1',
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  ;


CREATE TABLE IF NOT EXISTS `cdb_greatwall_prize` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `keys` varchar(50) NOT NULL,
  `prizes_nums` int(10) NOT NULL COMMENT '总数',
  `probability` int(11) NOT NULL DEFAULT '0' COMMENT '中奖概率率',
  `point` int(10) NOT NULL DEFAULT '0',
  `ticket_batch` varchar(16) DEFAULT NULL,
  `userlimit` int(10) NOT NULL DEFAULT '1',
  `config` text,
  `status` enum('-1','0','1','2') NOT NULL DEFAULT '1' COMMENT '-1-删除, 1-正常；0-锁定；',
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ;



CREATE TABLE IF NOT EXISTS `cdb_greatwall_prize_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL DEFAULT '0',
  `member_id` bigint(20) NOT NULL DEFAULT '0',
  `prize_id` int(10) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `addr` varchar(255) DEFAULT NULL,
  `ticket` varchar(32) DEFAULT NULL,
  `is_use` tinyint(1) NOT NULL DEFAULT '0',
  `source` tinyint(2) NOT NULL DEFAULT '0',
  `config` text,
  `ip` varchar(16) DEFAULT NULL,
  `status` enum('-1','0','1') NOT NULL DEFAULT '0' COMMENT '1-正常；0待审; -1-删除',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB   ;


CREATE TABLE IF NOT EXISTS `cdb_greatwall_project` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `project_type` varchar(32) DEFAULT NULL,
  `status` enum('-1','0','1') NOT NULL DEFAULT '1',
  `config` text,
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB   ;


CREATE TABLE IF NOT EXISTS `cdb_greatwall_ticket_batch` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(64) DEFAULT NULL,
  `batch` varchar(32) NOT NULL,
  `nums` int(10) DEFAULT NULL,
  `source` smallint(6) NOT NULL DEFAULT '0',
  `config` text,
  `status` enum('-1','0','1') NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM   ;



");



$finish = TRUE;
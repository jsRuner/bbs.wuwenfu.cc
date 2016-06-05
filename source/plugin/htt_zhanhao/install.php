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
  `username` varchar(15) NOT NULL COMMENT '��ȡ���û���',
  `zid` int(11) NOT NULL COMMENT '��ȡ���˺�id',
  `dateline` int(11) NOT NULL,
  `ip` varchar(125) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_category` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `title` varchar(15) NOT NULL COMMENT '�������',
  `info` varchar(125) NOT NULL COMMENT '��������',
  `dateline` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '����״̬��0��ʾ����1��ʾ����',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_zhanhao` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL COMMENT '�˺�',
  `password` varchar(125) NOT NULL COMMENT '����',
  `dateline` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�˺�״̬��0��ʾδ����ȡ 1��ʾ�Ѿ���ȡ',
  `cid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;



CREATE TABLE IF NOT EXISTS `cdb_httzhanhao_share` (
   `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL COMMENT '�����ߵ��û���',
  `dateline` int(11) NOT NULL,
  `ip` varchar(125) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;






");



$finish = TRUE;
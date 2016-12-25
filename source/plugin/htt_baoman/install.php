<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/4/3
 * Time: 16:55
 * description:
 *
 *
 */


if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

runquery("
CREATE TABLE IF NOT EXISTS `pre_htt_baoman_log` (
  `id` int(11) NOT NULL,
  `stime` int(11) NOT NULL   COMMENT '开始时间',
  `raw_content` text NOT NULL COMMENT '原始内容',
  `content` text NOT NULL COMMENT '整理后内容',
  `num` int(11) NOT NULL COMMENT '内容条数',
  `ids` varchar(125) NOT NULL COMMENT '关联的文章id.逗号分隔。'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pre_htt_baoman_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pre_htt_baoman_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

");




//todo:后期添加表
$finish = True;

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/13
 * Time: 16:42
 * description: 论坛管理员的项目管理。提供 编辑操作。
 *
 *
 */

//error_reporting(E_ALL);

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}


global $_G;

header('location:'.$_G['siteurl'].'plugin.php?id=htt_greatwall:manager');


?>
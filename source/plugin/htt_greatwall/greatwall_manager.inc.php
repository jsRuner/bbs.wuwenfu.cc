<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/13
 * Time: 16:42
 * description: ��̳����Ա����Ŀ�����ṩ �༭������
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
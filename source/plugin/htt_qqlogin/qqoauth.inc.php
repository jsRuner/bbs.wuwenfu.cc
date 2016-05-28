<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ÎâÎÄ¸¶ hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/5/21
 * Time: 10:43
 * description:
 *http://bbs.wuwenfu.cc/plugin.php?id=htt_qqlogin:qqoauth
 *
 */
//echo 11;
//echo "<pre>";
//var_dump($_G);
//echo "</pre>";
//exit();
error_reporting(E_ALL);
require_once("source/plugin/htt_qqlogin/API/qqConnectAPI.php");
$qc = new QC();

//var_dump($qc);



$qc->qq_login();
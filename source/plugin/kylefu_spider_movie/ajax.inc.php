<?php
/**
 * User: KyleFu
 * Date: 16/4/16
 * Time: 14:22
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once libfile('function/common', 'plugin/kylefu_spider_movie');
$mode = trim($_POST["mode"]);
if(addslashes($_POST['hash']) != FORMHASH){
    exit;
}
if(in_array($mode, array("tag", "auto", "task"))){
    
}
exit;
?>
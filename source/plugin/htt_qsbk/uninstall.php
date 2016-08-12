<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) exit('Access Denied!');


$cache_file =  DISCUZ_ROOT.'./data/sysdata/cache_htt_qsbk.php';
if(file_exists($cache_file)){

    @unlink($cache_file);
}



?>
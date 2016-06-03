<?php
/**
 * User: KyleFu
 * Date: 16/4/16
 * Time: 14:22
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_kylefu_spider_movie {

    function global_footer(){
        global $_G, $kylefu_spider_movie_var;
        require_once libfile('function/common', 'plugin/kylefu_spider_movie');
        if($kylefu_spider_movie_var["auto"]){
            
        }
    }
}
?>
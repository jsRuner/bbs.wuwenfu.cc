<?php
/**
 * User: KyleFu
 * Date: 16/4/16
 * Time: 14:24
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if(!isset($_G['cache']['plugin'])){
    loadcache('plugin');
}
$kylefu_spider_movie_var = $_G['cache']['plugin']['kylefu_spider_movie'];

function plugin_lang($lang, $symbol = "/"){
    if(strpos($lang, $symbol)){
        $language = "";
        foreach(explode($symbol, $lang) as $l){
            $language .= lang('plugin/kylefu_spider_movie', $l);
        }
        return $language;
    }
    return lang('plugin/kylefu_spider_movie', $lang);
}
function jqueryload($content){
    $script = '<script src="http://libs.baidu.com/jquery/1.8.3/jquery.min.js" type="text/javascript" type="text/javascript"></script>';
    $script .= '<script type="text/javascript">var jQ=jQuery.noConflict(true); '.$content.'</script>';
    return $script;
}
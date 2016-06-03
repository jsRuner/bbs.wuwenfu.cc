<?php
/**
 * User: KyleFu
 * Date: 16/4/16
 * Time: 14:26
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class spider_mp4ba_module extends spider {
    var $version = '1.0';
    var $copyright = 'KyleFu';
    var $name = '高清MP4';
    var $url = "http://www.mp4ba.com/";
    var $charset = "utf-8";
    var $dom = array(
        "list"      => "#data_list a",
        "page"      => ".pages a",
        "main"      => ".main .intro",
        "format"    => ".intro_inner",
        "extend"    => "",
        "magnet"    => ".basic_info .magnet"
    );
    var $link = array(
        array(
            "name"  => "国产电影",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=1"
        ),
        array(
            "name"  => "港台电影",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=2"
        ),
        array(
            "name"  => "欧美电影",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=3"
        ),
        array(
            "name"  => "日韩电影",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=8"
        ),
        array(
            "name"  => "海外电影",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=4"
        ),
        array(
            "name"  => "动画电影",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=9"
        ),
        array(
            "name"  => "国产电视剧",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=9"
        ),
        array(
            "name"  => "港台电视剧",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=6"
        ),
        array(
            "name"  => "欧美电视剧",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=7"
        ),
        array(
            "name"  => "综艺娱乐",
            "url"   => "http://www.mp4ba.com/index.php?sort_id=11"
        )
    );
}
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
    var $copyright = 'wuwenfu.cn';
    var $name = '糗事百科';
    var $url = "http://www.qiushibaike.com/";
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
            "name"  => "热门8小时",
            "url"   => "http://www.qiushibaike.com/"
        ),
        array(
            "name"  => "热门12小时",
            "url"   => "http://www.qiushibaike.com/hot/"
        ),
        array(
            "name"  => "热门穿越",
            "url"   => "http://www.qiushibaike.com/history/"
        ),
        array(
            "name"  => "最热图片",
            "url"   => "http://www.qiushibaike.com/imgrank/"
        ),
        array(
            "name"  => "最新图片",
            "url"   => "http://www.qiushibaike.com/pic/"
        ),
        array(
            "name"  => "最新文字",
            "url"   => "http://www.qiushibaike.com/textnew/"
        ),
        array(
            "name"  => "最热文字",
            "url"   => "http://www.qiushibaike.com/text/"
        ),

    );
}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/4
 * Time: 12:22
 * description:
 *
 *
 */

function time_tran($the_time)
{
//    $now_time = date("Y-m-d H:i:s", time() + 8 * 60 * 60);
    $now_time = time();
    $show_time = $the_time;
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return '';
    } else {
        if ($dur < 60) {
            return $dur . '��ǰ';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '����ǰ';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . 'Сʱǰ';
                } else {
                    if ($dur < 259200) {//3����
                        return floor($dur / 86400) . '��ǰ';
                    } else {
                        return $the_time;
                    }
                }
            }
        }
    }
}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/20
 * Time: 22:22
 * description:
 *
 *
 */


/*
 * ���������ַ�
 * */

if(!function_exists('htt_string_filter')){

    function htt_string_filter($str)
    {
        $str = str_replace('`', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('~', '', $str);
        $str = str_replace('!', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('@', '', $str);
        $str = str_replace('#', '', $str);
        $str = str_replace('$', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('%', '', $str);
        $str = str_replace('^', '', $str);
        $str = str_replace('����', '', $str);
        $str = str_replace('&', '', $str);
        $str = str_replace('*', '', $str);
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('-', '', $str);
        $str = str_replace('_', '', $str);
        $str = str_replace('����', '', $str);
        $str = str_replace('+', '', $str);
        $str = str_replace('=', '', $str);
        $str = str_replace('|', '', $str);
        $str = str_replace('\\', '', $str);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);
        $str = str_replace(';', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace(':', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('\'', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace(',', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('<', '', $str);
        $str = str_replace('>', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('.', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('/', '', $str);
        $str = str_replace('��', '', $str);
        $str = str_replace('?', '', $str);
        $str = str_replace('��', '', $str);
        return trim($str);
    }
}

/**
 * ָ��Ŀ¼���������ļ��С�
 * ���� 201606/20
 */
if(!function_exists('make_dir')){
    function make_dir($dir='.'){
        $dir1 = date('Ym');
        $dir2 = date('d');
        !is_dir($dir . '/' . $dir1) && mkdir($dir . '/' . $dir1, 0777);
        !is_dir($dir . '/' . $dir1 . '/' . $dir2) && mkdir($dir . '/' . $dir1 . '/' . $dir2, 0777);
        return $dir . '/' . $dir1 . '/' . $dir2 . '/';
    }
}



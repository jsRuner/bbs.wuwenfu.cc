<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/4
 * Time: 23:26
 * description:
 *
 *
 */
exit();
//接受ajax请求。优先处理。
if($_GET['op'] == 'fetch'){


    if ($_GET['formhash'] != FORMHASH) {
        die(1);
    }
    if($_G['uid'] <=0  || $_G['uid'] != $_GET['uid']){
        die(2);
    }




    //先判断是否登录
    echo 222;



    //然后判断是否。

    exit();
}

echo 11;
exit();

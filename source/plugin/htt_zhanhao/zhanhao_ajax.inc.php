<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/6/4
 * Time: 23:26
 * description:
 *
 *
 */
exit();
//����ajax�������ȴ���
if($_GET['op'] == 'fetch'){


    if ($_GET['formhash'] != FORMHASH) {
        die(1);
    }
    if($_G['uid'] <=0  || $_G['uid'] != $_GET['uid']){
        die(2);
    }




    //���ж��Ƿ��¼
    echo 222;



    //Ȼ���ж��Ƿ�

    exit();
}

echo 11;
exit();

<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
global $_G;

//print_r($_G);
//echo $_G['siteurl'].'plugin.php?id=htt_qqlogin:qqoauth_callback';
//exit();
//����ǲ��������ù�
//if(file_exists("source/plugin/htt_qqlogin/API/comm/setted.inc")){
//    echo '<meta charset="UTF-8">';
//    die("����ɾ��intallĿ¼��setted.inc�ļ��ٽ�������<br /><span style='color:red'>��������óɹ�����������������ֻ����APIĿ¼���ļ���ɾ��intallĿ¼�º������ļ�</span>");
//}

if(!function_exists("curl_init")){
    echo "<h1>���ȿ���curl֧��</h1>";
    echo "
        ����php curl������Ĳ���(for windows)<br />
        1).ȥ��windows/php.ini �ļ���;extension=php_curl.dllǰ���; /*�� echo phpinfo();�鿴php.ini��·��*/<br />
        2).��php5/libeay32.dll��ssleay32.dll���Ƶ�ϵͳĿ¼windows/��<br />
        3).����apache<br />
        ";
    exit();
}
if($_POST){
//    print_r($_POST);
//    exit();
//
//    foreach($_POST as $k => $val){
//        if($k !='scrolltop' && empty($val)){
//            die("����д$k");
//        }
//    }
    $data['storageType'] = "file";
    $data['host'] = "localhost";
    $data['user'] = "root";
    $data['password'] = "root";
    $data['database'] = "test";
//    $_POST['scope'] = implode(",",$_POST['scope']);
//    $data['scope'] = array("get_user_info","add_share","list_album","add_album","upload_pic","add_topic","add_one_blog","add_weibo","check_page_fans","add_t","add_pic_t","del_t","get_repost_list","get_info","get_other_info","get_fanslist","get_idolist","add_idol","del_idol","get_tenpay_addr");
    $data['scope'] = implode(",", array("get_user_info","add_share","list_album","add_album","upload_pic","add_topic","add_one_blog","add_weibo","check_page_fans","add_t","add_pic_t","del_t","get_repost_list","get_info","get_other_info","get_fanslist","get_idolist","add_idol","del_idol","get_tenpay_addr"));
//    $_POST['errorReport'] = (boolean) $_POST['errorReport'];
    $data['errorReport'] = True;
    $data['appid'] = $_POST['httqqlogin_appid'];
    $data['appkey'] = $_POST['httqqlogin_appsecret'];
    //ע�����������⡣
    $data['callback'] = $_G['siteurl'].'plugin.php?id=htt_qqlogin:qqoauth_callback';



//    foreach($_POST as $k=>$v){
//        if(empty($v)){
//            unset($_POST[$k]);
//        }
//    }
//    print_r($_POST);
//    echo 11;
//    $data = $_POST;
////    echo serialize($data);
//    $data = array('111'=>$_POST['scope']);



//    echo json_encode($data);
//    echo json_encode($_POST);
//    exit();

    $setting = "<?php die('forbidden'); ?>\n";
    $setting .= json_encode($data);

//    echo $setting;
//    exit();
    $setting = str_replace("\/", "/",$setting);
    $incFile = fopen("source/plugin/htt_qqlogin/API/comm/inc.php","w+") or die("������API\comm\inc.php��Ȩ��Ϊ777");
    if(fwrite($incFile, $setting)){
        echo "<meta charset='utf-8' />";
        echo "���óɹ�";

        fclose($incFile);
//        fclose(fopen("setted.inc", "w"));
    }else{
        echo "Error";
    }
}else{
//    require_once("source/plugin/htt_qqlogin/template/qqlogin_config.htm");
    showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_qqlogin&pmod=api_config', 'enctype');

    showtableheader();
    showsetting(lang('plugin/htt_qqlogin', 'httqqlogin_appid'), 'httqqlogin_appid', $setting['httqqlogin_appId'], 'text');
    showsetting(lang('plugin/htt_qqlogin', 'httqqlogin_appsecret'), 'httqqlogin_appsecret', $setting['httqqlogin_appsecret'], 'text');
//    showsetting(lang('plugin/htt_qqlogin', 'httqqlogin_callbackurl'), 'httqqlogin_callbackurl', $setting['httqqlogin_callbackurl'], 'text');

    showtablefooter();

    showtableheader();
    showsubmit('settingsubmit');
    showtablefooter();

    showformfooter();
}

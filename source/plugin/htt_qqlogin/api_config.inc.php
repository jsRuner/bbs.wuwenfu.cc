<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
global $_G;

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
    $data['storageType'] = "file";
    $data['host'] = "localhost";
    $data['user'] = "root";
    $data['password'] = "root";
    $data['database'] = "test";
    $data['scope'] = implode(",", array("get_user_info","add_share","list_album","add_album","upload_pic","add_topic","add_one_blog","add_weibo","check_page_fans","add_t","add_pic_t","del_t","get_repost_list","get_info","get_other_info","get_fanslist","get_idolist","add_idol","del_idol","get_tenpay_addr"));
    $data['errorReport'] = True;
    $data['appid'] = $_POST['httqqlogin_appid'];
    $data['appkey'] = $_POST['httqqlogin_appsecret'];
    //ע�����������⡣
    $data['callback'] = $_G['siteurl'].'plugin.php?id=htt_qqlogin:qqoauth_callback';

    $setting = "<?php die('forbidden'); ?>\n";
    $setting .= json_encode($data);
    $setting = str_replace("\/", "/",$setting);
    $incFile = fopen("source/plugin/htt_qqlogin/API/comm/inc.php","w+") or die("������API\comm\inc.php��Ȩ��Ϊ777");
    if(fwrite($incFile, $setting)){
        echo "<meta charset='utf-8' />";
        echo "���óɹ�";
        fclose($incFile);
    }else{
        echo "Error";
    }
}else{
    showformheader('plugins&operation=config&do='.$pluginid.'&identifier=htt_qqlogin&pmod=api_config', 'enctype');
    showtableheader();
    showsetting(lang('plugin/htt_qqlogin', 'httqqlogin_appid'), 'httqqlogin_appid', $setting['httqqlogin_appId'], 'text');
    showsetting(lang('plugin/htt_qqlogin', 'httqqlogin_appsecret'), 'httqqlogin_appsecret', $setting['httqqlogin_appsecret'], 'text');
    showtablefooter();
    showtableheader();
    showsubmit('settingsubmit');
    showtablefooter();

    showformfooter();
}

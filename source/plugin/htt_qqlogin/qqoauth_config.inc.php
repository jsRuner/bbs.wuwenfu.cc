<?php
//����ǲ��������ù�
if(file_exists("setted.inc")){
    echo '<meta charset="UTF-8">';
    die("����ɾ��intallĿ¼��setted.inc�ļ��ٽ�������<br /><span style='color:red'>��������óɹ�����������������ֻ����APIĿ¼���ļ���ɾ��intallĿ¼�º������ļ�</span>");
}
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

    foreach($_POST as $k => $val){
        if(empty($val)){
            die("����д$k");
        }
    }
    $_POST['storageType'] = "file";
    $_POST['host'] = "localhost";
    $_POST['user'] = "root";
    $_POST['password'] = "root";
    $_POST['database'] = "test";
    $_POST['scope'] = implode(",",$_POST['scope']);
    $_POST['errorReport'] = (boolean) $_POST['errorReport'];
    $setting = "<?php die('forbidden'); ?>\n";
    $setting .= json_encode($_POST);
    $setting = str_replace("\/", "/",$setting);
    $incFile = fopen("source/plugin/htt_qqlogin/API/comm/inc.php","w+") or die("������API\comm\inc.php��Ȩ��Ϊ777");
    if(fwrite($incFile, $setting)){
        echo "<meta charset='utf-8' />";
        echo "���óɹ�,<a href='source/plugin/htt_qqlogin/example/'>�鿴example</a><br /><span style='color:red'>��������óɹ�����������������ֻ����APIĿ¼���ļ���ɾ��intallĿ¼�º������ļ�</span>";

        fclose($incFile);
        fclose(fopen("setted.inc", "w"));
    }else{
        echo "Error";
    }
}else{
    require_once("source/plugin/htt_qqlogin/template/qqlogin_config.htm");
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 吴文付 hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/4/8
 * Time: 16:51
 * description:
 *
 *
 */
//http://bbs.wuwenfu.cc/plugin.php?id=htt_robot:robot 访问该页面的url
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}


// echo $_G['siteurl'];
// exit();

/*echo "<pre>";
var_dump($_G['setting']['discuzurl']);
echo "</pre>";

exit();*/
if ($_POST['formhash']!= FORMHASH) {
    showmessage('undefined_action');
}

function curl_html($url)
{

    $curl = curl_init(); //开启curl
    curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
    $html = curl_exec($curl); //执行curl操作
    curl_close($curl);

    return $html;
}

loadcache('plugin');
$var = $_G['cache']['plugin'];
$groupstr = $var['htt_robot']['groups']; //用户组。哪些用户组可以看到机器人。
$welcome_msg = $var['htt_robot']['welcome_msg']; //欢迎语
$tuling_key = $var['htt_robot']['tuling_key']; //key
$check = $var['htt_robot']['is_show'];  //1隐藏 2启用


$key = $tuling_key;

$info = $_POST['msg'];

$url = 'http://www.tuling123.com/openapi/api?key=' . $key . '&info=' . urlencode($info);

$replystr = dfsockopen($url);

$replyarr = json_decode($replystr, true);

$returnmsg = $replyarr['text'];

//如果自动带的请求无效。则使用curl模块
if (empty($returnmsg)) {
	$replystr = curl_html($url);
	$replyarr = json_decode($replystr, true);
	$returnmsg = $replyarr['text'];
}

//如果还不行，则使用file_get_contents
if (empty($returnmsg)) {
	$replystr = file_get_contents($url);
	$replyarr = json_decode($replystr, true);
	$returnmsg = $replyarr['text'];
}

// echo json_encode(array('msg' => $replyarr['text']));
echo json_encode(array('msg' =>$returnmsg));

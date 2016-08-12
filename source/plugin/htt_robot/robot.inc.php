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

global $_G;

loadcache('plugin');
$var = $_G['cache']['plugin'];
$groupstr = $var['htt_robot']['groups']; //用户组。哪些用户组可以看到机器人。
$welcome_msg = $var['htt_robot']['welcome_msg']; //欢迎语
$robot_type = $var['htt_robot']['robot_type']; //机器人类型

$robot_key = $var['htt_robot']['robot_key']; //key
$robot_secret = $var['htt_robot']['robot_secret']; //secret


$check = $var['htt_robot']['is_show'];  //1隐藏 2启用

$info = $_POST['msg'];

//如果是茉莉机器人
if ($robot_type == 2) {
    $url = "http://i.itpk.cn/api.php?question=".urlencode($info)."&api_key=$robot_key&api_secret=$robot_secret";
    $returnmsg = dfsockopen($url);
}else{
    $url = 'http://www.tuling123.com/openapi/api?key=' . $robot_key . '&info=' . urlencode($info);
    $replystr = dfsockopen($url);
    $replyarr = json_decode($replystr, true);
    $returnmsg = $replyarr['text'];
}

//输入结果
echo json_encode(array('msg' =>$returnmsg));


if($_G['charset'] == 'gbk'){

    $info =   iconv("utf-8", "gbk",$info);
    $returnmsg =   iconv("utf-8", "gbk",$returnmsg);

}


if(empty($_G['username'])){
    $username=lang('plugin/htt_robot', 'guest') ;
}else{
    $username = $_G['username'];
}

$insert_array = array(
    'uid'=>$_G['uid'],
    'username'=>$username,
    'ip'=> $_G['clientip'],
    'dateline'=>TIMESTAMP,
    'message'=>$info,
    'reply'=>$returnmsg,
);



//存到记录中去。
DB::insert("httrobot_message",$insert_array);

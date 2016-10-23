<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ���ĸ� hi_php@163.com
 * Blog: wuwenfu.cn
 * Date: 2016/4/8
 * Time: 16:51
 * description:
 *
 *
 */
//http://bbs.wuwenfu.cc/plugin.php?id=htt_robot:robot ���ʸ�ҳ���url
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if ($_GET['formhash']!= FORMHASH) {
    showmessage('undefined_action');
}

header("Content-Type: application/json; charset=".$_G['charset']);

function unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
    $arruni = explode($prefix, $unistr);
    $unistr = '';
    for($i = 1, $len = count($arruni); $i < $len; $i++) {
        if (strlen($postfix) > 0) {
            $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
        }
        $temp = intval($arruni[$i]);
        $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
    }
    return iconv('UCS-2', $encoding, $unistr);
}

function curl_html($url)
{

    $curl = curl_init(); //����curl
    curl_setopt($curl, CURLOPT_URL, $url); //���������ַ
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //�Ƿ���� 1 or true �ǲ���� 0  or false���
    $html = curl_exec($curl); //ִ��curl����
    curl_close($curl);

    return $html;
}

function arrayToString($arr) {
    if (is_array($arr)){
        return implode(',', array_map('arrayToString', $arr));
    }
    return $arr;
}
//���˵�ͼƬ
function filterImg($str){
//    $str=preg_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_/+.~#?&//=]+(.jpg|.png){1})','<img src="\0" />',$str);
    $str=preg_replace('(,{1}((f|ht){1}tp://)[-a-zA-Z0-9@:%_/+.~#?&//=]+(.jpg|.png){1})','',$str);
    return $str;
}

function linkAdd($content){
    //��ȡ�滻������A��ǩ��ͳһ���<{link}>��
    preg_match_all('{<a.*?href=".*?".*?>.*?</a>}i',$content,$linkList);
    $linkList=$linkList[0];
    $str=preg_replace('{<a.*?href=".*?".*?>.*?</a>}i','<{link}>',$content);
    //��ȡ�滻�����е�IMG��ǩ��ͳһ���<{img}>��
    preg_match_all('{<img[^>]+>}im',$content,$imgList);
    $imgList=$imgList[0];
    $str=preg_replace('{<img[^>]+>}im','<{img}>',$str);

    //��ȡ�滻��׼��URL��ַ
    $str=preg_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_/+.~#?&//=]+[^(.jpg)|^(.png)])','<a href="\0" target="_blank">\0</a>',$str);


    //��ԭAͳһ���Ϊԭ����A��ǩ
    $arrLen=count($linkList);
    for($i=0;$i<$arrLen;$i++){
        $str=preg_replace('{<{link}>}',$linkList[$i],$str,1);
    }

    //��ԭIMGͳһ���Ϊԭ����IMG��ǩ
    $arrLen2=count($imgList);
    for($i=0;$i<$arrLen2;$i++){
        $str=preg_replace('{<{img}>}',$imgList[$i],$str,1);
    }

    return $str;
}

global $_G;

loadcache('plugin');
$var = $_G['cache']['plugin'];
$groupstr = $var['htt_robot']['groups']; //�û��顣��Щ�û�����Կ��������ˡ�
$welcome_msg = $var['htt_robot']['welcome_msg']; //��ӭ��
$robot_type = $var['htt_robot']['robot_type']; //����������

$robot_key = $var['htt_robot']['robot_key']; //key
$robot_secret = $var['htt_robot']['robot_secret']; //secret

$check = $var['htt_robot']['is_show'];  //1���� 2����

$info = $_GET['msg'];

//����ǵ��ҡ���ִ��������߼���
if($info == 'click me'){
    $srchadd .= " AND  `message` != '' ";
    $count = C::t('#htt_robot#message')->count_by_search($srchadd);
    if($count==0){
        $returnmsg = lang('plugin/htt_robot', 'no_message') ;
    }else{
        $messages = C::t('#htt_robot#message')->fetch_all($srchadd);
        $random_message_index = rand(1,$count-1);
        $returnmsg = $messages[$random_message_index]['message'];

        if($_G['charset'] == 'gbk') {
            $returnmsg =   iconv("gbk", "utf-8",$returnmsg);
        }
    }
    echo json_encode(array('msg' =>$returnmsg));
    exit();
}


//��������������
if ($robot_type == 2) {
    $url = "http://i.itpk.cn/api.php?question=".urlencode($info)."&api_key=$robot_key&api_secret=$robot_secret";
    $returnmsg = dfsockopen($url);
}else{
    $url = 'http://www.tuling123.com/openapi/api?key=' . $robot_key . '&info=' . urlencode($info);
    $replystr = dfsockopen($url);
    $replyarr = json_decode($replystr, true);
    $returnmsg = arrayToString($replyarr);
    $returnmsg = filterImg($returnmsg);
    $returnmsg = linkAdd($returnmsg);
    $returnmsg=preg_replace('((\d)+,{1})','',$returnmsg);
}

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



//�浽��¼��ȥ��
DB::insert("httrobot_message",$insert_array);

<?php
/**
 *    [htttoutiao(htt_toutiao.cron_toutiao)] (C)2016-2099 Powered by ���ĸ�.
 *    Version: 1.0
 *    Date: 2016-4-2 16:24
 *    Warning: Don't delete this comment
 *
 *
 *    cronname: info_cronname
 *    week: -1
 *    day:-1
 *    hour:5
 *    minute:30
 */

//error_reporting(E_ALL);

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
function set_home($dir = '.')
{
    $dir1 = date('Ym');
    $dir2 = date('d');
    !is_dir($dir . '/' . $dir1) && mkdir($dir . '/' . $dir1, 0777);
    !is_dir($dir . '/' . $dir1 . '/' . $dir2) && mkdir($dir . '/' . $dir1 . '/' . $dir2, 0777);
    return $dir . '/' . $dir1 . '/' . $dir2 . '/';
}

/*
 * ���������ַ�
 * */
function strFilter($str)
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

function curl_qsbk($url)
{
    $curl = curl_init(); //����curl
    $header[] = "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0";
    $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
    $header[] = "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Upgrade-Insecure-Requests:1";
    $header[] = "Connection: keep-alive";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $url); //���������ַ
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //�Ƿ���� 1 or true �ǲ���� 0  or false���
    $html = curl_exec($curl); //ִ��curl����
    curl_close($curl);

    if (empty($html)) {
        return curl_qsbk($url);
    }
    return $html;
}

loadcache('plugin');
$var = $_G['cache']['plugin'];
$fidstr = $var['htt_toutiao']['fids'];

$fids = unserialize($fidstr);

//var_dump($fids);

//exit();


//$uidstr = $var['htt_toutiao']['uids'];

//$groupstr = $var['htt_toutiao']['groups']; //�û���
//$threads = $var['htt_toutiao']['threads'];
$charset_num = $var['htt_toutiao']['charset'];  // 1utf-8 2gbk
//$caiji_model = $var['htt_toutiao']['caiji_model']; //1���� 2��ʾ��ͼ 3ͼ��
//$check = $var['htt_toutiao']['check'];  //1����� 2��ˡ�
//$title_length = $var['htt_toutiao']['title_length']; //���ⳤ��
$title_default = $var['htt_toutiao']['title_default']; //Ĭ�ϱ���
//$post_model = $var['htt_toutiao']['post_model']; //����ģʽ
$model = $var['htt_toutiao']['model']; //�ɼ���ģ��

//echo 11;

//echo $models;
//exit();
/*
$models = array_filter(unserialize($model_str));
if (is_null($models) || empty($models)) {
    return;
}*/

//print_r($models) ;
//exit();


//����Դ��
$urls = array(
    '1' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_hot", //�ȵ�
    '2' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_society", //���
    '3' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_entertainment",//����
    '4' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_tech", //�Ƽ�
    '5' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_car", //����
    '6' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_sports", //����
    '7' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_finance", //�ƾ�
    '8' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_military", //����
    '9' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_world", //����
    '10' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_fashion", //ʱ��
    '11' => "http://toutiao.com/api/article/recent/?source=2&count=20&category=news_travel", //����
);


//����ѡ��ֻ��ȡһ����
//$rand_keys = array_rand($urls, 1);
$url = $urls[$model];
//$url = 'http://toutiao.com/api/article/recent/?source=2&count=20&category=news_society'; //�����Ϣ����ӿڡ�
$html = curl_qsbk($url);

$toutiaos = json_decode($html,true);
//echo '<pre>';
//var_dump($toutiaos);
//echo '</pre>';
//
////echo $html;
//exit();

$imgpath = set_home('data/attachment/forum'); //���ص���ȫ·����

////��������
//include_once DISCUZ_ROOT . './source/plugin/htt_toutiao/include/phpQuery/phpQuery.php';
//phpquery::newDocumentHTML($html, 'utf-8');
#��ȡ�����б��������Ǹ���
$articles = $toutiaos['data'];
$count = 1; //����


$tid = 0; //����Ĭ��ֵ

$lasttid = 0; //��һ�ε�tid.

$first = 0;


foreach ($articles as $article) {



    $data = array();
    $data['url'] = $article['article_url']; //���ӱ���.
    $data['title'] = $article['title']; //���ӱ���.

//    echo iconv("UTF-8", "gbk", $data['title']);
//
//    echo '<br>';
//
//    continue;

    $data['content'] = $article['abstract']; //ժҪ
    if(empty($article['image_list'])){
        $data['img'] = '';
    }else{
        $data['img'] = $article['image_list'][0]['url'];
    }

    $remote = 0;
    //ͼƬ���ڡ�
    if (!empty($data['img'])) {
        //ͼƬĿ¼���������ء�
        $dir1 = date('Ym');
        $dir2 = date('d');

        !is_dir('data/attachment/forum/' . $dir1) && mkdir($dir . '/' . $dir1, 0777);
        !is_dir('data/attachment/forum/' . $dir1 . '/' . $dir2) && mkdir($dir . '/' . $dir1 . '/' . $dir2, 0777);


        $img_name = TIMESTAMP . uniqid() . '.png';
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 30 //��ʱʱ�䣬��λΪ��
            )
        ));

        @file_put_contents('data/attachment/forum/' . $dir1 . '/' . $dir2 . '/' . $img_name, file_get_contents($data['img'], 0, $context));
        $data['img'] = $dir1 . '/' . $dir2 . '/' . $img_name;
        $remote = 0; //����ͼƬ���� 1��ʾԶ��ͼƬ��0��ʾ����ͼƬ��
        $attachment = 2; //����,0�޸��� 1��ͨ���� 2��ͼƬ����

    } else {
        $attachment = 0; //����,0�޸��� 1��ͨ���� 2��ͼƬ����
    }




    $invisible = 0; //������ˡ�
    $displayorder = 0; //��Ҫ��˵�����Ϊ-2

//���ѡ��һ�������û���
    $fid_key = array_rand($fids, 1);

    $fid = $fids[$fid_key];

    $uid = 1; //Ĭ�Ͼ��ǹ���Ա

    $forum = C::t('forum_forum')->fetch_info_by_fid($fid);


    $userinfo = C::t('common_member')->fetch($uid);


    $author = $userinfo['username'];

    //ת�����롣�������utf-8������Ҫת����Ĭ��Ϊutf-8
    if ($charset_num != 1) {
        $data['content'] = iconv("UTF-8", "gbk", $data['content']);
        $data['title'] = iconv("UTF-8", "gbk", $data['title']);
    }
    $subject = $data['content'];
    //����ȥ��һ�������ַ���.����������ҳ�ĸ�ͼƬ�޷�������ʾ
    $subject = strFilter($subject);
    //�������Ϊ�����.������
    if (strlen($subject) <= 0) {
        $subject = $title_default;
    }


    $publishdate = TIMESTAMP;
    $message = $data['content'];
    $title_total = date('Y-m-d') . $title_default;


    //ֻ��tidû�����á����� tid�ǵ�������ģʽ����������⡣
    if ($tid <= 0 ) {

        $newthread = array(
            'fid' => $fid,
            'posttableid' => 0,
            'readperm' => 0,
            'price' => 0,
            'typeid' => 0,
            'sortid' => 0,
            'author' => $author,
            'authorid' => $uid,
            'subject' => $title_total,
            'dateline' => $publishdate,
            'lastpost' => $publishdate,
            'lastposter' => $author,
            'displayorder' => $displayorder,
            'digest' => 0,
            'special' => 0,
            'attachment' => $attachment,
            'moderated' => 0,
            'status' => 32,
            'isgroup' => 0,
            'replycredit' => 0,
            'closed' => 0
        );
        //��������
        $tid = C::t('forum_thread')->insert($newthread, true);

        //remote 0��ʾ���ء�����ͼƬ��
        C::t('forum_threadimage')->insert(array(
            'tid' => $tid,
            'attachment' => $data['img'],
            'remote' => $remote,
        ), true);

        //���Ϊ�����⡣
        C::t('forum_newthread')->insert(array(
            'tid' => $tid,
            'fid' => $fid,
            'dateline' => $publishdate,
        ));

    }


    useractionlog($uid, 'tid');
    //����ǻ���ģʽ ���ǵ�һ��  ��first =1��
    if ($count == 1) {
        $first = 1; //0�Ƿ�������1��������
    }

    //����post�������ִ��2�������

    $message .= '





    [b][url='.$data['url'].']'.lang('plugin/htt_toutiao','btn_text').'[/url]';


    $pid = insertpost(array(
        'fid' => $fid,
        'tid' => $tid,
        'first' => $first, #�Ƿ���������
        'author' => $author,
        'authorid' => $uid,
        'subject' => $subject,
        'dateline' => $publishdate,
        'message' => $message,
        'useip' => getglobal('clientip'),
        'port' => getglobal('remoteport'),
        'invisible' => $invisible, //�Ƿ�ͨ�����
        'anonymous' => '0', //�Ƿ�����
        'usesig' => '1', //�Ƿ�����ǩ��
        'htmlon' => '0', //�Ƿ�����HTM
        'bbcodeoff' => '0', //�Ƿ�����BBCODE
        'smileyoff' => '-1', //�Ƿ�رձ���
        'parseurloff' => '0', //�Ƿ�����ճ��URL
        'attachment' => $attachment,//����
        'tags' => '0',//�����ֶΣ����ڴ��tag
        'replycredit' => '0',//������û��ּ�¼
        'status' => '0'//����״̬
    ));

    if ($data['img'] != '') {


        //��ȡ�ļ�����
        $filename = substr(strrchr($data['img'], '/'), 1);
        $filesize = filesize('data/attachment/forum/' . $data['img']);
        $arr = getimagesize('data/attachment/forum/' . $data['img']);
        $width = $arr[0];

        $xx = (string)$tid;
        //�����ֱ�Ĺ����� ����id ���� -1
        $tableid = dintval($xx{strlen($xx)-1});

        $aid = C::t('forum_attachment')->insert(array(
            'aid' => null,
            'tid' => $tid,
            'pid' => $pid,
            'uid' => $uid,
            'tableid' => $tableid,
            'downloads' => '0'
        ), true);
        //�ֱ���߼���ģ���Զ���ɵġ�
        C::t("forum_attachment_n")->insert('tid:' . $tid, array(
            'aid' => $aid,
            'tid' => $tid,
            'pid' => $pid,
            'uid' => $uid,
            'dateline' => $publishdate,
            'filename' => $filename,
            'filesize' => $filesize,
            'attachment' => $data['img'], //"201606/01/152319brne4s0xeirr48ii.png"
            'remote' => $remote,
            'description' => 'qsbk',
            'readperm' => 0,
            'price' => 0,
            'isimage' => 1,
            'width' => $width,
            'thumb' => 0,
            'picid' => 0,
        ));
        //��Ҫ����post����Ӹ������ݡ�
        if(empty($data['title'])){
            $data['title'] = cutstr($data[$context],36,'');
        }

        C::t('forum_post')->update(0, $pid, array('message' => '[b][size=5]'.$data['title'].'[/size][/b]'."


        [attach]" . $aid . "[/attach]


        ".$message));

    }else{
        //�������Ϊ����Ӧ����ˡ�
        C::t('forum_post')->update(0, $pid, array('message' => '[b][size=5]'.$data['title'].'[/size][/b]


        '.$message));

    }


    if ($tid != $lasttid) {

        $subject = str_replace("\t", ' ', $subject);
        $lastpost = "$tid\t" . $subject . "\t" . TIMESTAMP . "\t$author";
        C::t('forum_forum')->update($fid, array('lastpost' => $lastpost));


        C::t('forum_forum')->update_forum_counter($fid, 1, 1, 1);

        //�������̳������Ҫ�����ϼ���
        if ($forum['type'] == 'sub') {
            C::t('forum_forum')->update($forum['fup'], array('lastpost' => $lastpost));
        }

    }



    //ɳ������
    //tid ���ͱ仯�ٲ��롣
    if ($tid != $lasttid) {

        C::t('forum_sofa')->insert(array('tid' => $tid, 'fid' => $forum['fid']));
    }


    $count = $count + 1;

    $lasttid = $tid; //��¼֮ǰ��tid��

//    echo $tid;
//
//    exit();

}
?>
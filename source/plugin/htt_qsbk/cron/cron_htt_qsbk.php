<?php
/**
 *    [qsbkwwf(qsbkwwf.cron_qsbkwwf)] (C)2016-2099 Powered by ���ĸ�.
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
$fidstr = $var['htt_qsbk']['fids'];
$uidstr = $var['htt_qsbk']['uids'];
$groupstr = $var['htt_qsbk']['groups']; //�û���
$threads = $var['htt_qsbk']['threads'];
$charset_num = $var['htt_qsbk']['charset'];  // 1utf-8 2gbk
$caiji_model = $var['htt_qsbk']['caiji_model']; //1���� 2��ʾ��ͼ 3ͼ��
$check = $var['htt_qsbk']['check'];  //1����� 2��ˡ�
$title_length = $var['htt_qsbk']['title_length']; //���ⳤ��
$title_default = $var['htt_qsbk']['title_default']; //Ĭ�ϱ���
$post_model = $var['htt_qsbk']['post_model']; //����ģʽ

//����ɼ�����Ϊ0.��ִ�к���Ĳ��������ɼ���
if ($threads == 0) {
    return;
}
$fids = array_filter(unserialize($fidstr));
if (is_null($fids) || empty($fids)) {
    return;
}
$uids = array_filter(explode(',', $uidstr));
$groups = array_filter(unserialize($groupstr));
$members_bygroup = C::t('common_member')->fetch_all_by_groupid($groups);//����Ļ�Ա����


if (empty($uidstr)) {
    $uids = array();
    foreach ($members_bygroup as $item) {
        $uids[] = $item['uid'];
    }
}


if (empty($uids)) {
    return;
}
//����Ƿ񳬳���Χ��
if ($threads < 0 || $threads > 20) {
    return;
}

//����Դ��
$urls = array(
    'text_hot' => "http://www.qiushibaike.com/text/",
    'text_new' => "http://www.qiushibaike.com/textnew/",
    'pic_hot' => "http://www.qiushibaike.com/imgrank/",
    'pic_new' => "http://www.qiushibaike.com/pic/",
    '24h' => "http://www.qiushibaike.com/hot/",
    '8h' => "http://www.qiushibaike.com/",
);

switch ($caiji_model) {
    case 1:
        $urls = array(
            'text_hot' => "http://www.qiushibaike.com/text/",
            'text_new' => "http://www.qiushibaike.com/textnew/",
        );
        break;
    case 2:
        $urls = array(
            'pic_hot' => "http://www.qiushibaike.com/imgrank/",
            'pic_new' => "http://www.qiushibaike.com/pic/",
        );
        break;
    default:
        $urls = $urls;
        break;
}

#�����������ȡһ��
$rand_keys = array_rand($urls, 1);
$url = $urls[$rand_keys];
$html = curl_qsbk($url);

$imgpath = set_home('data/attachment/forum'); //���ص���ȫ·����

//��������
include_once DISCUZ_ROOT . './source/plugin/htt_qsbk/include/phpQuery/phpQuery.php';
phpquery::newDocumentHTML($html, 'utf-8');
#��ȡ�����б��������Ǹ���
$articles = pq(".article");
$count = 1; //����


$tid = 0; //����Ĭ��ֵ

$lasttid = 0; //��һ�ε�tid.

$fist = 0;


foreach ($articles as $article) {

    //����������������˳�ѭ����
    if ($count > $threads) {
        break;
    }


    $data = array();
    $data['content'] = pq($article)->find(".content")->text();
    $data['img'] = pq($article)->find(".thumb a img")->attr('src');

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

    //�޸���˲�����-2
    if ($check == '2') {
        $invisible = -2; //��Ҫ���
        $displayorder = -2; //��ʾ˳��
    } else {
        $invisible = 0; //������ˡ�
        $displayorder = 0; //��Ҫ��˵�����Ϊ-2
    }

//���ѡ��һ�������û���
    $fid_key = array_rand($fids, 1);
    $uid_key = array_rand($uids, 1);

    $fid = $fids[$fid_key];
    $uid = $uids[$uid_key];

    $forum = C::t('forum_forum')->fetch_info_by_fid($fid);


    $userinfo = C::t('common_member')->fetch($uid);


    $author = $userinfo['username'];

    //ת�����롣�������utf-8������Ҫת����Ĭ��Ϊutf-8
    if ($charset_num != 1) {
        $data['content'] = iconv("UTF-8", "gbk", $data['content']);
    }
    //���Ʊ���ĳ��ȡ��������ݡ�ͬʱ���ݳ��ȳ�����󳤶ȡ����ȡ��
    if (!empty($data['content']) && strlen($data['content']) > $title_length) {

        $subject = cutstr($data['content'], $title_length, '');
    } else {
        $subject = $data['content'];
    }
    //����ȥ��һ�������ַ���.����������ҳ�ĸ�ͼƬ�޷�������ʾ
    $subject = strFilter($subject);
    //�������Ϊ�����.������
    if (strlen($subject) <= 0) {
        $subject = $title_default;
    }


    $publishdate = TIMESTAMP;


    $message = $data['content'];

    //����ǻ���ģʽ���������Ҫ��������
    if ($post_model == 1) {
        $title_total = $subject;
    } else {

        $title_total = date('Y-m-d') . $title_default;
    }


    //ֻ��tidû�����á����� tid�ǵ�������ģʽ����������⡣
    if ($tid <= 0 || $post_model == 1) {

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
    if ($post_model == 2 && $count == 1) {
        $first = 1; //0�Ƿ�������1��������
    }

    //����post�������ִ��2�������
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
        C::t('forum_post')->update(0, $pid, array('message' => $message . "[attach]" . $aid . "[/attach]"));

    }
    if ($check == '2') {

        updatemoderate('tid', $tid);
        C::t('forum_forum')->update_forum_counter($fid, 0, 0, 1);


        //������˱�
        if ($tid != $lasttid) {

            C::t('common_moderate')->insert('tid', array(
                'id' => $tid,
                'status' => '0',
                'dateline' => $publishdate,
            ));

            //֪ͨ��ˡ�
            manage_addnotify('verifythread');
        }


        return;
    } else {


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


    }
    //ɳ������
    //tid ���ͱ仯�ٲ��롣
    if ($tid != $lasttid) {

        C::t('forum_sofa')->insert(array('tid' => $tid, 'fid' => $forum['fid']));
    }


    $count = $count + 1;

    $lasttid = $tid; //��¼֮ǰ��tid��

}
?>
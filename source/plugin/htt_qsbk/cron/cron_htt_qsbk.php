<?php
/**
 *    [qsbkwwf(qsbkwwf.cron_qsbkwwf)] (C)2016-2099 Powered by 吴文付.
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
 * 过滤特殊字符
 * */
function strFilter($str)
{
    $str = str_replace('`', '', $str);
    $str = str_replace('·', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('！', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('￥', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('……', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('（', '', $str);
    $str = str_replace('）', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('——', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('【', '', $str);
    $str = str_replace('】', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('；', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('：', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('“', '', $str);
    $str = str_replace('”', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('，', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('《', '', $str);
    $str = str_replace('》', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('。', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('、', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('？', '', $str);
    return trim($str);
}

function curl_qsbk($url)
{
    $curl = curl_init(); //开启curl
    $header[] = "User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0";
    $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
    $header[] = "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Upgrade-Insecure-Requests:1";
    $header[] = "Connection: keep-alive";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
    $html = curl_exec($curl); //执行curl操作
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
$groupstr = $var['htt_qsbk']['groups']; //用户组
$threads = $var['htt_qsbk']['threads'];
$charset_num = $var['htt_qsbk']['charset'];  // 1utf-8 2gbk
$caiji_model = $var['htt_qsbk']['caiji_model']; //1纯文 2表示纯图 3图文
$check = $var['htt_qsbk']['check'];  //1不审核 2审核。
$title_length = $var['htt_qsbk']['title_length']; //标题长度
$title_default = $var['htt_qsbk']['title_default']; //默认标题
$post_model = $var['htt_qsbk']['post_model']; //发帖模式

//如果采集数量为0.则不执行后面的操作。不采集。
if ($threads == 0) {
    return;
}
$fids = array_filter(unserialize($fidstr));
if (is_null($fids) || empty($fids)) {
    return;
}
$uids = array_filter(explode(',', $uidstr));
$groups = array_filter(unserialize($groupstr));
$members_bygroup = C::t('common_member')->fetch_all_by_groupid($groups);//该组的会员资料


if (empty($uidstr)) {
    $uids = array();
    foreach ($members_bygroup as $item) {
        $uids[] = $item['uid'];
    }
}


if (empty($uids)) {
    return;
}
//检查是否超出范围。
if ($threads < 0 || $threads > 20) {
    return;
}

//数据源。
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

#从数组中随机取一个
$rand_keys = array_rand($urls, 1);
$url = $urls[$rand_keys];
$html = curl_qsbk($url);

$imgpath = set_home('data/attachment/forum'); //返回的是全路径。

//解析数据
include_once DISCUZ_ROOT . './source/plugin/htt_qsbk/include/phpQuery/phpQuery.php';
phpquery::newDocumentHTML($html, 'utf-8');
#获取段子列表。最外面那个。
$articles = pq(".article");
$count = 1; //计数


$tid = 0; //设置默认值

$lasttid = 0; //上一次的tid.

$fist = 0;


foreach ($articles as $article) {

    //如果超过数量。则退出循环。
    if ($count > $threads) {
        break;
    }


    $data = array();
    $data['content'] = pq($article)->find(".content")->text();
    $data['img'] = pq($article)->find(".thumb a img")->attr('src');

    $remote = 0;
    //图片存在。
    if (!empty($data['img'])) {
        //图片目录存在则下载。
        $dir1 = date('Ym');
        $dir2 = date('d');

        !is_dir('data/attachment/forum/' . $dir1) && mkdir($dir . '/' . $dir1, 0777);
        !is_dir('data/attachment/forum/' . $dir1 . '/' . $dir2) && mkdir($dir . '/' . $dir1 . '/' . $dir2, 0777);


        $img_name = TIMESTAMP . uniqid() . '.png';
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 30 //超时时间，单位为秒
            )
        ));

        @file_put_contents('data/attachment/forum/' . $dir1 . '/' . $dir2 . '/' . $img_name, file_get_contents($data['img'], 0, $context));
        $data['img'] = $dir1 . '/' . $dir2 . '/' . $img_name;
        $remote = 0; //主题图片表中 1表示远程图片。0表示本地图片。
        $attachment = 2; //附件,0无附件 1普通附件 2有图片附件

    } else {
        $attachment = 0; //附件,0无附件 1普通附件 2有图片附件
    }

    //修改审核参数。-2
    if ($check == '2') {
        $invisible = -2; //需要审核
        $displayorder = -2; //显示顺序
    } else {
        $invisible = 0; //无须审核。
        $displayorder = 0; //需要审核的帖子为-2
    }

//随机选择一个版块和用户。
    $fid_key = array_rand($fids, 1);
    $uid_key = array_rand($uids, 1);

    $fid = $fids[$fid_key];
    $uid = $uids[$uid_key];

    $forum = C::t('forum_forum')->fetch_info_by_fid($fid);


    $userinfo = C::t('common_member')->fetch($uid);


    $author = $userinfo['username'];

    //转换编码。如果不是utf-8。则需要转换。默认为utf-8
    if ($charset_num != 1) {
        $data['content'] = iconv("UTF-8", "gbk", $data['content']);
    }
    //控制标题的长度。存在内容。同时内容长度超过最大长度。则截取。
    if (!empty($data['content']) && strlen($data['content']) > $title_length) {

        $subject = cutstr($data['content'], $title_length, '');
    } else {
        $subject = $data['content'];
    }
    //标题去掉一次特殊字符串.否则引发首页四格图片无法正常显示
    $subject = strFilter($subject);
    //避免标题为空情况.则设置
    if (strlen($subject) <= 0) {
        $subject = $title_default;
    }


    $publishdate = TIMESTAMP;


    $message = $data['content'];

    //如果是汇总模式。则标题需要单独处理。
    if ($post_model == 1) {
        $title_total = $subject;
    } else {

        $title_total = date('Y-m-d') . $title_default;
    }


    //只有tid没有设置。或者 tid是单独发帖模式。则插入主题。
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
        //插入主题
        $tid = C::t('forum_thread')->insert($newthread, true);

        //remote 0表示本地。主题图片表。
        C::t('forum_threadimage')->insert(array(
            'tid' => $tid,
            'attachment' => $data['img'],
            'remote' => $remote,
        ), true);

        //标记为新主题。
        C::t('forum_newthread')->insert(array(
            'tid' => $tid,
            'fid' => $fid,
            'dateline' => $publishdate,
        ));

    }


    useractionlog($uid, 'tid');
    //如果是汇总模式 又是第一条  则first =1。
    if ($post_model == 2 && $count == 1) {
        $first = 1; //0是非首贴，1是首贴。
    }

    //插入post表。这里会执行2个表操作
    $pid = insertpost(array(
        'fid' => $fid,
        'tid' => $tid,
        'first' => $first, #是否是首贴。
        'author' => $author,
        'authorid' => $uid,
        'subject' => $subject,
        'dateline' => $publishdate,
        'message' => $message,
        'useip' => getglobal('clientip'),
        'port' => getglobal('remoteport'),
        'invisible' => $invisible, //是否通过审核
        'anonymous' => '0', //是否匿名
        'usesig' => '1', //是否启用签名
        'htmlon' => '0', //是否允许HTM
        'bbcodeoff' => '0', //是否允许BBCODE
        'smileyoff' => '-1', //是否关闭表情
        'parseurloff' => '0', //是否允许粘贴URL
        'attachment' => $attachment,//附件
        'tags' => '0',//新增字段，用于存放tag
        'replycredit' => '0',//回帖获得积分记录
        'status' => '0'//帖子状态
    ));

    if ($data['img'] != '') {


        //获取文件名。
        $filename = substr(strrchr($data['img'], '/'), 1);
        $filesize = filesize('data/attachment/forum/' . $data['img']);
        $arr = getimagesize('data/attachment/forum/' . $data['img']);
        $width = $arr[0];

        $xx = (string)$tid;
        //附件分表的规则是 主题id 长度 -1
        $tableid = dintval($xx{strlen($xx)-1});

        $aid = C::t('forum_attachment')->insert(array(
            'aid' => null,
            'tid' => $tid,
            'pid' => $pid,
            'uid' => $uid,
            'tableid' => $tableid,
            'downloads' => '0'
        ), true);
        //分表的逻辑是模型自动完成的。
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
        //需要更新post。添加附件内容。
        C::t('forum_post')->update(0, $pid, array('message' => $message . "[attach]" . $aid . "[/attach]"));

    }
    if ($check == '2') {

        updatemoderate('tid', $tid);
        C::t('forum_forum')->update_forum_counter($fid, 0, 0, 1);


        //插入审核表。
        if ($tid != $lasttid) {

            C::t('common_moderate')->insert('tid', array(
                'id' => $tid,
                'status' => '0',
                'dateline' => $publishdate,
            ));

            //通知审核。
            manage_addnotify('verifythread');
        }


        return;
    } else {


        if ($tid != $lasttid) {

            $subject = str_replace("\t", ' ', $subject);
            $lastpost = "$tid\t" . $subject . "\t" . TIMESTAMP . "\t$author";
            C::t('forum_forum')->update($fid, array('lastpost' => $lastpost));


            C::t('forum_forum')->update_forum_counter($fid, 1, 1, 1);

            //如果子论坛，还需要更新上级。
            if ($forum['type'] == 'sub') {
                C::t('forum_forum')->update($forum['fup'], array('lastpost' => $lastpost));
            }

        }


    }
    //沙发数据
    //tid 发送变化再插入。
    if ($tid != $lasttid) {

        C::t('forum_sofa')->insert(array('tid' => $tid, 'fid' => $forum['fid']));
    }


    $count = $count + 1;

    $lasttid = $tid; //记录之前的tid。

}
?>
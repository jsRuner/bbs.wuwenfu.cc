<?php
/**
 *    [htttoutiao(htt_toutiao.cron_toutiao)] (C)2016-2099 Powered by 吴文付.
 *    Version: 1.0
 *    Date: 2016-4-2 16:24
 *    Warning: Don't delete this comment
 *
 *
 *    cronname: info_cronname
 *    week: -1
 *    day:-1
 *    hour:5
 *    minute:10,20,30,40,50
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
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 10*1000);  //5秒等待

    $html = curl_exec($curl); //执行curl操作
    curl_close($curl);

    if (empty($html)) {
        return '';
    }
    return $html;
}

/*
 * -- 读写缓存文件 --
 * value为空时读取name字段缓存
 * @param name String
 * @param value String
 */
 function wwf_cache($name = '',$value = ''){
    define('PLUGIN_IDENTIFIE', 'htt_toutiao');
    require_once libfile('function/cache');
    $cache = array();
    $cache_file = DISCUZ_ROOT.'data/sysdata/cache_' . PLUGIN_IDENTIFIE . '.php';
    if(file_exists($cache_file)){
        $cache = require($cache_file);
    }
    if($value != '' && $name != ''){    // 写入缓存
        $cache[$name] = $value;
        $cache_text = "\r\nreturn ".arrayeval($cache).";\r\n";
        writetocache(PLUGIN_IDENTIFIE,$cache_text);
        unset($cache);
        unset($cache_text);
        unset($cache_file);
    }else{  // 读取缓存
        unset($cache_file);
        return isset($cache[$name]) ? $cache[$name] : false;
    }
}


//先从缓存读取。如果采集过，则不执行了。
//如果没有。则读取数据库，如果采集过，则写入缓存

$toutiao_ed = wwf_cache('toutiao');
$tid_ed = wwf_cache('toutiao_tid');
$pid_ed = wwf_cache('toutiao_pid');
//不为空。同时时间是今天的。则表示成功了 ，不执行了。
if ( $toutiao_ed == date('Y-m-d') && intval($tid_ed) > 0 && intval($pid_ed) > 0 ) {
   return;
}


//处理昨天的情况。如果是昨天的，则消除其他的参数
if ( $toutiao_ed == date('Y-m-d',strtotime("-1 day"))) {
    wwf_cache('toutiao_tid',0);
    wwf_cache('toutiao_pid',0);
}


//如果是今天。存在tid，不存在pid,则需要进行删除操作。
if (  intval($tid_ed) > 0 && intval($pid_ed) <= 0 ) {
        C::t('forum_thread')->delete($tid_ed);
}



loadcache('plugin');
$var = $_G['cache']['plugin'];
$fidstr = $var['htt_toutiao']['fids'];

$fids = unserialize($fidstr);

$charset_num = $var['htt_toutiao']['charset'];  // 1utf-8 2gbk
$title_default = $var['htt_toutiao']['title_default']; //默认标题
$key = $var['htt_toutiao']['key']; //采集的新闻关键字




$param =array(
    'q'=>$key,
    'range'=>'all',
    'c'=>'news',
    'sort'=>'time',
);

$url = 'http://search.sina.com.cn/?'.http_build_query($param);
$html = curl_qsbk($url);




//解析数据
include_once DISCUZ_ROOT . './source/plugin/htt_toutiao/include/phpQuery/phpQuery.php';
phpquery::newDocumentHTML($html,'gbk');


#获取段子列表。最外面那个。
$articles = pq(".box-result");





$imgpath = set_home('data/attachment/forum'); //返回的是全路径。




$count = 1; //计数


$tid = 0; //设置默认值

$lasttid = 0; //上一次的tid.

$first = 0;




foreach ($articles as $article) {



    $data = array();
    $data['url'] = pq($article)->find('h2 a')->attr('href'); //增加标题.


    $data['title'] = pq($article)->find('h2 a')->text(); //增加标题.



    $data['content'] = pq($article)->find('p')->text(); //摘要

    $data['img'] = pq($article)->find('img')->attr('src'); //摘要


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




    $invisible = 0; //无须审核。
    $displayorder = 0; //需要审核的帖子为-2

    //随机选择一个版块和用户。
    $fid_key = array_rand($fids, 1);

    $fid = $fids[$fid_key];

    $uid = 1; //默认就是管理员

    $forum = C::t('forum_forum')->fetch_info_by_fid($fid);


    $userinfo = C::t('common_member')->fetch($uid);


    $author = $userinfo['username'];

    //转换编码。如果不是utf-8。则需要转换。默认为utf-8
    if ($charset_num != 1) {
        $data['content'] = iconv("UTF-8", "gbk", $data['content']);
        $data['title'] = iconv("UTF-8", "gbk", $data['title']);
    }
    $subject = $data['content'];
    //标题去掉一次特殊字符串.否则引发首页四格图片无法正常显示
    $subject = strFilter($subject);
    //避免标题为空情况.则设置
    if (strlen($subject) <= 0) {
        $subject = $title_default;
    }


    $publishdate = TIMESTAMP;
    $message = $data['content'];
    $title_total = date('Y-m-d') . $title_default;


    //只有tid没有设置。或者 tid是单独发帖模式。则插入主题。
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
        //插入主题
        $tid = C::t('forum_thread')->insert($newthread, true);

        //记录id
        wwf_cache('toutiao_tid',$tid);

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
    if ($count == 1) {
        $first = 1; //0是非首贴，1是首贴。
    }

    //插入post表。这里会执行2个表操作

    $message .= '





    [b][url='.$data['url'].']'.lang('plugin/htt_toutiao','btn_text').'[/url]';


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

    wwf_cache('toutiao_pid',$pid);

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
        if(empty($data['title'])){
            $data['title'] = cutstr($data[$context],36,'');
        }

        C::t('forum_post')->update(0, $pid, array('message' => '[b][size=5]'.$data['title'].'[/size][/b]'."


        [attach]" . $aid . "[/attach]


        ".$message));

    }else{
        //如果附件为空则应当如此。
        C::t('forum_post')->update(0, $pid, array('message' => '[b][size=5]'.$data['title'].'[/size][/b]


        '.$message));

    }


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



    //沙发数据
    //tid 发送变化再插入。
    if ($tid != $lasttid) {

        C::t('forum_sofa')->insert(array('tid' => $tid, 'fid' => $forum['fid']));
    }


    $count = $count + 1;

    $lasttid = $tid; //记录之前的tid。
}
wwf_cache('toutiao',date('Y-m-d'));
?>
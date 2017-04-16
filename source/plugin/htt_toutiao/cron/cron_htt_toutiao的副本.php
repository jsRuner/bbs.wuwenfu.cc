<?php
/**
 *    [htt_toutiao(.cron_htt_toutiao)] (C)2016-2099 Powered by 吴文付.
 *    Version: 1.0
 *    Date: 2016-4-2 16:24
 *    Warning: Don't delete this comment
 *
 *
 *    cronname: cron_htt_toutiao
 *    week: -1
 *    day:-1
 *    hour:-1
 *    minute:00,05,10,15,20,25,30,35,40,45,50,55
 */


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
        return '';
    }
    return $html;
}


loadcache('plugin');
$var = $_G['cache']['plugin'];
$fidstr = $var['htt_toutiao']['fids'];
$uidstr = $var['htt_toutiao']['uids'];
$groupstr = $var['htt_toutiao']['groups']; //用户组
$threads = $var['htt_toutiao']['threads']; //采集数量。
$times = $var['htt_toutiao']['times']; //采集时间间隔。

// $times = 5; //修改为5分钟。方便测试。

$charset_num = $var['htt_toutiao']['charset'];  // 1utf-8 2gbk
$caiji_model = $var['htt_toutiao']['caiji_model']; //1纯文 2表示纯图 3图文
$check = $var['htt_toutiao']['check'];  //1不审核 2审核。
$title_default = $var['htt_toutiao']['title_default']; //默认标题
$post_model = $var['htt_toutiao']['post_model']; //发帖模式


$key = $var['htt_toutiao']['key']; //采集的关键字





//查出num=0的记录。取最新的。判断一下时间。
//如果存在。则取删除对应的帖子。再对比下时间。符合要求。则执行采集。

$log_last = array();
$query = DB::query("SELECT * FROM  ".DB::table("htt_toutiao_log")." order by `stime` desc  ");

while($item = DB::fetch($query)) {
    $log_last = $item;
    break;
}



if (!empty($log_last) && $log_last['num'] == 0 ) {
    # code...
    //执行删除逻辑。
    //删除主题
    C::t('forum_thread')->delete($log_last['ids']);
    //删除日志记录。
    C::t('#htt_toutiao#htt_toutiao_log')->delete($log_last['id']);
}
// file_put_contents('data/1.txt',$log_last['stime']."\r\n",FILE_APPEND);
// file_put_contents('data/1.txt',date('Y-m-d H:i:s')."\r\n",FILE_APPEND);
// file_put_contents('data/1.txt',$times."\r\n",FILE_APPEND);
//时间未达到。则放弃执行。
if ( !empty($log_last) && $log_last['stime']+ intval($times) * 60  > time() ) {
    # code...
    // file_put_contents('data/1.txt',$log_last['stime'].'\r\n时间间隔未到',FILE_APPEND);
    
    // file_put_contents('data/1.txt',"--------时间未到------------\r\n",FILE_APPEND);
    return;
}
// file_put_contents('data/1.txt',"--------时间满足------------\r\n",FILE_APPEND);


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

//2016年12月17日修改为新浪头条。
$param = array(
    'q'=>$key,
    'range'=>'all',
    'c'=>'news',
    'sort'=>'time',
    );
$url = 'http://search.sina.com.cn/?'.http_build_query($param);

$html = curl_qsbk($url);

$imgpath = set_home('data/attachment/forum'); //返回的是全路径。

//解析数据
include_once DISCUZ_ROOT . './source/plugin/htt_toutiao/include/phpQuery/phpQuery.php';
phpquery::newDocumentHTML($html, 'utf-8');
#获取段子列表。最外面那个。
$articles = pq(".box-result");
$count = 1; //计数


$tid = 0; //设置默认值

$lasttid = 0; //上一次的tid.

$first = 0;


$datas = array();
$ids = '';




$log_data = array(
    'raw_content'=>$html,
    'content'=>'',
    'num'=>0,
    'ids'=>'',
    'stime'=>TIMESTAMP,
    );

$log_id = C::t('#htt_toutiao#htt_toutiao_log')->insert($log_data,true);



foreach ($articles as $article) {



    //如果超过数量。则退出循环。
    if ($count > $threads) {
        break;
    }

    $data = array();

    $data['title'] = pq($article)->find("h2 a")->text();
    $data['url'] = pq($article)->find("h2 a")->attr('href');

    $data['content'] = pq($article)->find("p")->text();
    $data['img'] = pq($article)->find("img")->attr('src');

    // var_dump($data);

    $remote = 0;
    //图片存在。
    if (!empty($data['img'])) {
        //图片目录存在则下载。
        $dir1 = date('Ym');
        $dir2 = date('d');
        $dir = 'data/attachment/forum';

        !is_dir('data/attachment') && mkdir('data/attachment', 0777);
        !is_dir('data/attachment/forum') && mkdir('data/attachment/forum', 0777);

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
    // if ($_G['charset'] == 'gbk') {
    $data['content'] = iconv("UTF-8", "gbk", $data['content']);
    // }
    $publishdate = TIMESTAMP;
    $message = $data['content'];

    //先设置标题。取内容的第一逗号内容。如果为空。则取第二个。
    
    //2016年12月17日 标题固定。例如 2016年12月17日 新闻速递
    
    if ($post_model == 1) {
        $title_total = date('Y-m-d H:i') . $title_default.'('.$count.')';
    } else {
        $title_total = date('Y-m-d H:i') . $title_default;
    }
    $subject = $title_total;



    //只有tid没有设置。或者 tid是单独发帖模式。则插入主题。
    if ($tid<=0 || $post_model == 1) {
        $newthread = array(
            'fid' => $fid,
            'posttableid' => 0,
            'readperm' => 0,
            'price' => 0,
            'typeid' => 0,
            'sortid' => 0,
            'author' => $author,
            'authorid' => $uid,
            'subject' => $subject,
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
        /*
        C::t('forum_threadimage')->insert(array(
            'tid' => $tid,
            'attachment' => $data['img'],
            'remote' => $remote,
        ), true);*/

        //标记为新主题。
        C::t('forum_newthread')->insert(array(
            'tid' => $tid,
            'fid' => $fid,
            'dateline' => $publishdate,
        ));
    }

    //记录ids。作为删除的标记。
    C::t('#htt_toutiao#htt_toutiao_log')->update($log_id,array('ids'=>$tid));

    useractionlog($uid, 'tid');

    //如果是汇总模式 又是第一条  则first =1。
    if ($post_model == 2 && $count == 1) {
        $first = 1; //0是非首贴，1是首贴。
    }else{
        $first = 0;
        $subject = '';
    }

    //修改为新闻速递。添加上链接。点击查看。
    $message.= '









    [b][url='.$data['url'].']'.lang('plugin/htt_toutiao','btn_read_all').'[/url]';

    //插入post表。这里会执行2个表操作
    $pid = insertpost(array(
        'fid' => $fid,
        'tid' => $tid,
        'first' => $first, #是否是首贴。
        'author' => $author,
        'authorid' => $uid,
        'subject' => '',
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
        C::t('forum_post')->update(0, $pid, array('message' => '[b][size=5]'.$data['title'].'[/size][/b]'. "




            [attach]" . $aid . "[/attach]







            ".$message));
    }else{

        C::t('forum_post')->update(0, $pid, array('message' => '[b][size=5]'.$data['title'].'[/size][/b]




            '.$message));
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
    $datas[] = $data; //累计数据。
    $ids = $ids.$tid.',';   
}

// echo 11;

// exit();

$log_data = array(
    'raw_content'=>$html,
    'content'=>json_encode($datas),
    'num'=>count($datas),
    'ids'=>trim($ids,','),
    );

C::t('#htt_toutiao#htt_toutiao_log')->update($log_id,$log_data);




?>
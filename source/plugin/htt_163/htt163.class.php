<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_htt_163 {
    protected static $postReportAction = array('post_newthread_succeed', 'post_edit_succeed', 'post_reply_succeed',
        'post_newthread_mod_succeed', 'post_newthread_mod_succeed', 'post_reply_mod_succeed',
        'edit_reply_mod_succeed', 'edit_newthread_mod_succeed');
    //TODO - Insert your code here
    protected static $cloudAppService;
    protected static $securityService;
    protected static $securityStatus;
    public function __construct() {
        self::$cloudAppService = Cloud::loadClass('Service_App');
        self::$securityStatus = self::$cloudAppService->getCloudAppStatus('security');
        self::$securityService = Cloud::loadClass('Service_Security');
    }

}

class plugin_htt_163_forum extends plugin_htt_163{
    public function post_security(){
        return true;
    }


    function post_middle(){
        include_once template('htt_163:index');
        return $music_html;
//        return '<a>11111111</a>';
    }

    function post_btn_extra(){
        include_once template('htt_163:index_js');
        return $music_js_html;
    }


    public function post_report_message($param) {
        global $_G, $extra, $redirecturl;
        file_put_contents("c://1.txt",json_encode($param));
        $info = $param['param'][2];
        //读取cookie，如果有数据。则关联。并销毁。
//        file_put_contents("c://1cookie.txt",getcookie('music_163'));
        if($music_str = getcookie('music_163')){
            $music = explode(':',$music_str);
            $insetdata = array(
                'tid'=>$info['tid'],
                'music_id'=>intval($music['0']),
                'music_p'=>intval($music['1']),
                'dateline'=>time()
            );
            C::t('#htt_163#music')->insert($insetdata);
            dsetcookie('music_163','');

        }


    }

    public function viewthread_posttop(){
        //获取当前的帖子id。查询是否有music
        global $_G,$postlist,$_GET;
        $tid = $_GET['tid'];
        $rs = C::t('#htt_163#music')->fetch_all_by_tid($tid);
        //存在并位于头部。
        if($rs && $rs['music_p'] == 1){
            include_once template('htt_163:music');
            return array($viewthread_music_html);

        }
        return array();
    }

    public function viewthread_postbottom(){
        global $_G,$postlist,$_GET;
        $tid = $_GET['tid'];
        $rs = C::t('#htt_163#music')->fetch_all_by_tid($tid);
        //存在并位于头部。
        if($rs && $rs['music_p'] == 2){
            include_once template('htt_163:music');
            return array($viewthread_music_html);

        }
        return array();
    }


}



?>
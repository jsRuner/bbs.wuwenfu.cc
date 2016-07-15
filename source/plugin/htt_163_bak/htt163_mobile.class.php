<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class mobileplugin_htt_163 {
    protected  $is_open;
    protected $width;
    protected $height;

    public function __construct() {
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $this->is_open =  $var['htt_163']['is_open'];
        $this->width =  $var['htt_163']['width'];
        $this->height =  $var['htt_163']['height'];


    }

}

class mobileplugin_htt_163_forum extends mobileplugin_htt_163{




    public function viewthread_posttop_mobile(){
        if($this->is_open ==2){
            return array();
        }

        $width = $this->width;
        $height = $this->height;

        //获取当前的帖子id。查询是否有music
        global $_G,$postlist,$_GET;
        $tid = intval($_GET['tid']);
        $rs = C::t('#htt_163#music')->fetch_all_by_tid($tid);
        //存在并位于头部。
        if($rs && $rs['music_p'] == 1){
            include_once template('htt_163:music');
            return array($viewthread_music_html);

        }
        return array();
    }

    public function viewthread_postbottom_mobile(){
        if($this->is_open ==2){
            return array();
        }
        $width = $this->width;
        $height = $this->height;
        global $_G,$postlist,$_GET;
        $tid = intval($_GET['tid']);

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
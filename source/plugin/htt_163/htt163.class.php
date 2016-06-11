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

    protected  $is_open;
    protected $width;
    protected $height;


    public function __construct() {
        self::$cloudAppService = Cloud::loadClass('Service_App');
        self::$securityStatus = self::$cloudAppService->getCloudAppStatus('security');
        self::$securityService = Cloud::loadClass('Service_Security');

        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $this->is_open =  $var['htt_163']['is_open'];
        $this->width =  $var['htt_163']['width'];
        $this->height =  $var['htt_163']['height'];


    }

}

class plugin_htt_163_forum extends plugin_htt_163{
    public function post_security(){
        return true;
    }


    function post_middle(){
        if($this->is_open ==2){
            return '';
        }
        //�������tid����ȥ����Ƿ������ݡ�
        $tid = intval($_GET['tid']);

        if($tid > 0){
            //������ڡ���ȥ������
            $info = C::t('#htt_163#music')->fetch_all_by_tid($tid);
        }

        include_once template('htt_163:index');
        return $music_html;
//        return '<a>11111111</a>';
    }

    function post_btn_extra(){
        if($this->is_open ==2){
            return '';
        }
        include_once template('htt_163:index_js');
        return $music_js_html;
    }


    public function post_report_message($param) {
        if($this->is_open ==2){
            return ;
        }
        global $_G, $extra, $redirecturl;
//        file_put_contents("c://22.txt",json_encode($param));
        $info = $param['param'][2];
        //��ȡcookie����������ݡ�������������١�
//        file_put_contents("c://1cookie.txt",getcookie('music_163'));
        if($music_str = getcookie('music_163')){

            $music_str = addslashes($music_str);

            //$music_str = del_music,���ʾ����ղ�����
            //��ִ�в�ѯ���������������¡�
            if($music_str == 'del_music'){
                C::t('#htt_163#music')->delete($info['tid']);
                dsetcookie('music_163','');
                return;

            }

            $music = explode(':',$music_str);

            $rs = C::t('#htt_163#music')->count_by_tid($info['tid']);
            if($rs > 0){
                //��������и��²�����
                $update_data = array(
                    'music_id'=>intval($music['0']),
                    'music_p'=>intval($music['1']),
                    'dateline'=>time()
                );
                C::t('#htt_163#music')->update($info['tid'],$update_data);
                dsetcookie('music_163','');

            }else{

                $insetdata = array(
                    'tid'=>$info['tid'],
                    'music_id'=>intval($music['0']),
                    'music_p'=>intval($music['1']),
                    'dateline'=>time()
                );
                C::t('#htt_163#music')->insert($insetdata);

            }


            dsetcookie('music_163','');

        }


    }




    public function viewthread_posttop(){
        if($this->is_open ==2){
            return array();
        }

        $width = $this->width;
        $height = $this->height;

        //��ȡ��ǰ������id����ѯ�Ƿ���music
        global $_G,$postlist,$_GET;
        $tid = $_GET['tid'];
        $rs = C::t('#htt_163#music')->fetch_all_by_tid($tid);
        //���ڲ�λ��ͷ����
        if($rs && $rs['music_p'] == 1){
            include_once template('htt_163:music');
            return array($viewthread_music_html);

        }
        return array();
    }

    public function viewthread_postbottom(){
        if($this->is_open ==2){
            return array();
        }
        $width = $this->width;
        $height = $this->height;
        global $_G,$postlist,$_GET;
        $tid = $_GET['tid'];
        $rs = C::t('#htt_163#music')->fetch_all_by_tid($tid);
        //���ڲ�λ��ͷ����
        if($rs && $rs['music_p'] == 2){
            include_once template('htt_163:music');
            return array($viewthread_music_html);

        }
        return array();
    }


}



?>
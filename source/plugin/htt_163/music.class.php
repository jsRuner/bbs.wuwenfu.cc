<?php

if(!defined('IN_DISCUZ')) {

	exit('Access Denied');

}

class plugin_htt_163 {

	function _music_bbcode2html(&$message, $strpos = false) {

		if(!empty($message)) {

			if(strpos($message, '[/163]') !== FALSE) {

//				$message = preg_replace("/\[163\](.+?)\[\/163\]/is", '$this->parsemusic("\\1")', $message);
                $message = preg_replace_callback("/\[163\](.+?)\[\/163\]/is",array($this,'parsemusic'),$message);
			}

		}

		return $message;

	}


	function parsemusic($match) {
        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $is_open =  $var['htt_163']['is_open'];
        $width =  $var['htt_163']['width'];
        $height =  $var['htt_163']['height'];

        $songurl = $match[1];
        //判断是否为合法的url http://music.163.com/#/song?id=287879
        if(!preg_match('/^http:\/\/music.163.com\/#\/song\?id=(\d)+$/is',$songurl)){

            return '';

        }
//        return $songurl;

        //解析url为id
        $p = strpos($songurl,'id=');


        $music = substr($songurl,$p+3);
		return '<iframe frameborder="no" border="0" marginwidth="0" marginheight="0" width='.$width.' height='.$height.' src="http://music.163.com/outchain/player?type=2&id='.$music.'&auto=1&height=66"></iframe>';

	}

    //根据编码不同，加载不同的编码js文件
	function _music_script() {
        global $_G;
        if($_G['charset'] == 'gbk'){
            return '
			<script type="text/javascript" src="source/plugin/htt_163/js/htt_music_gbk.js?'.VERHASH.'" charset="gb2312"></script>
			';
        }else{
            return '
			<script type="text/javascript" src="source/plugin/htt_163/js/htt_music_utf8.js?'.VERHASH.'" charset="utf-8"></script>
			';
        }

	}
}

class plugin_htt_163_forum extends plugin_htt_163 {

	function viewthread_music_output() {

		global $_G, $postlist, $post;

		if(!empty($_GET['viewpid']) && is_array($post) && $post) {

			$post['message'] = $this->_music_bbcode2html($post['message']);
	
	}
 elseif(empty($_GET['viewpid']) && is_array($postlist) && $postlist) {

			foreach($postlist as $pid=>$thispost) {

				$postlist[$pid]['message'] = $this->_music_bbcode2html($thispost['message']);

			}

		}

		return '';

	}


	function post_bottom_output() {

        global $_G;
        loadcache('plugin');
        $var = $_G['cache']['plugin'];
        $is_open =  $var['htt_163']['is_open'];
        $width =  $var['htt_163']['width'];
        $height =  $var['htt_163']['height'];
        if($is_open != 1){
            return '';
        }

		return $this->_music_script();
	}
}
?>
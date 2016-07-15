<?php
/**
 *	[虾米帖内音乐播放器] (C)2013 Powered by 90 Discuz!
 *	Version: 1.0.0免费版
 *	Notice:请尊重开发者，严禁盗版，侵权必究！
 *	Website:http://discuz.xhuaian.com
 *	Date: 2013-03-10 22:00
 */
if(!defined('IN_DISCUZ')) {

	exit('Access Denied');

}

class plugin_xhuaian_xiami {

	function _music_bbcode2html(&$message, $strpos = false) {

		if(!empty($message)) {

			if(strpos($message, '[/163]') !== FALSE) {

				$message = preg_replace("/\[163\](.+?)\[\/163\]/ies", '$this->parsemusic("\\1")', $message);

			}

		}

		return $message;

	}


	function parsemusic($songurl) {
        //解析url为id
        $p = strpos($songurl,'id=');

        $height = "300";
        $width = "300";
        $music = substr($songurl,$p+3);
		return '<iframe frameborder="no" border="0" marginwidth="0" marginheight="0" width='.$width.' height='.$height.' src="http://music.163.com/outchain/player?type=2&id='.$music.'&auto=1&height=66"></iframe>';

	}


	function _music_script() {

		return '
			<script type="text/javascript" src="source/plugin/xhuaian_xiami/js/xhuaian_xiami.js?'.VERHASH.'" charset="utf-8"></script>
			';
	}
}

class plugin_xhuaian_xiami_forum extends plugin_xhuaian_xiami {

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

		return $this->_music_script();
	}
}
?>
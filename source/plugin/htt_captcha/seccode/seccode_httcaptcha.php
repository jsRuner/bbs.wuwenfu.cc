<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: seccode_cloudcaptcha.php 34041 2013-09-24 09:48:15Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class seccode_httcaptcha {

	var $version = '1.0';
	var $name = '北岸验证码';
	var $description = '';
	var $copyright = '<a href="http://wuwenfu.cn" target="_blank">北岸的云 .</a>';

	//这个只是代表额外的验证。可以不存在。
	function check($value, $idhash, $seccheck, $fromjs, $modid) {
		global $_G;
		if(!isset($_G['cookie']['seccode'.$idhash])) {
			return false;
		}
		list($checkvalue, $checktime, $checkidhash, $checkformhash) = explode("\t", authcode($_G['cookie']['seccode'.$idhash], 'DECODE', $_G['config']['security']['authkey']));
		return $checkvalue == strtoupper($value) && TIMESTAMP < $checktime && $checkidhash == $idhash && FORMHASH == $checkformhash;
	}

	function make($idhash, $modid) {
		global $_G;

		
		$rand = random(10);
		$src = 'plugin.php?id=htt_captcha:get&rand='.$rand.'&modid='.$modid.'&idhash='.$idhash;
		$tips = lang('core', 'seccode_image_tips');
		echo '<span id="seccode_js'.$idhash.'"></span><script type="text/javascript" src="http://discuz.gtimg.cn/cloud/scripts/captcha.js?version='.CLOUDCAPTCHA_VER.'"></script>'.
		    '<script type="text/javascript" reload="1">'.
		    'var refresh = $(\'seccode_'.$idhash.'\').innerHTML ? 1 : 0;'.
		    'var cloudCaptchaTimer = setInterval(function(){if(typeof cloudCaptcha != "undefined"){'.
		    'clearInterval(cloudCaptchaTimer);'.
		    'cloudCaptcha.run("'.$src.'&refresh=" + refresh, "'.$idhash.'", "'.$tips.'");}}, 50);</script>';
	}

	function image($idhash, $modid) {
		global $_G;
		
		$rand = random(10);
		return $_G['siteurl'].'plugin.php?id=htt_captcha:get&rand='.$rand.'&modid='.$modid.'&idhash='.$idhash;
	}

}

?>
<?php

class seccode_yychk {
	var $version;
	var $name;
	var $copyright;
	var $setting = array();
	public function __construct(){
		$this->version = $this->plang('version');
		$this->name = $this->plang('name');
		$this->copyright = $this->plang('copyright');
	}
	function check($value, $idhash) {
		global $_G;
		if(!isset($_G['cookie']['seccode'.$idhash])) {
			return false;
		}
		list($checkvalue, $checktime, $checkidhash, $checkformhash) = explode("\t", authcode($_G['cookie']['seccode'.$idhash], 'DECODE', $_G['config']['security']['authkey']));
		return $checkvalue == strtoupper($value) && TIMESTAMP < $checktime && $checkidhash == $idhash && FORMHASH == $checkformhash;
	}
	function make($idhash) {
		global $_G;
		$cvar = $_G['cache']['plugin']['yy_seccode'];
		$width = $cvar['width'];
		$height = $cvar['height'];
		echo '<img onclick="updateseccode(\''.$idhash.'\')" width="150" height="80" src="plugin.php?id=yy_seccode:create&idhash='.$idhash.'&rand='.rand(0,10000).'" class="vm" alt="" />';
	}
	public function plang($str){
		return lang('plugin/yy_seccode',$str);
	}
}

?>
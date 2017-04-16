<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_yy_seccode {
	function global_footer(){
		global $_G;
		$t=$_G['setting']['seccodedata']['type'];
		$etype = explode(':', $t);
		if($etype[1]=='yychk'||$t=='yychk')
			$_G['setting']['seccodedata']['type']=1;
	}
}

?>
<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

exit();
$refererhost = parse_url($_SERVER['HTTP_REFERER']);
$refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';
if($refererhost['host'] != $_SERVER['HTTP_HOST'] || !extension_loaded('gd')){
	exit('Access Denied');
}
$idhash = $_GET['idhash'];
$cvar = $_G['cache']['plugin']['yy_seccode'];
require_once libfile('yy_seccode','plugin/yy_seccode');
$sec = new yy_seccode();
$seccode = $sec->getsec();
dsetcookie('seccode'.$idhash, authcode(strtoupper($seccode)."\t".(TIMESTAMP + $cvar['ltime'])."\t".$idhash."\t".FORMHASH, 'ENCODE', $_G['config']['security']['authkey']), 0, 1, true);
if(!$_G['setting']['nocacheheaders']) {
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
}
$sec->font = DISCUZ_ROOT.'./source/plugin/yy_seccode/font/'.$cvar['font'];
$sec->intfline = $cvar['intfline']; 
$sec->intfpix = $cvar['intfpix']; 
$sec->scatter = $cvar['scatter'];
$sec->angle = $cvar['angle'];
$sec->display();

?>
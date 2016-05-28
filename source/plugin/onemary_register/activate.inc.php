<?php

/*==========================================================
 *	Plugin Name   ：onemary_job
 *	Plugin author : RaoLibao
 *	Updated date  : 2013-12-3
 *	Phone number  : (086)18650336706, (0591)83701411
 *	Other contact : QQ1609883787, Email 1609883787@qq.com
 *	AUTHOR URL    : http://www.onemary.com
 *	This is NOT a freeware, use is subject to license terms
=============================================================*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('NOROBOT', TRUE);

$gallery = $_GET['gallery'] ? $_GET['gallery'] : 0;
if(empty($gallery)){
	exit('Access Denied');
}

if($_GET['uid'] && $_GET['activateid']) {
	$member = getuserbyuid($_GET['uid']);
	if($member && $member['groupid'] == 8) {
		$member = array_merge(C::t('common_member_field_forum')->fetch($member['uid']), $member);
	} else {
		showmessage('activate_illegal', 'index.php');
	}
	list($dateline, $operation, $idstring) = explode("\t", $member['authstr']);
	if($operation == 2 && $idstring == $_GET['activateid']) {
		$ret = C::t('#onemary_register#onemary_register_field')->fetch_all($gallery);
		$newgroup['groupid'] = $ret[$gallery]['new_groupid'];
		
		C::t('common_member')->update($member['uid'], array('groupid' => $newgroup['groupid'], 'emailstatus' => '1'));
		C::t('common_member_field_forum')->update($member['uid'], array('authstr' => ''));
		showmessage('activate_succeed', 'index.php', array('username' => $member['username']));
	} else {
		showmessage('activate_illegal', 'index.php');
	}
}

?>
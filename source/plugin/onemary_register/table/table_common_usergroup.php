<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_common_usergroup.php 31679 2012-09-21 02:09:05Z monkey $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_usergroup extends discuz_table
{
	public function __construct() {

		$this->_table = 'common_usergroup';
		$this->_pk    = 'groupid';

		parent::__construct();
	}
	public function usergroup()
	{
		return DB::fetch_all('SELECT * FROM %t WHERE type != %s OR groupid = %d',array($this->_table,'member',10));
	}
}

?>
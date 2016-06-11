<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_myrepeats.php 31512 2012-09-04 07:11:08Z monkey $
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_music extends discuz_table
{
	public function __construct() {

		$this->_table = 'htt163_music';
		$this->_pk    = '';

		parent::__construct();
	}


	public function fetch_all_by_tid($tid) {
        $rs = DB::fetch_all("SELECT * FROM %t WHERE tid=%s", array($this->_table, $tid));
        return $rs[0];
	}



}

?>
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

class table_category extends discuz_table
{
	public function __construct() {

		$this->_table = 'httzhanhao_category';
		$this->_pk    = '';

		parent::__construct();
	}

	public function fetch_all_by_cid($cid) {
		return DB::fetch_all("SELECT * FROM %t WHERE id=%d", array($this->_table, $cid));
	}


    public function fetch_all() {
        return DB::fetch_all("SELECT * FROM %t WHERE 1 ORDER BY sort",array($this->_table));
    }

	public function delete_by_id($id) {
		DB::query("DELETE FROM %t WHERE id=%d ", array($this->_table, $id));
	}


}

?>
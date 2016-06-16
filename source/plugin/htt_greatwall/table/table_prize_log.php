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

class table_prize_log extends discuz_table
{
	public function __construct() {

		$this->_table = 'greatwall_prize_log';
		$this->_pk    = 'id';

		parent::__construct();
	}

	public function fetch_by_pid($pid) {
		$rs = DB::fetch_all("SELECT * FROM %t WHERE id=%d", array($this->_table, $pid));
        return $rs[0];

	}

	public function fetch_all_by_username($username) {
		return DB::fetch_all("SELECT * FROM %t WHERE username=%s", array($this->_table, $username));
	}

	public function fetch_all() {
		$rs =  DB::fetch_all("SELECT * FROM %t WHERE 1", array($this->_table));
        $new_rs = array();
        foreach($rs as $k=>$v){
            $new_rs[$v['id']] = $v;
        }
        return $new_rs;

	}


	public function delete_by_id($id) {
		DB::query("DELETE FROM %t WHERE id=%d ", array($this->_table, $id));
	}

	public function update_status_by_id($id, $value) {
		DB::query("UPDATE %t SET status=%s WHERE id=%d", array($this->_table, $value, $id));
	}

	public function count_by_search($condition) {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE 1 %i", array($this->_table, $condition));
	}

	public function fetch_all_by_search($condition, $start, $ppp) {
		$rs =  DB::fetch_all("SELECT * FROM %t WHERE 1 %i ORDER BY  created DESC LIMIT %d, %d", array($this->_table, $condition, $start, $ppp));

        return $rs;
	}



}

?>
<?php
/**
 *	Version: v1.0
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_auto extends discuz_table {

	public function __construct() {
		$this->_table = 'kylefu_spider_movie_auto';
		$this->_pk = 'id';

		parent::__construct();
	}
	public function count_all($where = "") {
		return (int) DB::result_first("SELECT count(*) FROM %t %i", array($this->_table, $where));
	}
	public function fetch_all($where = "") {
		return DB::fetch_all('SELECT * FROM %t %i', array($this->_table, $where));
	}
	public function fetch_url($url) {
		return DB::fetch_first('SELECT * FROM %t WHERE `url`=%s', array($this->_table, $url));
	}
	public function fetch_spider($url) {
		return DB::fetch_first('SELECT * FROM %t WHERE `spider`=%s', array($this->_table, $url));
	}
	public function update_by_id($data,$id) {
		return DB::update($this->_table,$data,"id=$id");
	}
	public function insert($data) {
		return DB::insert($this->_table,$data,true);
	}
	public function delete_by_id($id) {
		return DB::delete($this->_table,"id=$id",true);	
	}
}
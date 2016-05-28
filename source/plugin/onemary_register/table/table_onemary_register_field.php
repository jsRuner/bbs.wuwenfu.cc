<?php/*========================================================== *	Plugin Name   ：onemary_job *	Plugin author : RaoLibao *	Updated date  : 2013-12-3 *	Phone number  : (086)18650336706, (0591)83701411 *	Other contact : QQ1609883787, Email 1609883787@qq.com *	AUTHOR URL    : http://www.onemary.com *	This is NOT a freeware, use is subject to license terms=============================================================*/class table_onemary_register_field extends discuz_table{	public function __construct() {		$this->_table = 'onemary_register_field';		$this->_pk    = 'gallery';		parent::__construct();	}	
public function get_open($i)
{
	return DB::fetch_all('SELECT gallery,gallery_name FROM %t WHERE open=%d',array($this->table,$i));
}
}	
?>
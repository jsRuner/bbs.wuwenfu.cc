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

class table_htt_baoman_log extends discuz_table
{
	public function __construct() {

		$this->_table = 'htt_baoman_log';
		$this->_pk    = 'id';

		parent::__construct();
	}

	


}

?>
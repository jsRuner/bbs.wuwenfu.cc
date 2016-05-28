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



require_once 'source/plugin/onemary_register/class/register.class.php';

require_once 'source/plugin/onemary_register/function/register.func.php';



require_once libfile('function/member');

include_once libfile('function/profile');

define('NOROBOT', TRUE);

$ctl_obj = new register_ctl();

$ctl_obj->setting = $_G['setting'];

$ctl_obj->gallery = !empty($_GET['gallery']) ? ((in_array($_GET['gallery'],array(1,2,3,4,5))) ? $_GET['gallery'] : 1) : 1;

$ctl_obj->template = 'onemary_register:register';


$ctl_obj->on_register();



?>




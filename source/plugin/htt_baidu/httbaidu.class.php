<?php
/**
 *	[百度贴吧(htt_baidu.{modulename})] (C)2016-2099 Powered by 北岸的云.
 *	Version: 1.0
 *	Date: 2016-4-22 21:48
 *	string forumdisplay_forumaction
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_htt_baidu {

	const DEBUG = 0;
    protected static $postReportAction = array('post_newthread_succeed', 'post_edit_succeed', 'post_reply_succeed',
        'post_newthread_mod_succeed', 'post_newthread_mod_succeed', 'post_reply_mod_succeed',
        'edit_reply_mod_succeed', 'edit_newthread_mod_succeed');
	//TODO - Insert your code here
    protected static $cloudAppService;
    protected static $securityService;
    protected static $securityStatus;

    public function __construct() {
        self::$cloudAppService = Cloud::loadClass('Service_App');
        self::$securityStatus = self::$cloudAppService->getCloudAppStatus('security');
        self::$securityService = Cloud::loadClass('Service_Security');
    }

	//TODO - Insert your code here
	function global_header()
	{
		global $_G;
		return $fid = $_G['fid'];
		// return 2226662;
		# code...
	}

}


//脚本嵌入点类
class plugin_htt_baidu_forum extends plugin_htt_baidu {

	 public function post_security(){
        return true;
    }

	function forumdisplay_forumaction_output() {
		#读取数据。判断是否关注了该版块。
		global $_G;

		$uid = $_G['uid'];
		$fid = $_G['fid'];

		$guanzhuinfo = '';

		$query = DB::query("SELECT * FROM  `pre_httbaidu` WHERE  `fid`=$fid and `uid`=$uid LIMIT 0 , 30");
		while($item = DB::fetch($query)) {
			// var_dump($item);
			$guanzhuinfo = $item;
		}
		include_once template('htt_baidu:button');

		if (empty($guanzhuinfo)){

        	return $button_no_html;
		}else{
        	return $button_yes_html;
			
		}
	}

	//发帖后触发 {"param":["post_reply_succeed","forum.php?mod=viewthread&tid=96&pid=99&page=1&extra=#pid99",{"fid":"2","tid":"96","pid":99,"from":null,"sechash":""},[],0]}
	 public function post_report_message($param) {
        global $_G, $extra, $redirecturl;
        #获取uid fid 增加一次积分。该积分值读取插件的设置
       	$uid = $_G['uid'];
		$fid = $_G['fid'];
		$query = DB::query("update `pre_httbaidu` set `credit`=`credit`+10 WHERE  `fid`=$fid and `uid`=$uid");
    }
    //帖子左侧显示版块的积分
    public function viewthread_sidebottom()
    {	
    	global $_G;

		$uid = $_G['uid'];
		$fid = $_G['fid'];

		$guanzhuinfo = '';
    	$query = DB::query("SELECT * FROM  `pre_httbaidu` WHERE  `fid`=$fid and `uid`=$uid");
		while($item = DB::fetch($query)) {
			// var_dump($item);
			$guanzhuinfo = $item;
		}
		if(empty($guanzhuinfo)){
			$credit = 0;
		}else{

			$credit = $guanzhuinfo['credit'];
		}
		// $credit = 10;
    	include_once template('htt_baidu:side');
    	$return =array($side_html);

    	
    	return $return;
    }


}





?>
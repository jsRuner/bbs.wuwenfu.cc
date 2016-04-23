<?php
/**
 *	[�ٶ�����(htt_baidu.{modulename})] (C)2016-2099 Powered by ��������.
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


//�ű�Ƕ�����
class plugin_htt_baidu_forum extends plugin_htt_baidu {

	 public function post_security(){
        return true;
    }

	function forumdisplay_forumaction_output() {
		#��ȡ���ݡ��ж��Ƿ��ע�˸ð�顣
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

	//�����󴥷� {"param":["post_reply_succeed","forum.php?mod=viewthread&tid=96&pid=99&page=1&extra=#pid99",{"fid":"2","tid":"96","pid":99,"from":null,"sechash":""},[],0]}
	 public function post_report_message($param) {
        global $_G, $extra, $redirecturl;
        #��ȡuid fid ����һ�λ��֡��û���ֵ��ȡ���������
       	$uid = $_G['uid'];
		$fid = $_G['fid'];
		$query = DB::query("update `pre_httbaidu` set `credit`=`credit`+10 WHERE  `fid`=$fid and `uid`=$uid");
    }
    //���������ʾ���Ļ���.
    //���ӻ��洦����ȡ�������ݡ����ں��ٶ�ȡ���ݿ�ġ�
    public function viewthread_sidebottom()
    {	
    	global $_G;
    	loadcache('plugin');
		$var = $_G['cache']['plugin'];

		// var_dump($var);
		$cache_time =  $var['htt_baidu']['cache_time'];

		$cache_file = DISCUZ_ROOT.'./data/sysdata/cache_htt_baidu_contents.php';

		//��������ˡ�
		if(($_G['timestamp'] - @filemtime($cache_file)) > $cache_time*60) {
    		# code...
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

			//����ȼ����ơ���ѯ���еȼ���Ȼ�������ȼ���todo����Ҫ����
			$level_list = array();
			$touxian = "��������"; #Ĭ�� �������š����û���õĻ�
	    	$query = DB::query("SELECT * FROM  `pre_httbaidu_level` WHERE  1 order by `floor` asc  ");
			while($item = DB::fetch($query)) {
				//�Ͼ��Ƿ�������ޣ�С�����ޡ������-1����ֻ�Ƚ�����
				if($item['ceil'] == -1){
					if ($credit>=$item['floor']) {
						$touxian = $item['leveltitle'];
					}

				}else{

					if ($credit>=$item['floor'] && $credit <$item['ceil']) {
						$touxian = $item['leveltitle'];
					}
				}
			}
		//�������档
		$info['credit'] = $credit;
		$info['touxian'] = urlencode($touxian);
		//д�뻺�����Ҫ��''
		$cacheArray .= "\$contents='".json_encode($info)."';\n";
		require_once libfile('function/cache');
		writetocache('htt_baidu_contents', $cacheArray); 
    	
    	}else{
		   //����Դӻ����ļ������.
    		//��ȡ���������
    		include_once DISCUZ_ROOT.'./data/sysdata/cache_htt_baidu_contents.php';
			$htt_baidu_cache= json_decode($contents,true);
    		$touxian = $htt_baidu_cache['touxian'];
    		$touxian = urldecode($touxian);
    		$credit = $htt_baidu_cache['credit'];

    	}

    	include_once template('htt_baidu:side');
    	$return =array($side_html);

    	
    	return $return;
    }


}





?>
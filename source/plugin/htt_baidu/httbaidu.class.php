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

}


//�ű�Ƕ�����
class plugin_htt_baidu_forum extends plugin_htt_baidu {

	 public function post_security(){
        return true;
    }

	function forumdisplay_forumaction_output() {
		#��ȡ���ݡ��ж��Ƿ��ע�˸ð�顣
		global $_G;

		loadcache('plugin');
		$var = $_G['cache']['plugin'];
		$cache_time =  $var['htt_baidu']['cache_time'];
		$credit_title =  $var['htt_baidu']['credit_title'];
		$level_title =  $var['htt_baidu']['level_title'];

		$show_style =  $var['htt_baidu']['show_style']; // 1���� 2�ȼ� 3���߶�
		$del_credit =  $var['htt_baidu']['del_credit']; //ɾ������ 1���� 2ɾ��

		$show_num =  $var['htt_baidu']['show_num']; //��ע�� 1��ʾ 2����ʾ

		$uid = $_G['uid'];
		$fid = $_G['fid'];

		$guanzhuinfo = '';

		$query = DB::query("SELECT * FROM  `pre_httbaidu` WHERE  `fid`=$fid and `uid`=$uid LIMIT 0 , 30");
		while($item = DB::fetch($query)) {
			// var_dump($item);
			$guanzhuinfo = $item;
		}

		if(empty($guanzhuinfo)){
			$guanzhustr = lang("plugin/htt_baidu",'no_guanzhu');
		}else{
			$guanzhustr = lang("plugin/htt_baidu",'yes_guanzhu');

		}

		$yesguanzhu = lang("plugin/htt_baidu",'yes_guanzhu');

		#ͳ�ư��Ĺ�ע������
		$query = DB::query("SELECT count(`id`) as `guanzhu_num` FROM  `pre_httbaidu` WHERE  `fid`=$fid");
		while($item = DB::fetch($query)) {
			// var_dump($item);
			$guanzhu_num = $item['guanzhu_num'];
		}

		#��Ҫ��ȡ�Ƿ��ע�ͣ���ǰ���Ĺ�ע������
		include_once template('htt_baidu:button');
		return $button_yes_html;

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
    //����Ҫ����ķǵ�¼�û���uid,���Ƿ�������˵İ��ȼ�����֣��Ǹ�����������
    function viewthread_sidebottom_output()
    {	
    	// global $_G;
    	global $_G,$postlist,$_GET;
    	$tid = $_GET['tid'];

    	loadcache('plugin');
		$var = $_G['cache']['plugin'];
		$cache_time =  $var['htt_baidu']['cache_time'];
		$credit_title =  $var['htt_baidu']['credit_title'];
		$level_title =  $var['htt_baidu']['level_title'];

		$show_style =  $var['htt_baidu']['show_style']; // 1���� 2�ȼ� 3���߶�
		$del_credit =  $var['htt_baidu']['del_credit']; //ɾ������ 1���� 2ɾ��

		$show_num =  $var['htt_baidu']['show_num']; //��ע�� 1��ʾ 2����ʾ

		//���û�й�ע������ʾ�������ע�������������ʾ���ֻ��ߵȼ�����2��




		$cache_file = DISCUZ_ROOT.'./data/sysdata/cache_htt_baidu_contents_'.$tid.'.php';

		//��������ˡ�
		if(($_G['timestamp'] - @filemtime($cache_file)) > $cache_time*60) {
			foreach ($postlist as $key => $value) {
				$info =array();
				$uid = $value['uid'];
				$fid = $_G['fid'];
				$guanzhuinfo = '';
		    	$query = DB::query("SELECT * FROM  `pre_httbaidu` WHERE  `fid`=$fid and `uid`=$uid");
				while($item = DB::fetch($query)) {
					// var_dump($item);
					$guanzhuinfo = $item;
				}
				if(empty($guanzhuinfo)){
					//˵����û�й�ע��Ĭ��һ����ע����Ϊ1���֡�
					$credit = 0;

				}else{

					$credit = $guanzhuinfo['credit'];
				}

				//����ȼ����ơ���ѯ���еȼ���Ȼ�������ȼ���todo����Ҫ����
				$level_list = array();
				$touxian = ""; #Ĭ��Ϊ��
		    	$query = DB::query("SELECT * FROM  `pre_httbaidu_level` WHERE  1 order by `floor` asc  ");
		    	
		    	$level_num = 0;

				while($item = DB::fetch($query)) {
					$level_num += 1 ;
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
					#���û���ҵ���
					if (empty($touxian)) {
						# code...
						$touxian = lang("plugin/htt_baidu","error_setting_level");	
					}

				}

				if ($level_num == 0) {
					$touxian = lang("plugin/htt_baidu","no_setting_level");
				}



				#û�й�ע����Ϊ���ַ�����
				if ($credit ==0) {
					$side_html = "";
					# code...
				}else{

					#�����ע�ˣ������������ʾ��
					if ($show_style==1) {
						$side_html = '<dl class="pil cl"><dt>'.$credit_title.'</dt><dd><a href="home.php?mod=space&amp;uid=1&amp;do=profile" target="_blank" class="xi2">'.$credit.'</a></dd></dl>';
						# code...
					}elseif($show_style==2){
						$side_html = '<dl class="pil cl">
					    <dt>'.$level_title.'</dt><dd><a href="home.php?mod=space&amp;uid=1&amp;do=profile" target="_blank" class="xi2">'.$touxian.'</a></dd></dl>';

					}else{
		    			$side_html = '<dl class="pil cl"><dt>'.$credit_title.'</dt><dd><a href="home.php?mod=space&amp;uid=1&amp;do=profile" target="_blank" class="xi2">'.$credit.'</a></dd></dl>	<dl class="pil cl">
					    <dt>'.$level_title.'</dt><dd><a href="home.php?mod=space&amp;uid=1&amp;do=profile" target="_blank" class="xi2">'.$touxian.'</a></dd></dl>';
					}

				}

				// $echoq[] = urlencode($side_html);
				// $echoq[$tid][] = $side_html;
				$echoq[] =$side_html;

				$cachestr[] = urlencode($side_html);
			}
		//д�뻺�����Ҫ��''
		// $cacheArray .= "\$contents='".json_encode($echoq)."';\n";
		$cacheArray = "\$contents='".json_encode($cachestr)."';\n";
		require_once libfile('function/cache');
		writetocache('htt_baidu_contents', $cacheArray); 
    	
    	}else{
		   //����Դӻ����ļ������.
    		//��ȡ���������
    		include_once DISCUZ_ROOT.'./data/sysdata/cache_htt_baidu_contents.php';
			
			$contents= json_decode($contents,true);
			// var_dump($contents);
			foreach ($contents as $key => $value) {

				$echoq[] = urldecode($value);
			}
    	}
    	return $echoq;
    }
}





?>
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
	/**
	 * -- 读写缓存文件 --
	 * value为空时读取name字段缓存
	 * @param name String
	 * @param value String
	 */
	 function _cache($name = '',$value = ''){
		define('PLUGIN_IDENTIFIE', 'htt_baidu');
		require_once libfile('function/cache');
		$cache = array();
		$cache_file = DISCUZ_ROOT.'data/sysdata/cache_' . PLUGIN_IDENTIFIE . '.php';
		if(file_exists($cache_file)){
			$cache = require($cache_file);
		}
		if($value != '' && $name != ''){	// 写入缓存
			$cache[$name] = $value;
			$cache_text = "\r\nreturn ".arrayeval($cache).";\r\n";
			writetocache(PLUGIN_IDENTIFIE,$cache_text);
			unset($cache);
			unset($cache_text);
			unset($cache_file);
		}else{	// 读取缓存
			unset($cache_file);
			return isset($cache[$name]) ? $cache[$name] : false;
		}
	}

}


//脚本嵌入点类
class plugin_htt_baidu_forum extends plugin_htt_baidu {

	 public function post_security(){
        return true;
    }

    //关注量增加缓存处理。
	function forumdisplay_forumaction_output() {
		#读取数据。判断是否关注了该版块。
		global $_G;

		loadcache('plugin');
		$var = $_G['cache']['plugin'];
		$cache_time =  $var['htt_baidu']['cache_time'];
		$credit_title =  $var['htt_baidu']['credit_title'];
		$level_title =  $var['htt_baidu']['level_title'];

		$show_style =  $var['htt_baidu']['show_style']; // 1积分 2等级 3两者都
		$del_credit =  $var['htt_baidu']['del_credit']; //删除积分 1保留 2删除

		$show_num =  $var['htt_baidu']['show_num']; //关注量 1显示 2不显示

		$uid = $_G['uid'];
		$fid = $_G['fid'];

		$guanzhuinfo = '';

		$query = DB::query("SELECT * FROM  ".DB::table("httbaidu")." WHERE  `fid`=$fid and `uid`=$uid ");
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


		//添加缓存处理。
		$cache_file = DISCUZ_ROOT.'./data/sysdata/cache_htt_baidu.php';
		//缓存过期了。
		if(($_G['timestamp'] - @filemtime($cache_file)) > $cache_time*60) {

			#统计版块的关注数量。
			$query = DB::query("SELECT count(`id`) as `guanzhu_num` FROM  ".DB::table("httbaidu")." WHERE  `fid`=$fid");
			while($item = DB::fetch($query)) {
				// var_dump($item);
				$guanzhu_num = $item['guanzhu_num'];
			}

			$this->_cache('guanzhu_num_'.$fid,$guanzhu_num);

			

		}else{

			
			$guanzhu_num = $this->_cache('guanzhu_num_'.$fid);

		}

		#需要获取是否关注和，当前版块的关注人数。
		include_once template('htt_baidu:button');
		return $button_yes_html;

	}

	//只要发帖，回帖编辑等都触发。无须区分太清楚。鼓励活跃度
	 public function post_report_message($param) {
        global $_G, $extra, $redirecturl;
        #获取uid fid 增加一次积分。该积分值读取插件的设置
       	$uid = $_G['uid'];
		$fid = $_G['fid'];
		$query = DB::query("update ".DB::table("httbaidu")." set `credit`=`credit`+10 WHERE  `fid`=$fid and `uid`=$uid");
    }
    //帖子左侧显示版块的积分.
    //增加缓存处理。读取缓存数据。过期后再读取数据库的。
    //这里要输出的非登录用户的uid,而是发帖左侧人的版块等级与积分，是个多次输出过程
    function viewthread_sidebottom_output()
    {	
    	// global $_G;
    	global $_G,$postlist,$_GET;
    	$tid = $_GET['tid'];

    	loadcache('plugin');
		$var = $_G['cache']['plugin'];
		$cache_time =  $var['htt_baidu']['cache_time']; #缓存有效期。
		$credit_title =  $var['htt_baidu']['credit_title'];
		$level_title =  $var['htt_baidu']['level_title'];

		$show_style =  $var['htt_baidu']['show_style']; // 1积分 2等级 3两者都
		$del_credit =  $var['htt_baidu']['del_credit']; //删除积分 1保留 2删除

		$show_num =  $var['htt_baidu']['show_num']; //关注量 1显示 2不显示

		//如果没有关注，则不显示。如果关注了则根据配置显示积分或者等级或者2者
		$cache_file = DISCUZ_ROOT.'./data/sysdata/cache_htt_baidu.php';

		//缓存过期了。
		if(($_G['timestamp'] - @filemtime($cache_file)) > $cache_time*60) {
			foreach ($postlist as $key => $value) {
				$info =array();
				$uid = $value['uid'];
				$fid = $_G['fid'];
				$guanzhuinfo = '';
		    	$query = DB::query("SELECT * FROM  ".DB::table("httbaidu")." WHERE  `fid`=$fid and `uid`=$uid");
				while($item = DB::fetch($query)) {
					// var_dump($item);
					$guanzhuinfo = $item;
				}
				if(empty($guanzhuinfo)){
					//说明是没有关注。默认一旦关注，则为1积分。
					$credit = 0;

				}else{

					$credit = $guanzhuinfo['credit'];
				}

				//计算等级名称。查询所有等级。然后计算出等级。todo：需要缓存
				$level_list = array();
				$touxian = ""; #默认为空
		    	$query = DB::query("SELECT * FROM  ".DB::table("httbaidu_level")." WHERE  1 order by `floor` asc  ");
		    	
		    	$level_num = 0;

				while($item = DB::fetch($query)) {
					$level_num += 1 ;
					//毕竟是否大于下限，小于上限。如果是-1。则只比较下限
					if($item['ceil'] == -1){
						if ($credit>=$item['floor']) {
							$touxian = $item['leveltitle'];
						}

					}else{

						if ($credit>=$item['floor'] && $credit <$item['ceil']) {
							$touxian = $item['leveltitle'];
						}
					}
					#如果没有找到。
					if (empty($touxian)) {
						# code...
						$touxian = lang("plugin/htt_baidu","error_setting_level");	
					}

				}

				if ($level_num == 0) {
					$touxian = lang("plugin/htt_baidu","no_setting_level");
				}



				#没有关注。则为空字符串。
				if ($credit ==0) {
					$side_html = "";
					# code...
				}else{

					#如果关注了，则根据配置显示。
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
				$echoq[] =$side_html;

				$cachestr[] = urlencode($side_html);
			}

			$this->_cache('httbaiduinfo_'.$tid,$cachestr);
    	}else{
		   
			$contents = $this->_cache('httbaiduinfo_'.$tid);
			// var_dump($contents);
			foreach ($contents as $key => $value) {

				$echoq[] = urldecode($value);
			}
    	}
    	return $echoq;
    }
}





?>